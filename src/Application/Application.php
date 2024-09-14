<?php


namespace Forgge\Application;

use Closure;
use Pimple\Container;
use Forgge\Exceptions\ConfigurationException;
use Forgge\Requests\Request;
use Forgge\Support\Arr;

/**
 * The core Forgge component representing an application.
 */
class Application {
	use HasAliasesTrait;
	use LoadsServiceProvidersTrait;
	use HasContainerTrait;

	/**
	 * Flag whether to intercept and render configuration exceptions.
	 *
	 * @varbool
	 */
	protected $render_config_exceptions = true;

	/**
	 * Flag whether the application has been bootstrapped.
	 *
	 * @varbool
	 */
	protected $bootstrapped = false;

	/**
	 * Make a new application instance.
	 *
	 * @codeCoverageIgnore
	 * @return static
	 */
	public static function make() {
		return new static( new Container() );
	}

	/**
	 * Constructor.
	 *
	 * @param Container $container
	 * @parambool   $render_config_exceptions
	 */
	public function __construct( Container $container, $render_config_exceptions = true ) {
		$this->setContainer( $container );
		$this->container()[ FORGGE_APPLICATION_KEY ] = $this;
		$this->render_config_exceptions = $render_config_exceptions;
	}

	/**
	 * Get whether the application has been bootstrapped.
	 *
	 * @returnbool
	 */
	public function isBootstrapped() {
		return $this->bootstrapped;
	}

	/**
	 * Bootstrap the application.
	 *
	 * @param  array   $config
	 * @param bool $run
	 * @return void
	 */
	public function bootstrap( $config = [], $run = true ) {
		if ( $this->isBootstrapped() ) {
			throw new ConfigurationException( static::class . ' already bootstrapped.' );
		}

		$this->bootstrapped = true;

		$container = $this->container();
		$this->loadConfig( $container, $config );
		$this->loadServiceProviders( $container );

		$this->renderConfigExceptions( function () use ( $run ) {
			$this->loadRoutes();

			if ( $run ) {
				$kernel = $this->resolve( FORGGE_WORDPRESS_HTTP_KERNEL_KEY );
				$kernel->bootstrap();
			}
		} );
	}

	/**
	 * Load config into the service container.
	 *
	 * @codeCoverageIgnore
	 * @param  Container $container
	 * @param  array     $config
	 * @return void
	 */
	protected function loadConfig( Container $container, $config ) {
		$container[ FORGGE_CONFIG_KEY ] = $config;
	}

	/**
	 * Load route definition files depending on the current request.
	 *
	 * @codeCoverageIgnore
	 * @return void
	 */
	protected function loadRoutes() {
		if ( wp_doing_ajax() ) {
			$this->loadRoutesGroup( 'ajax' );
			return;
		}

		if ( is_admin() ) {
			$this->loadRoutesGroup( 'admin' );
			return;
		}

		$this->loadRoutesGroup( 'web' );
	}

	/**
	 * Load a route group applying default attributes, if any.
	 *
	 * @codeCoverageIgnore
	 * @param  string $group
	 * @return void
	 */
	protected function loadRoutesGroup( $group ) {
		$config = $this->resolve( FORGGE_CONFIG_KEY );
		$file = Arr::get( $config, 'routes.' . $group . '.definitions', '' );
		$attributes = Arr::get( $config, 'routes.' . $group . '.attributes', [] );

		if ( empty( $file ) ) {
			return;
		}

		$middleware = Arr::get( $attributes, 'middleware', [] );

		if ( ! in_array( $group, $middleware, true ) ) {
			$middleware = array_merge( [$group], $middleware );
		}

		$attributes['middleware'] = $middleware;

		$blueprint = $this->resolve( FORGGE_ROUTING_ROUTE_BLUEPRINT_KEY );
		$blueprint->attributes( $attributes )->group( $file );
	}

	/**
	 * Catch any configuration exceptions and short-circuit to an error page.
	 *
	 * @codeCoverageIgnore
	 * @param  Closure $action
	 * @return void
	 */
	public function renderConfigExceptions( Closure $action ) {
		try {
			$action();
		} catch ( ConfigurationException $exception ) {
			if ( ! $this->render_config_exceptions ) {
				throw $exception;
			}

			$request = Request::fromGlobals();
			$handler = $this->resolve( FORGGE_EXCEPTIONS_CONFIGURATION_ERROR_HANDLER_KEY );

			add_filter( 'forgge.pretty_errors.apply_admin_styles', '__return_false' );

			$response_service = $this->resolve( FORGGE_RESPONSE_SERVICE_KEY );
			$response_service->respond( $handler->getResponse( $request, $exception ) );

			wp_die();
		}
	}
}
