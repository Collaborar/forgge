<?php


namespace Forgge\View;

use Forgge\Support\Arr;

trait HasContextTrait {
	/**
	 * Context.
	 *
	 * @var array
	 */
	protected $context = [];

	/**
	 * Get context values.
	 *
	 * @param  string|null $key
	 * @param  mixed|null  $default
	 * @return mixed
	 */
	public function getContext( $key = null, $default = null ) {
		if ( $key === null ) {
			return $this->context;
		}

		return Arr::get( $this->context, $key, $default );
	}

	/**
	 * Add context values.
	 *
	 * @param  string|array<string, mixed> $key
	 * @param  mixed                       $value
	 * @return static                      $this
	 */
	public function with( $key, $value = null ) {
		if ( is_array( $key ) ) {
			$this->context = array_merge( $this->getContext(), $key );
		} else {
			$this->context[ $key ] = $value;
		}
		return $this;
	}
}
