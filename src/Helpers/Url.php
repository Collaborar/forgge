<?php


namespace Forgge\Helpers;

use Forgge\Requests\RequestInterface;
use Forgge\Support\Arr;

/**
 * A collection of tools dealing with URLs.
 */
class Url {
	/**
	 * Get the path for the request relative to the home url.
	 * Works only with absolute URLs.
	 *
	 * @param  RequestInterface $request
	 * @param  string           $home_url
	 * @return string
	 */
	public static function getPath( RequestInterface $request, string $home_url = '' ): string {
		$parsed_request = wp_parse_url( $request->getUrl() );
		$parsed_home = wp_parse_url( $home_url ? $home_url : home_url( '/' ) );

		$request_path = Arr::get( $parsed_request, 'path', '/' );
		$request_path = static::removeTrailingSlash( $request_path );
		$request_path = static::addLeadingSlash( $request_path );

		if ( $parsed_request['host'] !== $parsed_home['host'] ) {
			return $request_path;
		}

		$home_path = Arr::get( $parsed_home, 'path', '/' );
		$home_path = static::removeTrailingSlash( $home_path );
		$home_path = static::addLeadingSlash( $home_path );
		$path = $request_path;

		if ( strpos( $request_path, $home_path ) === 0 ) {
			$path = substr( $request_path, strlen( $home_path ) );
		}

		return static::addLeadingSlash( $path );
	}

	/**
	 * Ensure url has a leading slash
	 *
	 * @param  string  $url
	 * @param bool $leave_blank
	 * @return string
	 */
	public static function addLeadingSlash( string $url, bool $leave_blank = false ): string {
		if ( $leave_blank && $url === '' ) {
			return '';
		}

		return '/' . static::removeLeadingSlash( $url );
	}

	/**
	 * Ensure url does not have a leading slash
	 *
	 * @param  string $url
	 * @return string
	 */
	public static function removeLeadingSlash( string $url ): string {
		return preg_replace( '/^\/+/', '', $url );
	}

	/**
	 * Ensure url has a trailing slash
	 *
	 * @param  string  $url
	 * @param bool $leave_blank
	 * @return string
	 */
	public static function addTrailingSlash( string $url, bool $leave_blank = false ): string {
		if ( $leave_blank && $url === '' ) {
			return '';
		}

		return trailingslashit( $url );
	}

	/**
	 * Ensure url does not have a trailing slash
	 *
	 * @param  string $url
	 * @return string
	 */
	public static function removeTrailingSlash( string $url ): string {
		return untrailingslashit( $url );
	}
}
