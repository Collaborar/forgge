<?php


namespace Forgge\Middleware;

use Pimple\Container;
use Forgge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide middleware dependencies.
 *
 * @codeCoverageIgnore
 */
class MiddlewareServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( Container $container ): void {
		$container[ UserLoggedOutMiddleware::class ] = fn ( Container $c ): UserLoggedOutMiddleware =>
			new UserLoggedOutMiddleware( $c[ FORGGE_RESPONSE_SERVICE_KEY ] );

		$container[ UserLoggedInMiddleware::class ] = fn ( Container $c ): UserLoggedInMiddleware =>
			new UserLoggedInMiddleware( $c[ FORGGE_RESPONSE_SERVICE_KEY ] );

		$container[ UserCanMiddleware::class ] = fn ( Container $c ): UserCanMiddleware =>
			new UserCanMiddleware( $c[ FORGGE_RESPONSE_SERVICE_KEY ] );
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( Container $container ): void {
		// Nothing to bootstrap.
	}
}
