<?php


namespace Forgge\Middleware;

/**
 * Allow objects to have controller middleware.
 */
trait HasControllerMiddlewareTrait {
	/**
	 * Array of middleware.
	 *
	 * @var ControllerMiddleware[]
	 */
	protected array $middleware = [];

	/**
	 * Get middleware.
	 *
	 * @param  string   $method
	 * @return string[]
	 */
	public function getMiddleware( string $method ): array {
		$middleware = array_filter( $this->middleware, function ( ControllerMiddleware $middleware ) use ( $method ) {
			return $middleware->appliesTo( $method );
		} );

		$middleware = array_map( function ( ControllerMiddleware $middleware ) {
			return $middleware->get();
		}, $middleware );

		if ( ! empty( $middleware ) ) {
			$middleware = call_user_func_array( 'array_merge', $middleware );
		}

		return $middleware;
	}

	/**
	 * Add middleware.
	 *
	 * @param  string|string[]      $middleware
	 * @return ControllerMiddleware
	 */
	public function addMiddleware( string|array $middleware ): ControllerMiddleware {
		$controller_middleware = new ControllerMiddleware( $middleware );

		$this->middleware = array_merge(
			$this->middleware,
			[$controller_middleware]
		);

		return $controller_middleware;
	}

	/**
	 * Fluent alias for addMiddleware().
	 *
	 * @codeCoverageIgnore
	 * @param  string|string[]      $middleware
	 * @return ControllerMiddleware
	 */
	public function middleware( string|array $middleware ): ControllerMiddleware {
		return call_user_func_array( [$this, 'addMiddleware'], func_get_args() );
	}
}
