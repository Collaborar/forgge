<?php


namespace Forgge\Application;

use Pimple\Container;
use Forgge\Exceptions\ClassNotFoundException;

/**
 * Generic class instance factory.
 */
class GenericFactory {
	/**
	 * Container.
	 *
	 * @var Container
	 */
	protected ?Container $container = null;

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param Container $container
	 */
	public function __construct( Container $container ) {
		$this->container = $container;
	}

	/**
	 * Make a class instance.
	 *
	 * @throws ClassNotFoundException
	 * @param  string $class
	 * @return mixed
	 */
	public function make( string $class ): mixed {
		if ( isset( $this->container[ $class ] ) ) {
			return $this->container[ $class ];
		}

		if ( ! class_exists( $class ) ) {
			throw new ClassNotFoundException( 'Class not found: ' . $class );
		}

		return new $class();
	}
}
