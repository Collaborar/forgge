<?php


namespace Forgge\Application;

use Closure;

/**
 * Factory that makes closures which resolve values from the container.
 */
class ClosureFactory {
	/**
	 * Factory.
	 *
	 * @var GenericFactory
	 */
	protected ?GenericFactory $factory = null;

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param GenericFactory $factory
	 */
	public function __construct( GenericFactory $factory ) {
		$this->factory = $factory;
	}

	/**
	 * Make a closure that resolves a value from the container.
	 *
	 * @param  string $key
	 * @return Closure
	 */
	public function value( string $key ): Closure {
		return fn (): mixed => $this->factory->make( $key );
	}

	/**
	 * Make a closure that resolves a class instance from the container and
	 * calls one of its methods.
	 * Useful if you need to pass a callable to an API without container
	 * support such as the REST API.
	 *
	 * @param  string $key
	 * @param  string $method
	 * @return Closure
	 */
	public function method( string $key, string $method ): Closure {
		return function ( ...$parameters ) use ( $key, $method ): mixed {
			return ($this->factory->make( $key ))->$method( ...$parameters );
		};
	}
}
