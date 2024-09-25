<?php


namespace Forgge\ServiceProviders;

use Pimple\Container;

/**
 * Interface that service providers must implement
 */
interface ServiceProviderInterface {
	/**
	 * Register all dependencies in the IoC container.
	 *
	 * @param  Container $container
	 * @return void
	 */
	public function register( Container $container ): void;

	/**
	 * Bootstrap any services if needed.
	 *
	 * @param  Container $container
	 * @return void
	 */
	public function bootstrap( Container $container ): void;
}
