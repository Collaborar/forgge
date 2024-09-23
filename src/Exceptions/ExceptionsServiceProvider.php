<?php


namespace Forgge\Exceptions;

use Pimple\Container;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Forgge\Exceptions\Whoops\DebugDataProvider;
use Forgge\ServiceProviders\ExtendsConfigTrait;
use Forgge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide exceptions dependencies.
 *
 * @codeCoverageIgnore
 */
class ExceptionsServiceProvider implements ServiceProviderInterface {
	use ExtendsConfigTrait;

	/**
	 * {@inheritDoc}
	 */
	public function register( Container $container ): void {
		$debug = defined( 'WP_DEBUG' ) && WP_DEBUG;

		$this->extendConfig( $container, 'debug', [
			'enable' => $debug,
			'pretty_errors' => $debug,
		] );

		$this->registerPrettyErrorHandler( $container );
		$this->registerErrorHandler( $container );
	}

	/**
	 * Register the pretty error handler service.
	 *
	 * @param Container $container
	 */
	protected function registerPrettyErrorHandler( Container $container ): void {
		$container[ DebugDataProvider::class ] = fn ( Container $c ): DebugDataProvider =>
			new DebugDataProvider( $c );

		$container[ PrettyPageHandler::class ] = function ( Container $c ): PrettyPageHandler {
			$handler = new PrettyPageHandler();
			$handler->addResourcePath( implode( DIRECTORY_SEPARATOR, [FORGGE_DIR, 'src', 'Exceptions', 'Whoops'] ) );

			$handler->addDataTableCallback( 'Forgge: Route', function ( $inspector ) use ( $c ) {
				return $c[ DebugDataProvider::class ]->route( $inspector );
			} );

			return $handler;
		};

		$container[ Run::class ] = function ( Container $c ): ?Run {
			if ( ! class_exists( Run::class ) ) {
				return null;
			}

			$run = new Run();
			$run->allowQuit( false );

			$handler = $c[ PrettyPageHandler::class ];

			if ( $handler ) {
				$run->pushHandler( $handler );
			}

			return $run;
		};
	}

	/**
	 * Register the error handler service.
	 *
	 * @param Container $container
	 */
	protected function registerErrorHandler( Container $container ): void {
		$container[ FORGGE_EXCEPTIONS_ERROR_HANDLER_KEY ] = function ( Container $c ): ErrorHandler {
			$debug = $c[ FORGGE_CONFIG_KEY ]['debug'];
			$whoops = $debug['pretty_errors'] ? $c[ Run::class ] : null;
			return new ErrorHandler( $c[ FORGGE_RESPONSE_SERVICE_KEY ], $whoops, $debug['enable'] );
		};

		$container[ FORGGE_EXCEPTIONS_CONFIGURATION_ERROR_HANDLER_KEY ] = function ( Container $c ): ErrorHandler {
			$debug = $c[ FORGGE_CONFIG_KEY ]['debug'];
			$whoops = $debug['pretty_errors'] ? $c[ Run::class ] : null;
			return new ErrorHandler( $c[ FORGGE_RESPONSE_SERVICE_KEY ], $whoops, $debug['enable'] );
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( Container $container ): void {
		// Nothing to bootstrap.
	}
}
