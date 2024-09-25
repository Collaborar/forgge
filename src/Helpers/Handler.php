<?php


namespace Forgge\Helpers;

use Closure;
use Forgge\Application\GenericFactory;
use Forgge\Exceptions\ClassNotFoundException;
use Forgge\Exceptions\ConfigurationException;
use Forgge\Support\Arr;

/**
 * Represent a generic handler - a Closure or a class method to be resolved from the service container
 */
class Handler {
	/**
	 * Injection Factory.
	 *
	 * @var GenericFactory
	 */
	protected ?GenericFactory $factory = null;

	/**
	 * Parsed handler
	 *
	 * @var array|Closure
	 */
	protected array|Closure|null $handler = null;

	/**
	 * Constructor
	 *
	 * @param GenericFactory       $factory
	 * @param string|array|Closure $raw_handler
	 * @param string               $default_method
	 * @param string               $namespace
	 */
	public function __construct(
		GenericFactory $factory,
		string|array|Closure $raw_handler,
		string $default_method = '',
		string $namespace = ''
	) {
		$this->factory = $factory;

		$handler = $this->parse( $raw_handler, $default_method, $namespace );

		if ( $handler === null ) {
			throw new ConfigurationException( 'No or invalid handler provided.' );
		}

		$this->handler = $handler;
	}

	/**
	 * Parse a raw handler to a Closure or a [class, method, namespace] array
	 *
	 * @param  string|array|Closure $raw_handler
	 * @param  string               $default_method
	 * @param  string               $namespace
	 * @return array|Closure|null
	 */
	protected function parse( string|array|Closure $raw_handler, string $default_method, string $namespace ): array|Closure|null {
		if ( $raw_handler instanceof Closure ) {
			return $raw_handler;
		}

		if ( is_array( $raw_handler ) ) {
			return $this->parseFromArray( $raw_handler, $default_method, $namespace );
		}

		return $this->parseFromString( $raw_handler, $default_method, $namespace );
	}

	/**
	 * Parse a [Class::class, 'method'] array handler to a [class, method, namespace] array
	 *
	 * @param  array      $raw_handler
	 * @param  string     $default_method
	 * @param  string     $namespace
	 * @return array|null
	 */
	protected function parseFromArray( array $raw_handler, string $default_method, string $namespace ): ?array {
		$class = Arr::get( $raw_handler, 0, '' );
		$class = preg_replace( '/^\\\\+/', '', $class );
		$method = Arr::get( $raw_handler, 1, $default_method );

		if ( empty( $class ) ) {
			return null;
		}

		if ( empty( $method ) ) {
			return null;
		}

		return [
			'class' => $class,
			'method' => $method,
			'namespace' => $namespace,
		];
	}

	/**
	 * Parse a 'Controller@method' or 'Controller::method' string handler to a [class, method, namespace] array
	 *
	 * @param  string     $raw_handler
	 * @param  string     $default_method
	 * @param  string     $namespace
	 * @return array|null
	 */
	protected function parseFromString( string $raw_handler, string $default_method, string $namespace ): ?array {
		return $this->parseFromArray( preg_split( '/@|::/', $raw_handler, 2 ), $default_method, $namespace );
	}

	/**
	 * Get the parsed handler
	 *
	 * @return array|Closure
	 */
	public function get(): array|Closure|null {
		return $this->handler;
	}

	/**
	 * Make an instance of the handler.
	 *
	 * @return object
	 */
	public function make(): object {
		$handler = $this->get();

		if ( $handler instanceof Closure ) {
			return $handler;
		}

		$namespace = $handler['namespace'];
		$class = $handler['class'];

		try {
			$instance = $this->factory->make( $class );
		} catch ( ClassNotFoundException $e ) {
			try {
				$instance = $this->factory->make( $namespace . $class );
			} catch ( ClassNotFoundException $e ) {
				throw new ClassNotFoundException( 'Class not found - tried: ' . $class . ', ' . $namespace . $class );
			}
		}

		return $instance;
	}

	/**
	 * Execute the parsed handler with any provided arguments and return the result.
	 *
	 * @param  mixed ,...$arguments
	 * @return mixed
	 */
	public function execute( mixed ...$arguments ): mixed {
		$instance = $this->make();

		if ( $instance instanceof Closure ) {
			return $instance( ...$arguments );
		}

		/** @psalm-suppress UndefinedMethod */
		$method = $this->get()['method'] ?? null;

		if ( null === $method ) {
			throw new \RuntimeException( 'Method not found in instance.' );
		}

		return $instance->$method( ...$arguments );
	}
}
