<?php


namespace Forgge\Input;

use Forgge\Flash\Flash;
use Forgge\Support\Arr;

/**
 * Provide a way to get values from the previous request.
 */
class OldInput {
	/**
	 * Flash service.
	 *
	 * @var Flash
	 */
	protected $flash = null;

	/**
	 * Key to store the flashed data with.
	 *
	 * @var string
	 */
	protected $flash_key = '';

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param Flash  $flash
	 * @param string $flash_key
	 */
	public function __construct( Flash $flash, $flash_key = '__forggeOldInput' ) {
		$this->flash = $flash;
		$this->flash_key = $flash_key;
	}

	/**
	 * Get whether the old input service is enabled.
	 *
	 * @returnbool
	 */
	public function enabled() {
		return $this->flash->enabled();
	}

	/**
	 * Get request value for key from the previous request.
	 *
	 * @param  string $key
	 * @param  mixed  $default
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		return Arr::get( $this->flash->get( $this->flash_key, [] ), $key, $default );
	}

	/**
	 * Set input for the next request.
	 *
	 * @param array $input
	 */
	public function set( $input ) {
		$this->flash->add( $this->flash_key, $input );
	}

	/**
	 * Clear input for the next request.
	 *
	 * @return void
	 */
	public function clear() {
		$this->flash->clear( $this->flash_key );
	}
}
