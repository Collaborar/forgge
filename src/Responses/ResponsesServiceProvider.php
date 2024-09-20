<?php


namespace Forgge\Responses;

use Pimple\Container;
use Forgge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide responses dependencies.
 *
 * @codeCoverageIgnore
 */
class ResponsesServiceProvider implements ServiceProviderInterface {
	/**
	 * {@inheritDoc}
	 */
	public function register( Container $container ): void {
		$container[ FORGGE_RESPONSE_SERVICE_KEY ] = function ( $c ) {
			return new ResponseService( $c[ FORGGE_REQUEST_KEY ], $c[ FORGGE_VIEW_SERVICE_KEY ] );
		};

		$app = $container[ FORGGE_APPLICATION_KEY ];
		$app->alias( 'responses', FORGGE_RESPONSE_SERVICE_KEY );

		$app->alias( 'response', function () use ( $app ) {
			return call_user_func_array( [$app->responses(), 'response'], func_get_args() );
		} );

		$app->alias( 'output', function () use ( $app ) {
			return call_user_func_array( [$app->responses(), 'output'], func_get_args() );
		} );

		$app->alias( 'json', function () use ( $app ) {
			return call_user_func_array( [$app->responses(), 'json'], func_get_args() );
		} );

		$app->alias( 'redirect', function () use ( $app ) {
			return call_user_func_array( [$app->responses(), 'redirect'], func_get_args() );
		} );

		$app->alias( 'error', function () use ( $app ) {
			return call_user_func_array( [$app->responses(), 'error'], func_get_args() );
		} );
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( Container $container ): void {
		// Nothing to bootstrap.
	}
}
