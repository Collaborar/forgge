<?php


namespace Forgge\Flash;

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
	public function register( $container ) {
		$container[ FORGGE_FLASH_KEY ] = function ( $c ) {
			$session = null;
			if ( isset( $c[ FORGGE_SESSION_KEY ] ) ) {
				$session = &$c[ FORGGE_SESSION_KEY ];
			} else if ( isset( $_SESSION ) ) {
				$session = &$_SESSION;
			}
			return new Flash( $session );
		};

		$container[ FlashMiddleware::class ] = function ( $c ) {
			return new FlashMiddleware( $c[ FORGGE_FLASH_KEY ] );
		};

		$app = $container[ FORGGE_APPLICATION_KEY ];
		$app->alias( 'flash', FORGGE_FLASH_KEY );
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		// Nothing to bootstrap.
	}
}
