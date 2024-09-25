<?php


namespace Forgge\View;

trait HasNameTrait {
	/**
	 * Name.
	 *
	 * @var string
	 */
	protected string $name = '';

	/**
	 * Get name.
	 *
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * Set name.
	 *
	 * @param  string $name
	 * @return static $this
	 */
	public function setName( string $name ) {
		$this->name = $name;
		return $this;
	}
}
