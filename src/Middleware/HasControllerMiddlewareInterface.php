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
	public function getMiddleware( string $method ): array;

	/**
	 * Add middleware.
	 *
	 * @param  string|string[]      $middleware
	 * @return ControllerMiddleware
	 */
	public function addMiddleware( string|array $middleware ): ControllerMiddleware;

	/**
	 * Fluent alias for addMiddleware().
	 *
	 * @param  string|string[]      $middleware
	 * @return ControllerMiddleware
	 */
	public function middleware( string|array $middleware ): ControllerMiddleware;
}
