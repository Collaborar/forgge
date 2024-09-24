<?php


namespace Forgge\Helpers;

/**
 * Represent an object which has an array of attributes.
 */
interface HasAttributesInterface {
	/**
	 * Get attribute.
	 *
	 * @param  string $attribute
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function getAttribute( string $attribute, mixed $default = '' ): mixed;

	/**
	 * Get all attributes.
	 *
	 * @return array<string, mixed>
	 */
	public function getAttributes(): array;

	/**
	 * Set attribute.
	 *
	 * @param  string $attribute
	 * @param  mixed  $value
	 * @return void
	 */
	public function setAttribute( string $attribute, mixed $value ): void;

	/**
	 * Fluent alias for setAttribute().
	 *
	 * @param  string $attribute
	 * @param  mixed  $value
	 * @return static $this
	 */
	public function attribute( string $attribute, mixed $value ): HasAttributesInterface;

	/**
	 * Set attributes.
	 * No attempt to merge attributes is done - this is a direct overwrite operation.
	 *
	 * @param  array<string, mixed> $attributes
	 * @return void
	 */
	public function setAttributes( array $attributes ): void;

	/**
	 * Fluent alias for setAttributes().
	 *
	 * @param  array<string, mixed> $attributes
	 * @return static               $this
	 */
	public function attributes( array $attributes ): HasAttributesInterface;
}
