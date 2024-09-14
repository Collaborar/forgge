<?php


namespace Forgge\Middleware;

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
	public function register( $container ) {
		$container[ UserLoggedOutMiddleware::class ] = function ( $c ) {
			return new UserLoggedOutMiddleware( $c[ FORGGE_RESPONSE_SERVICE_KEY ] );
		};

		$container[ UserLoggedInMiddleware::class ] = function ( $c ) {
			return new UserLoggedInMiddleware( $c[ FORGGE_RESPONSE_SERVICE_KEY ] );
		};

		$container[ UserCanMiddleware::class ] = function ( $c ) {
			return new UserCanMiddleware( $c[ FORGGE_RESPONSE_SERVICE_KEY ] );
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		// Nothing to bootstrap.
	}
}
