<?php


namespace Forgge\Requests;

use Psr\Http\Message\ServerRequestInterface;

/**
 * A representation of a request to the server.
 */
interface RequestInterface extends ServerRequestInterface {
	/**
	 * Alias for ::getUri().
	 * Even though URI and URL are slightly different things this alias returns the URI for simplicity/familiarity.
	 *
	 * @return string
	 */
	public function getUrl(): string;

	/**
	 * Check if the request method is GET.
	 *
	 * @return bool
	 */
	public function isGet(): bool;

	/**
	 * Check if the request method is HEAD.
	 *
	 * @return bool
	 */
	public function isHead(): bool;

	/**
	 * Check if the request method is POST.
	 *
	 * @return bool
	 */
	public function isPost(): bool;

	/**
	 * Check if the request method is PUT.
	 *
	 * @return bool
	 */
	public function isPut(): bool;

	/**
	 * Check if the request method is PATCH.
	 *
	 * @return bool
	 */
	public function isPatch(): bool;

	/**
	 * Check if the request method is DELETE.
	 *
	 * @return bool
	 */
	public function isDelete(): bool;

	/**
	 * Check if the request method is OPTIONS.
	 *
	 * @return bool
	 */
	public function isOptions(): bool;

	/**
	 * Check if the request method is a "read" verb.
	 *
	 * @return bool
	 */
	public function isReadVerb(): bool;

	/**
	 * Check if the request is an ajax request.
	 *
	 * @return bool
	 */
	public function isAjax(): bool;

	/**
	 * Get a value from the request attributes.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function attributes( string $key = '', mixed $default = null ): mixed;

	/**
	 * Get a value from the request query (i.e. $_GET).
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function query( string $key = '', mixed $default = null ): mixed;

	/**
	 * Get a value from the request body (i.e. $_POST).
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function body( string $key = '', mixed $default = null ): mixed;

	/**
	 * Get a value from the COOKIE parameters.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function cookies( string $key = '', mixed $default = null ): mixed;

	/**
	 * Get a value from the FILES parameters.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function files( string $key = '', mixed $default = null ): mixed;

	/**
	 * Get a value from the SERVER parameters.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function server( string $key = '', mixed $default = null ): mixed;

	/**
	 * Get a value from the headers.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function headers( string $key = '', mixed $default = null ): mixed;
}
