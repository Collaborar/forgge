<?php


namespace Forgge\View;

use Forgge\Support\Arr;

trait HasContextTrait {
	/**
	 * Context.
	 *
	 * @var array
	 */
	protected array $context = [];

	/**
	 * Get context values.
	 *
	 * @param  string|null $key
	 * @param  mixed|null  $default
	 * @return mixed
	 */
	public function getContext( ?string $key = null, mixed $default = null ): mixed {
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
	public function with( string|array $key, mixed $value = null ) {
		if ( is_array( $key ) ) {
			$this->context = array_merge( $this->getContext(), $key );
		} else {
			$this->context[ $key ] = $value;
		}
		return $this;
	}
}
