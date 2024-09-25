<?php


namespace Forgge\Helpers;

use Forgge\Support\Arr;

/**
 * Represent an object which has an array of attributes.
 */
trait HasAttributesTrait {
	/**
	 * Attributes.
	 *
	 * @var array<string, mixed>
	 */
	protected $attributes = [];

	/**
	 * Get attribute.
	 *
	 * @param  string $attribute
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function getAttribute( string $attribute, mixed $default = '' ): mixed {
		return Arr::get( $this->getAttributes(), $attribute, $default );
	}

	/**
	 * Get all attributes.
	 *
	 * @return array<string, mixed>
	 */
	public function getAttributes(): array {
		return $this->attributes;
	}

	/**
	 * Set attribute.
	 *
	 * @param  string $attribute
	 * @param  mixed  $value
	 * @return void
	 */
	public function setAttribute( string $attribute, mixed $value ): void {
		$this->setAttributes( array_merge(
			$this->getAttributes(),
			[$attribute => $value]
		) );
	}

	/**
	 * Fluent alias for setAttribute().
	 *
	 * @codeCoverageIgnore
	 * @param  string $attribute
	 * @param  mixed  $value
	 * @return static $this
	 */
	public function attribute( string $attribute, mixed $value ) {
		$this->setAttribute( $attribute, $value );

		return $this;
	}

	/**
	 * Set all attributes.
	 * No attempt to merge attributes is done - this is a direct overwrite operation.
	 *
	 * @param  array<string, mixed> $attributes
	 * @return void
	 */
	public function setAttributes( array $attributes ): void {
		$this->attributes = $attributes;
	}

	/**
	 * Fluent alias for setAttributes().
	 *
	 * @codeCoverageIgnore
	 * @param  array<string, mixed> $attributes
	 * @return static               $this
	 */
	public function attributes( array $attributes ) {
		$this->setAttributes( $attributes );

		return $this;
	}
}
