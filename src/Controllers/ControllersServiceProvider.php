<?php


namespace Forgge\Controllers;

use Pimple\Container;
use Forgge;
use Forgge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide controller dependencies
 *
 * @codeCoverageIgnore
 */
class ControllersServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( Container $container ): void {
		$container[ WordPressController::class ] = function ( $c ) {
			return new WordPressController( $c[ FORGGE_VIEW_SERVICE_KEY ] );
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( Container $container ): void {
		// Nothing to bootstrap.
	}
}
