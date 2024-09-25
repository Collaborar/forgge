<?php


namespace Forgge\Kernels;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Forgge\Helpers\Handler;
use Forgge\Middleware\HasMiddlewareDefinitionsInterface;
use Forgge\Requests\RequestInterface;

/**
 * Describes how a request is handled.
 */
interface HttpKernelInterface extends HasMiddlewareDefinitionsInterface {
	/**
	 * Bootstrap the kernel.
	 *
	 * @return void
	 */
	public function bootstrap(): void;

	/**
	 * Run a response pipeline for the given request.
	 *
	 * @param  RequestInterface       $request
	 * @param  string[]               $middleware
	 * @param  string|Closure|Handler $handler
	 * @param  array                  $arguments
	 * @return ResponseInterface
	 */
	public function run(
		RequestInterface $request,
		array $middleware,
		string|Closure|Handler $handler,
		array $arguments = []
	): ResponseInterface;

	/**
	 * Return a response for the given request.
	 *
	 * @param  RequestInterface       $request
	 * @param  array                  $arguments
	 * @return ResponseInterface|null
	 */
	public function handle( RequestInterface $request, array $arguments = [] ): ?ResponseInterface;
}
