<?php


namespace Forgge\Kernels;

use Pimple\Container;
use Forgge\ServiceProviders\ExtendsConfigTrait;
use Forgge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide old input dependencies.
 *
 * @codeCoverageIgnore
 */
class KernelsServiceProvider implements ServiceProviderInterface {
	use ExtendsConfigTrait;

	/**
	 * {@inheritDoc}
	 */
	public function register( Container $container ): void {
		$this->extendConfig( $container, 'middleware', [
			'flash' => \Forgge\Flash\FlashMiddleware::class,
			'old_input' => \Forgge\Input\OldInputMiddleware::class,
			'csrf' => \Forgge\Csrf\CsrfMiddleware::class,
			'user.logged_in' => \Forgge\Middleware\UserLoggedInMiddleware::class,
			'user.logged_out' => \Forgge\Middleware\UserLoggedOutMiddleware::class,
			'user.can' => \Forgge\Middleware\UserCanMiddleware::class,
		] );

		$this->extendConfig( $container, 'middleware_groups', [
			'forgge' => [
				'flash',
				'old_input',
			],
			'global' => [],
			'web' => [],
			'ajax' => [],
			'admin' => [],
		] );

		$this->extendConfig( $container, 'middleware_priority', [] );

		$container[ FORGGE_WORDPRESS_HTTP_KERNEL_KEY ] = function ( $c ) {
			$kernel = new HttpKernel(
				$c,
				$c[ FORGGE_APPLICATION_GENERIC_FACTORY_KEY ],
				$c[ FORGGE_HELPERS_HANDLER_FACTORY_KEY ],
				$c[ FORGGE_RESPONSE_SERVICE_KEY ],
				$c[ FORGGE_REQUEST_KEY ],
				$c[ FORGGE_ROUTING_ROUTER_KEY ],
				$c[ FORGGE_VIEW_SERVICE_KEY ],
				$c[ FORGGE_EXCEPTIONS_ERROR_HANDLER_KEY ]
			);

			$kernel->setMiddleware( $c[ FORGGE_CONFIG_KEY ]['middleware'] );
			$kernel->setMiddlewareGroups( $c[ FORGGE_CONFIG_KEY ]['middleware_groups'] );
			$kernel->setMiddlewarePriority( $c[ FORGGE_CONFIG_KEY ]['middleware_priority'] );

			return $kernel;
		};

		$app = $container[ FORGGE_APPLICATION_KEY ];

		$app->alias( 'run', function () use ( $app ) {
			$kernel = $app->resolve( FORGGE_WORDPRESS_HTTP_KERNEL_KEY );
			return call_user_func_array( [$kernel, 'run'], func_get_args() );
		} );
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( Container $container ): void {
		// Nothing to bootstrap.
	}
}
