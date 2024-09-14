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
	public function register( $container ) {
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
	protected function registerPrettyErrorHandler( $container ) {
		$container[ DebugDataProvider::class ] = function ( $container ) {
			return new DebugDataProvider( $container );
		};

		$container[ PrettyPageHandler::class ] = function ( $container ) {
			$handler = new PrettyPageHandler();
			$handler->addResourcePath( implode( DIRECTORY_SEPARATOR, [FORGGE_DIR, 'src', 'Exceptions', 'Whoops'] ) );

			$handler->addDataTableCallback( 'Forgge: Route', function ( $inspector ) use ( $container ) {
				return $container[ DebugDataProvider::class ]->route( $inspector );
			} );

			return $handler;
		};

		$container[ Run::class ] = function ( $container ) {
			if ( ! class_exists( Run::class ) ) {
				return null;
			}

			$run = new Run();
			$run->allowQuit( false );

			$handler = $container[ PrettyPageHandler::class ];

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
	protected function registerErrorHandler( $container ) {
		$container[ FORGGE_EXCEPTIONS_ERROR_HANDLER_KEY ] = function ( $container ) {
			$debug = $container[ FORGGE_CONFIG_KEY ]['debug'];
			$whoops = $debug['pretty_errors'] ? $container[ Run::class ] : null;
			return new ErrorHandler( $container[ FORGGE_RESPONSE_SERVICE_KEY ], $whoops, $debug['enable'] );
		};

		$container[ FORGGE_EXCEPTIONS_CONFIGURATION_ERROR_HANDLER_KEY ] = function ( $container ) {
			$debug = $container[ FORGGE_CONFIG_KEY ]['debug'];
			$whoops = $debug['pretty_errors'] ? $container[ Run::class ] : null;
			return new ErrorHandler( $container[ FORGGE_RESPONSE_SERVICE_KEY ], $whoops, $debug['enable'] );
		};
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		// Nothing to bootstrap.
	}
}
