<?php


namespace Forgge\Exceptions;

use Exception as PhpException;
use Psr\Http\Message\ResponseInterface;
use Forgge\Requests\RequestInterface;

interface ErrorHandlerInterface {
	/**
	 * Register any necessary error, exception and shutdown handlers.
	 *
	 * @return void
	 */
	public function register(): void;

	/**
	 * Unregister any registered error, exception and shutdown handlers.
	 *
	 * @return void
	 */
	public function unregister(): void;

	/**
	 * Get a response representing the specified exception.
	 *
	 * @param  RequestInterface  $request
	 * @param  PhpException      $exception
	 * @return ResponseInterface
	 */
	public function getResponse( RequestInterface $request, PhpException $exception ): ResponseInterface;
}
