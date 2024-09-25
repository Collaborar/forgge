<?php


namespace Forgge\View;

use Forgge\Responses\ResponsableInterface;

/**
 * Represent and render a view to a string.
 */
interface ViewInterface extends HasContextInterface, ResponsableInterface {
	/**
	 * Get name.
	 *
	 * @return string
	 */
	public function getName(): string;

	/**
	 * Set name.
	 *
	 * @param  string $name
	 * @return static $this
	 */
	public function setName( string $name );

	/**
	 * Render the view to a string.
	 *
	 * @return string
	 */
	public function toString(): string;
}
