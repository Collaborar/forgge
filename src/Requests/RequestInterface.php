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
	public function getUrl();

	/**
	 * Check if the request method is GET.
	 *
	 * @returnbool
	 */
	public function isGet();

	/**
	 * Check if the request method is HEAD.
	 *
	 * @returnbool
	 */
	public function isHead();

	/**
	 * Check if the request method is POST.
	 *
	 * @returnbool
	 */
	public function isPost();

	/**
	 * Check if the request method is PUT.
	 *
	 * @returnbool
	 */
	public function isPut();

	/**
	 * Check if the request method is PATCH.
	 *
	 * @returnbool
	 */
	public function isPatch();

	/**
	 * Check if the request method is DELETE.
	 *
	 * @returnbool
	 */
	public function isDelete();

	/**
	 * Check if the request method is OPTIONS.
	 *
	 * @returnbool
	 */
	public function isOptions();

	/**
	 * Check if the request method is a "read" verb.
	 *
	 * @returnbool
	 */
	public function isReadVerb();

	/**
	 * Check if the request is an ajax request.
	 *
	 * @returnbool
	 */
	public function isAjax();

	/**
	 * Get a value from the request attributes.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function attributes( $key = '', $default = null );

	/**
	 * Get a value from the request query (i.e. $_GET).
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function query( $key = '', $default = null );

	/**
	 * Get a value from the request body (i.e. $_POST).
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function body( $key = '', $default = null );

	/**
	 * Get a value from the COOKIE parameters.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function cookies( $key = '', $default = null );

	/**
	 * Get a value from the FILES parameters.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function files( $key = '', $default = null );

	/**
	 * Get a value from the SERVER parameters.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function server( $key = '', $default = null );

	/**
	 * Get a value from the headers.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function headers( $key = '', $default = null );
}
