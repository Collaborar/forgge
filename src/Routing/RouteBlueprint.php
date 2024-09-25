<?php


namespace Forgge\Routing;

use Closure;
use Forgge\Helpers\HasAttributesTrait;
use Forgge\Routing\Conditions\ConditionInterface;
use Forgge\View\ViewService;

/**
 * Provide a fluent interface for registering routes with the router.
 */
class RouteBlueprint {
	use HasAttributesTrait;

	/**
	 * Router.
	 *
	 * @var Router
	 */
	protected ?Router $router = null;

	/**
	 * View service.
	 *
	 * @var ViewService
	 */
	protected ?ViewService $view_service = null;

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param Router      $router
	 * @param ViewService $view_service
	 */
	public function __construct( Router $router, ViewService $view_service ) {
		$this->router = $router;
		$this->view_service = $view_service;
	}

	/**
	 * Match requests using one of the specified methods.
	 *
	 * @param  string[] $methods
	 * @return static   $this
	 */
	public function methods( array $methods ): RouteBlueprint {
		$methods = $this->router->mergeMethodsAttribute(
			(array) $this->getAttribute( 'methods', [] ),
			(array) $methods
		);

		return $this->attribute( 'methods', $methods );
	}

	/**
	 * Set the condition attribute to a URL.
	 *
	 * @param  string                $url
	 * @param  array<string, string> $where
	 * @return static                $this
	 */
	public function url( string $url, array $where = [] ): RouteBlueprint {
		return $this->where( 'url', $url, $where );
	}

	/**
	 * Set the condition attribute.
	 *
	 * @param  string|array|ConditionInterface|null $condition
	 * @param  mixed                           ,...$arguments
	 * @return static                          $this
	 */
	public function where( string|array|ConditionInterface|null $condition ): RouteBlueprint {
		if ( ! $condition instanceof ConditionInterface ) {
			$condition = func_get_args();
		}

		$condition = $this->router->mergeConditionAttribute(
			$this->getAttribute( 'condition', null ),
			$condition
		);

		return $this->attribute( 'condition', $condition );
	}

	/**
	 * Set the middleware attribute.
	 *
	 * @param  string|string[] $middleware
	 * @return static          $this
	 */
	public function middleware( string|array $middleware ): RouteBlueprint {
		$middleware = $this->router->mergeMiddlewareAttribute(
			(array) $this->getAttribute( 'middleware', [] ),
			(array) $middleware
		);

		return $this->attribute( 'middleware', $middleware );
	}

	/**
	 * Set the namespace attribute.
	 * This should be renamed to namespace for consistency once minimum PHP
	 * version is increased to 7+.
	 *
	 * @param  string $namespace
	 * @return static $this
	 */
	public function setNamespace( string $namespace ): RouteBlueprint {
		$namespace = $this->router->mergeNamespaceAttribute(
			$this->getAttribute( 'namespace', '' ),
			$namespace
		);

		return $this->attribute( 'namespace', $namespace );
	}

	/**
	 * Set the query attribute.
	 *
	 * @param  callable $query
	 * @return static   $this
	 */
	public function query( callable $query ): RouteBlueprint {
		$query = $this->router->mergeQueryAttribute(
			$this->getAttribute( 'query', null ),
			$query
		);

		return $this->attribute( 'query', $query );
	}

	/**
	 * Set the name attribute.
	 *
	 * @param  string $name
	 * @return static $this
	 */
	public function name( string $name ): RouteBlueprint {
		return $this->attribute( 'name', $name );
	}

	/**
	 * Create a route group.
	 *
	 * @param  Closure|string $routes Closure or path to file.
	 * @return void
	 */
	public function group( Closure|string $routes ): void {
		$this->router->group( $this->getAttributes(), $routes );
	}

	/**
	 * Create a route.
	 *
	 * @param  string|Closure $handler
	 * @return void
	 */
	public function handle( Closure|string $handler = '' ): void {
		if ( ! empty( $handler ) ) {
			$this->attribute( 'handler', $handler );
		}

		$route = $this->router->route( $this->getAttributes() );

		$trace = debug_backtrace( DEBUG_BACKTRACE_IGNORE_ARGS, 1 );

		if ( ! empty( $trace ) && ! empty( $trace[0]['file'] ) ) {
			$route->attribute( '__definition', $trace[0]['file'] . ':' . $trace[0]['line'] );
		}

		$this->router->addRoute( $route );
	}

	/**
	 * Handle a request by directly rendering a view.
	 *
	 * @param  string|string[] $views
	 * @return void
	 */
	public function view( string|array $views ): void {
		$this->handle( function () use ( $views ) {
			return $this->view_service->make( $views );
		} );
	}

	/**
	 * Match ALL requests.
	 *
	 * @param  string|Closure $handler
	 * @return void
	 */
	public function all( Closure|string $handler = '' ): void {
		$this->any()->url( '*' )->handle( $handler );
	}

	/**
	 * Match requests with a method of GET or HEAD.
	 *
	 * @return static $this
	 */
	public function get(): RouteBlueprint {
		return $this->methods( ['GET', 'HEAD'] );
	}

	/**
	 * Match requests with a method of POST.
	 *
	 * @return static $this
	 */
	public function post(): RouteBlueprint {
		return $this->methods( ['POST'] );
	}

	/**
	 * Match requests with a method of PUT.
	 *
	 * @return static $this
	 */
	public function put(): RouteBlueprint {
		return $this->methods( ['PUT'] );
	}

	/**
	 * Match requests with a method of PATCH.
	 *
	 * @return static $this
	 */
	public function patch(): RouteBlueprint {
		return $this->methods( ['PATCH'] );
	}

	/**
	 * Match requests with a method of DELETE.
	 *
	 * @return static $this
	 */
	public function delete(): RouteBlueprint {
		return $this->methods( ['DELETE'] );
	}

	/**
	 * Match requests with a method of OPTIONS.
	 *
	 * @return static $this
	 */
	public function options(): RouteBlueprint {
		return $this->methods( ['OPTIONS'] );
	}

	/**
	 * Match requests with any method.
	 *
	 * @return static $this
	 */
	public function any(): RouteBlueprint {
		return $this->methods( ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'] );
	}
}
