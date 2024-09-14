<?php


namespace Forgge\Requests;

use Forgge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide request dependencies.
 *
 * @codeCoverageIgnore
 */
class RequestsServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		$container[ FORGGE_REQUEST_KEY ] = function () {
			return Request::fromGlobals();
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		// Nothing to bootstrap.
	}
}
