<?php


namespace Forgge\Flash;

use Pimple\Container;
use Forgge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide flash dependencies.
 *
 * @codeCoverageIgnore
 */
class FlashServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( Container $container ): void {
		$container[ FORGGE_FLASH_KEY ] = function ( Container $c ): Flash {
			$session = null;
			if ( isset( $c[ FORGGE_SESSION_KEY ] ) ) {
				$session = &$c[ FORGGE_SESSION_KEY ];
			} else if ( isset( $_SESSION ) ) {
				$session = &$_SESSION;
			}
			return new Flash( $session );
		};

		$container[ FlashMiddleware::class ] = fn ( Container $c ): FlashMiddleware =>
			new FlashMiddleware( $c[ FORGGE_FLASH_KEY ] );

		$app = $container[ FORGGE_APPLICATION_KEY ];
		$app->alias( 'flash', FORGGE_FLASH_KEY );
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( Container $container ): void {
		// Nothing to bootstrap.
	}
}
