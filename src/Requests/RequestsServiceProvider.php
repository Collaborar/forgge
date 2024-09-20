<?php


namespace Forgge\Requests;

use Pimple\Container;
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
	public function register( Container $container ): void {
		$container[ FORGGE_REQUEST_KEY ] = function () {
			return Request::fromGlobals();
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( Container $container ): void {
		// Nothing to bootstrap.
	}
}
