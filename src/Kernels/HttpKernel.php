<?php


namespace Forgge\Kernels;

use Closure;
use Exception;
use Pimple\Container;
use Psr\Http\Message\ResponseInterface;
use WP_Query;
use Forgge\Application\GenericFactory;
use Forgge\Exceptions\ConfigurationException;
use Forgge\Exceptions\ErrorHandlerInterface;
use Forgge\Helpers\Handler;
use Forgge\Helpers\HandlerFactory;
use Forgge\Middleware\ExecutesMiddlewareTrait;
use Forgge\Middleware\HasMiddlewareDefinitionsTrait;
use Forgge\Middleware\ReadsHandlerMiddlewareTrait;
use Forgge\Requests\RequestInterface;
use Forgge\Responses\ConvertsToResponseTrait;
use Forgge\Responses\ResponseService;
use Forgge\Routing\HasQueryFilterInterface;
use Forgge\Routing\Router;
use Forgge\Routing\SortsMiddlewareTrait;
use Forgge\View\ViewService;

/**
 * Describes how a request is handled.
 */
class HttpKernel implements HttpKernelInterface {
	use HasMiddlewareDefinitionsTrait;
	use SortsMiddlewareTrait;
	use ConvertsToResponseTrait;
	use ReadsHandlerMiddlewareTrait;
	use ExecutesMiddlewareTrait;

	/**
	 * Container.
	 *
	 * @var Container
	 */
	protected ?Container $container = null;

	/**
	 * Injection factory.
	 *
	 * @var GenericFactory
	 */
	protected ?GenericFactory $factory = null;

	/**
	 * Handler factory.
	 *
	 * @var HandlerFactory
	 */
	protected ?HandlerFactory $handler_factory = null;

	/**
	 * Response service.
	 *
	 * @var ResponseService
	 */
	protected ?ResponseService $response_service = null;

	/**
	 * Request.
	 *
	 * @var RequestInterface
	 */
	protected ?RequestInterface $request = null;

	/**
	 * Router.
	 *
	 * @var Router
	 */
	protected ?Router $router = null;

	/**
	 * View Service.
	 *
	 * @var ViewService
	 */
	protected ?ViewService $view_service = null;

	/**
	 * Error handler.
	 *
	 * @var ErrorHandlerInterface
	 */
	protected ?ErrorHandlerInterface $error_handler = null;

	/**
	 * Template WordPress attempted to load.
	 *
	 * @var string
	 */
	protected string $template = '';

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param Container             $container
	 * @param GenericFactory        $factory
	 * @param HandlerFactory        $handler_factory
	 * @param ResponseService       $response_service
	 * @param RequestInterface      $request
	 * @param Router                $router
	 * @param ViewService           $view_service
	 * @param ErrorHandlerInterface $error_handler
	 */
	public function __construct(
		Container $container,
		GenericFactory $factory,
		HandlerFactory $handler_factory,
		ResponseService $response_service,
		RequestInterface $request,
		Router $router,
		ViewService $view_service,
		ErrorHandlerInterface $error_handler
	) {
		$this->container = $container;
		$this->factory = $factory;
		$this->handler_factory = $handler_factory;
		$this->response_service = $response_service;
		$this->request = $request;
		$this->router = $router;
		$this->view_service = $view_service;
		$this->error_handler = $error_handler;
	}

	/**
	 * Get the current response.
	 *
	 * @codeCoverageIgnore
	 * @return ResponseInterface|null
	 */
	protected function getResponse(): ?ResponseInterface {
		return isset( $this->container[ FORGGE_RESPONSE_KEY ] ) ? $this->container[ FORGGE_RESPONSE_KEY ] : null;
	}

	/**
	 * Get a Response Service instance.
	 *
	 * @codeCoverageIgnore
	 * @return ResponseService
	 */
	protected function getResponseService(): ResponseService {
		return $this->response_service;
	}

	/**
	 * Make a middleware class instance.
	 *
	 * @codeCoverageIgnore
	 * @param  string $class
	 * @return object
	 */
	protected function makeMiddleware( string $class ): object {
		return $this->factory->make( $class );
	}

	/**
	 * Execute a handler.
	 *
	 * @param  Handler           $handler
	 * @param  array             $arguments
	 * @return ResponseInterface
	 */
	protected function executeHandler( Handler $handler, array $arguments = [] ): ResponseInterface {
		$response = $handler->execute( ...array_values( $arguments ) );
		$response = $this->toResponse( $response );

		if ( ! $response instanceof ResponseInterface ) {
			throw new ConfigurationException(
				'Response returned by controller is not valid ' .
				'(expected ' . ResponseInterface::class . '; received ' . gettype( $response ) . ').'
			);
		}

		return $response;
	}

	/**
	 * {@inheritDoc}
	 */
	public function run(
		RequestInterface $request,
		array $middleware,
		string|Closure|Handler $handler,
		array $arguments = []
	): ResponseInterface {
		$this->error_handler->register();

		try {
			$handler = $handler instanceof Handler ? $handler : $this->handler_factory->make( $handler );

			$middleware = array_merge( $middleware, $this->getHandlerMiddleware( $handler ) );
			$middleware = $this->expandMiddleware( $middleware );
			$middleware = $this->uniqueMiddleware( $middleware );
			$middleware = $this->sortMiddleware( $middleware );

			$response = $this->executeMiddleware( $middleware, $request, function () use ( $handler, $arguments ) {
				return $this->executeHandler( $handler, $arguments );
			} );
		} catch ( Exception $exception ) {
			$response = $this->error_handler->getResponse( $request, $exception );
		}

		$this->error_handler->unregister();

		return $response;
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle( RequestInterface $request, array $arguments = [] ): ?ResponseInterface {
		$route = $this->router->execute( $request );

		if ( $route === null ) {
			return null;
		}

		$route_arguments = $route->getArguments( $request );

		$request = $request
			->withAttribute( 'route', $route )
			->withAttribute( 'route_arguments', $route_arguments );

		$response = $this->run(
			$request,
			$route->getAttribute( 'middleware', [] ),
			$route->getAttribute( 'handler' ),
			array_merge(
				[$request],
				$arguments,
				$route_arguments
			)
		);

		$this->container[ FORGGE_RESPONSE_KEY ] = $response;

		return $response;
	}

	/**
	 * Respond with the current response.
	 *
	 * @return void
	 */
	public function respond(): void {
		$response = $this->getResponse();

		if ( ! $response instanceof ResponseInterface ) {
			return;
		}

		$this->response_service->respond( $response );
	}

	/**
	 * Output the current view outside of the routing flow.
	 *
	 * @return void
	 */
	public function compose(): void {
		$view = $this->view_service->make( $this->template );

		echo $view->toString();
	}

	/**
	 * {@inheritDoc}
	 * @codeCoverageIgnore
	 */
	public function bootstrap(): void {
		// Web. Use 3100 so it's high enough and has uncommonly used numbers
		// before and after. For example, 1000 is too common and it would have 999 before it
		// which is too common as well.).
		add_action( 'request', [$this, 'filterRequest'], 3100 );
		add_action( 'template_include', [$this, 'filterTemplateInclude'], 3100 );

		// Ajax.
		add_action( 'admin_init', [$this, 'registerAjaxAction'] );

		// Admin.
		add_action( 'admin_init', [$this, 'registerAdminAction'] );

		add_action( 'rest_api_init', [$this, 'registerRest'] );
	}

	public function registerRest() {
		$route = $this->router->execute( $this->request );

		if ( $route === null ) {
			return null;
		}

		$route_arguments = $route->getArguments( $this->request );

		var_dump( $route_arguments );
		exit();
	}

	/**
	 * Filter the main query vars.
	 *
	 * @param  array $query_vars
	 * @return array
	 */
	public function filterRequest( array $query_vars ): array {
		$routes = $this->router->getRoutes();

		foreach ( $routes as $route ) {
			if ( ! $route instanceof HasQueryFilterInterface ) {
				continue;
			}

			/** @var \Forgge\Routing\RouteInterface $route */
			if ( ! $route->isSatisfied( $this->request ) ) {
				continue;
			}

			/** @var \Forgge\Application\Application */
			$app = $this->container[ FORGGE_APPLICATION_KEY ];

			$app->renderConfigExceptions( function () use ( $route, &$query_vars ) {
				$query_vars = $route->applyQueryFilter( $this->request, $query_vars );
			} );
			break;
		}

		return $query_vars;
	}

	/**
	 * Filter the main template file.
	 *
	 * @param  string $template
	 * @return string
	 */
	public function filterTemplateInclude( string $template ): string {
		/** @var WP_Query $wp_query */
		global $wp_query;

		$this->template = $template;

		$response = $this->handle( $this->request, [$template] );

		// A route has matched so we use its response.
		if ( $response instanceof ResponseInterface ) {
			if ( $response->getStatusCode() === 404 ) {
				$wp_query->set_404();
			}

			add_action( 'forgge.kernels.http_kernel.respond', [$this, 'respond'] );

			return FORGGE_DIR . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'view.php';
		}

		// No route has matched, but we still want to compose views.
		$composers = $this->view_service->getComposersForView( $template );

		if ( ! empty( $composers ) ) {
			add_action( 'forgge.kernels.http_kernel.respond', [$this, 'compose'] );

			return FORGGE_DIR . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'view.php';
		}

		return $template;
	}

	/**
	 * Register ajax action to hook into current one.
	 *
	 * @return void
	 */
	public function registerAjaxAction(): void {
		if ( ! wp_doing_ajax() ) {
			return;
		}

		$action = $this->request->body( 'action', $this->request->query( 'action' ) );
		$action = sanitize_text_field( $action );

		add_action( "wp_ajax_{$action}", [$this, 'actionAjax'] );
		add_action( "wp_ajax_nopriv_{$action}", [$this, 'actionAjax'] );
	}

	/**
	 * Act on ajax action.
	 *
	 * @return void
	 */
	public function actionAjax(): void {
		$response = $this->handle( $this->request, [''] );

		if ( ! $response instanceof ResponseInterface ) {
			return;
		}

		$this->response_service->respond( $response );

		wp_die( '', '', ['response' => null] );
	}

	/**
	 * Get page hook.
	 * Slightly modified version of code from wp-admin/admin.php.
	 *
	 * @return string
	 */
	protected function getAdminPageHook(): string {
		global $pagenow, $typenow, $plugin_page;

		$page_hook = '';

		if ( isset( $plugin_page ) ) {
			$the_parent = $pagenow;

			if ( ! empty( $typenow ) ) {
				$the_parent = $pagenow . '?post_type=' . $typenow;
			}

			$page_hook = get_plugin_page_hook( $plugin_page, $the_parent );
		}

		return (string) $page_hook;
	}

	/**
	 * Get admin page hook.
	 * Slightly modified version of code from wp-admin/admin.php.
	 *
	 * @param  string $page_hook
	 * @return string
	 */
	protected function getAdminHook( string $page_hook ): string {
		global $pagenow, $plugin_page;

		if ( ! empty( $page_hook ) ) {
			return $page_hook;
		}

		if ( isset( $plugin_page ) ) {
			return $plugin_page;
		}

		if ( isset( $pagenow ) ) {
			return $pagenow;
		}

		return '';
	}

	/**
	 * Register admin action to hook into current one.
	 *
	 * @return void
	 */
	public function registerAdminAction(): void {
		$page_hook = $this->getAdminPageHook();
		$hook_suffix = $this->getAdminHook( $page_hook );

		add_action( "load-{$hook_suffix}", [$this, 'actionAdminLoad'] );
		add_action( $hook_suffix, [$this, 'actionAdmin'] );
	}

	/**
	 * Act on admin action load.
	 *
	 * @return void
	 */
	public function actionAdminLoad(): void {
		$response = $this->handle( $this->request, [''] );

		if ( ! $response instanceof ResponseInterface ) {
			return;
		}

		if ( ! headers_sent() ) {
			$this->response_service->sendHeaders( $response );
		}
	}

	/**
	 * Act on admin action.
	 *
	 * @return void
	 */
	public function actionAdmin(): void {
		$response = $this->getResponse();

		if ( ! $response instanceof ResponseInterface ) {
			return;
		}

		$this->response_service->sendBody( $response );
	}
}
