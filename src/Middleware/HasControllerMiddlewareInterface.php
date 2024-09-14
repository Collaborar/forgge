<?php


namespace Forgge\Middleware;

/**
 * Interface for HasControllerMiddlewareTrait.
 */
interface HasControllerMiddlewareInterface {
	/**
	 * Get middleware.
	 *
	 * @param  string   $method
	 * @return string[]
	 */
	public function getMiddleware( $method );

	/**
	 * Add middleware.
	 *
	 * @param  string|string[]      $middleware
	 * @return ControllerMiddleware
	 */
	public function addMiddleware( $middleware );

	/**
	 * Fluent alias for addMiddleware().
	 *
	 * @param  string|string[]      $middleware
	 * @return ControllerMiddleware
	 */
	public function middleware( $middleware );
}
