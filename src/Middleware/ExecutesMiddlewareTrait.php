<?php


namespace Forgge\Middleware;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Forgge\Requests\RequestInterface;

/**
 * Executes middleware.
 */
trait ExecutesMiddlewareTrait {
	/**
	 * Make a middleware class instance.
	 *
	 * @param  string $class
	 * @return object
	 */
	abstract protected function makeMiddleware( string $class ): object;

	/**
	 * Execute an array of middleware recursively (last in, first out).
	 *
	 * @param  string[][]        $middleware
	 * @param  RequestInterface  $request
	 * @param  Closure           $next
	 * @return ResponseInterface
	 */
	protected function executeMiddleware( $middleware, RequestInterface $request, Closure $next ) {
		$top_middleware = array_shift( $middleware );

		if ( $top_middleware === null ) {
			return $next( $request );
		}

		$top_middleware_next = function ( $request ) use ( $middleware, $next ) {
			return $this->executeMiddleware( $middleware, $request, $next );
		};

		$instance = $this->makeMiddleware( $top_middleware[0] );
		$arguments = array_merge(
			[$request, $top_middleware_next],
			array_slice( $top_middleware, 1 )
		);

		return call_user_func_array( [$instance, 'handle'], $arguments );
	}
}
