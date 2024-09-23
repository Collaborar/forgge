<?php


namespace Forgge\Csrf;

use Pimple\Container;
use Forgge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide CSRF dependencies.
 *
 * @codeCoverageIgnore
 */
class CsrfServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( Container $container ): void {
		$container[ FORGGE_CSRF_KEY ] = fn (): Csrf => new Csrf();

		$container[ CsrfMiddleware::class ] = fn ( Container $c ): CsrfMiddleware =>
			new CsrfMiddleware( $c[ FORGGE_CSRF_KEY ] );

		$app = $container[ FORGGE_APPLICATION_KEY ];
		$app->alias( 'csrf', FORGGE_CSRF_KEY );
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( Container $container ): void {
		// Nothing to bootstrap.
	}
}
