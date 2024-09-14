<?php


namespace Forgge\Input;

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
	public function register( $container ) {
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
	public function bootstrap( $container ) {
		// Nothing to bootstrap.
	}
}
