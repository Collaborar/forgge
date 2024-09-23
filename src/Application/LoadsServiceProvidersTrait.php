<?php


namespace Forgge\Application;

use Pimple\Container;
use Forgge\Controllers\ControllersServiceProvider;
use Forgge\Csrf\CsrfServiceProvider;
use Forgge\Exceptions\ConfigurationException;
use Forgge\Exceptions\ExceptionsServiceProvider;
use Forgge\Flash\FlashServiceProvider;
use Forgge\Input\OldInputServiceProvider;
use Forgge\Kernels\KernelsServiceProvider;
use Forgge\Middleware\MiddlewareServiceProvider;
use Forgge\Requests\RequestsServiceProvider;
use Forgge\Responses\ResponsesServiceProvider;
use Forgge\Routing\RoutingServiceProvider;
use Forgge\ServiceProviders\ServiceProviderInterface;
use Forgge\Support\Arr;
use Forgge\View\ViewServiceProvider;

/**
 * Load service providers.
 */
trait LoadsServiceProvidersTrait {
	/**
	 * Array of default service providers.
	 *
	 * @var ServiceProviderInterface[]
	 */
	protected array $service_providers = [
		ApplicationServiceProvider::class,
		KernelsServiceProvider::class,
		ExceptionsServiceProvider::class,
		RequestsServiceProvider::class,
		ResponsesServiceProvider::class,
		RoutingServiceProvider::class,
		ViewServiceProvider::class,
		ControllersServiceProvider::class,
		MiddlewareServiceProvider::class,
		CsrfServiceProvider::class,
		FlashServiceProvider::class,
		OldInputServiceProvider::class,
	];

	/**
	 * Register and bootstrap all service providers.
	 *
	 * @codeCoverageIgnore
	 * @param  Container $container
	 * @return void
	 */
	protected function loadServiceProviders( Container $container ): void {
		$container[ FORGGE_SERVICE_PROVIDERS_KEY ] = array_merge(
			$this->service_providers,
			Arr::get( $container[ FORGGE_CONFIG_KEY ], 'providers', [] )
		);

		$service_providers = array_map( function ( string $service_provider ) use ( $container ): mixed {
			if ( ! is_subclass_of( $service_provider, ServiceProviderInterface::class ) ) {
				throw new ConfigurationException(
					'The following class is not defined or does not implement ' .
					ServiceProviderInterface::class . ': ' . $service_provider
				);
			}

			// Provide container access to the service provider instance
			// so bootstrap hooks can be unhooked e.g.:
			// remove_action( 'some_action', [\App::resolve( SomeServiceProvider::class ), 'methodAddedToAction'] );
			$container[ $service_provider ] = new $service_provider();

			return $container[ $service_provider ];
		}, $container[ FORGGE_SERVICE_PROVIDERS_KEY ] );

		$this->registerServiceProviders( $service_providers, $container );
		$this->bootstrapServiceProviders( $service_providers, $container );
	}

	/**
	 * Register all service providers.
	 *
	 * @param  ServiceProviderInterface[] $service_providers
	 * @param  Container                  $container
	 * @return void
	 */
	protected function registerServiceProviders( array $service_providers, Container $container ): void {
		foreach ( $service_providers as $provider ) {
			$provider->register( $container );
		}
	}

	/**
	 * Bootstrap all service providers.
	 *
	 * @param  ServiceProviderInterface[] $service_providers
	 * @param  Container                  $container
	 * @return void
	 */
	protected function bootstrapServiceProviders( array $service_providers, Container $container ): void {
		foreach ( $service_providers as $provider ) {
			$provider->bootstrap( $container );
		}
	}
}
