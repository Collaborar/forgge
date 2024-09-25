<?php


namespace Forgge\Flash;

use ArrayAccess;
use Forgge\Exceptions\ConfigurationException;
use Forgge\Helpers\MixedType;
use Forgge\Support\Arr;

/**
 * Provide a way to flash data into the session for the next request.
 */
class Flash {
	/**
	 * Keys for different request contexts.
	 */
	const CURRENT_KEY = 'current';
	const NEXT_KEY = 'next';

	/**
	 * Key to store flashed data in store with.
	 *
	 * @var string
	 */
	protected string $store_key = '';

	/**
	 * Root store array or object implementing ArrayAccess.
	 *
	 * @var array|ArrayAccess|null
	 */
	protected array|ArrayAccess|null $store = null;

	/**
	 * Flash store array.
	 *
	 * @var array
	 */
	protected array $flashed = [];

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param array|ArrayAccess|null $store
	 * @param string             $store_key
	 */
	public function __construct( array|ArrayAccess|null &$store, string $store_key = '__forggeFlash' ) {
		$this->store_key = $store_key;
		$this->setStore( $store );
	}

	/**
	 * Get whether a store object is valid.
	 *
	 * @param  mixed   $store
	 * @returnbool
	 */
	protected function isValidStore( mixed $store ): bool {
		return ( is_array( $store ) || $store instanceof ArrayAccess );
	}

	/**
	 * Throw an exception if store is not valid.
	 *
	 * @return void
	 */
	protected function validateStore(): void {
		if ( ! $this->isValidStore( $this->store ) ) {
			throw new ConfigurationException(
				'Attempted to use Flash without an active session. ' .
				'Did you miss to call session_start()?'
			);
		}
	}

	/**
	 * Get the store for flash messages.
	 *
	 * @return array|ArrayAccess|null
	 */
	public function getStore(): array|ArrayAccess|null {
		return $this->store;
	}

	/**
	 * Set the store for flash messages.
	 *
	 * @param  array|ArrayAccess|null $store
	 * @return void
	 */
	public function setStore( array|ArrayAccess|null &$store ): void {
		if ( ! $this->isValidStore( $store ) ) {
			return;
		}

		$this->store = &$store;

		if ( ! isset( $this->store[ $this->store_key ] ) ) {
			$this->store[ $this->store_key ] = [
				static::CURRENT_KEY => [],
				static::NEXT_KEY => [],
			];
		}

		$this->flashed = $store[ $this->store_key ];
	}

	/**
	 * Get whether the flash service is enabled.
	 *
	 * @returnbool
	 */
	public function enabled(): bool {
		return $this->isValidStore( $this->store );
	}

	/**
	 * Get the entire store or the values for a key for a request.
	 *
	 * @param  string $request_key
	 * @param  string|null $key
	 * @param  mixed $default
	 * @return mixed
	 */
	protected function getFromRequest( string $request_key, ?string $key = null, mixed $default = [] ): mixed {
		$this->validateStore();

		if ( $key === null ) {
			return Arr::get( $this->flashed, $request_key, $default );
		}

		return Arr::get( $this->flashed[ $request_key ], $key, $default );
	}

	/**
	 * Add values for a key for a request.
	 *
	 * @param  string $request_key
	 * @param  string $key
	 * @param  mixed $new_items
	 * @return void
	 */
	protected function addToRequest( string $request_key, string $key, mixed $new_items ): void {
		$this->validateStore();

		$new_items = MixedType::toArray( $new_items );
		$items = MixedType::toArray( $this->getFromRequest( $request_key, $key, [] ) );
		$this->flashed[ $request_key ][ $key ] = array_merge( $items, $new_items );
	}

	/**
	 * Remove all values or values for a key from a request.
	 *
	 * @param  string $request_key
	 * @param  string|null $key
	 * @return void
	 */
	protected function clearFromRequest( string $request_key, ?string $key = null ): void {
		$this->validateStore();

		$keys = $key === null ? array_keys( $this->flashed[ $request_key ] ) : [$key];
		foreach ( $keys as $k ) {
			unset( $this->flashed[ $request_key ][ $k ] );
		}
	}

	/**
	 * Add values for a key for the next request.
	 *
	 * @param  string $key
	 * @param  mixed $new_items
	 * @return void
	 */
	public function add( string $key, mixed $new_items ): void {
		$this->addToRequest( static::NEXT_KEY, $key, $new_items );
	}

	/**
	 * Add values for a key for the current request.
	 *
	 * @param  string $key
	 * @param  mixed $new_items
	 * @return void
	 */
	public function addNow( string $key, mixed $new_items ): void {
		$this->addToRequest( static::CURRENT_KEY, $key, $new_items );
	}

	/**
	 * Get the entire store or the values for a key for the current request.
	 *
	 * @param  string|null $key
	 * @param  mixed $default
	 * @return mixed
	 */
	public function get( ?string $key = null, mixed $default = [] ): mixed {
		return $this->getFromRequest( static::CURRENT_KEY, $key, $default );
	}

	/**
	 * Get the entire store or the values for a key for the next request.
	 *
	 * @param  string|null $key
	 * @param  mixed $default
	 * @return mixed
	 */
	public function getNext( ?string $key = null, mixed $default = [] ): mixed {
		return $this->getFromRequest( static::NEXT_KEY, $key, $default );
	}

	/**
	 * Clear the entire store or the values for a key for the current request.
	 *
	 * @param  string|null $key
	 * @return void
	 */
	public function clear( ?string $key = null ): void {
		$this->clearFromRequest( static::CURRENT_KEY, $key );
	}

	/**
	 * Clear the entire store or the values for a key for the next request.
	 *
	 * @param  string|null $key
	 * @return void
	 */
	public function clearNext( ?string $key = null ): void {
		$this->clearFromRequest( static::NEXT_KEY, $key );
	}

	/**
	 * Shift current store and replace it with next store.
	 *
	 * @return void
	 */
	public function shift(): void {
		$this->validateStore();

		$this->flashed[ static::CURRENT_KEY ] = $this->flashed[ static::NEXT_KEY ];
		$this->flashed[ static::NEXT_KEY ] = [];
	}

	/**
	 * Save flashed data to store.
	 *
	 * @return void
	 */
	public function save(): void {
		$this->validateStore();

		$this->store[ $this->store_key ] = $this->flashed;
	}
}
