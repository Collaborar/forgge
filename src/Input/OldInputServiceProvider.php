<?php


namespace Forgge\Input;

use Pimple\Container;
use Forgge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide old input dependencies.
 *
 * @codeCoverageIgnore
 */
class OldInputServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( Container $container ): void {
		$container[ FORGGE_OLD_INPUT_KEY ] = function ( $c ) {
			return new OldInput( $c[ FORGGE_FLASH_KEY ] );
		};

		$container[ OldInputMiddleware::class ] = function ( $c ) {
			return new OldInputMiddleware( $c[ FORGGE_OLD_INPUT_KEY ] );
		};

		$app = $container[ FORGGE_APPLICATION_KEY ];
		$app->alias( 'oldInput', FORGGE_OLD_INPUT_KEY );
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( Container $container ): void {
		// Nothing to bootstrap.
	}
}
