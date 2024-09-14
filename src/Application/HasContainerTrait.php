<?php


namespace Forgge\Application;

use Pimple\Container;

/**
 * Holds an IoC container.
 */
trait HasContainerTrait {
	/**
	 * IoC container.
	 *
	 * @var Container
	 */
	protected $container = null;

	/**
	 * Get the IoC container instance.
	 *
	 * @codeCoverageIgnore
	 * @return Container
	 */
	public function container() {
		return $this->container;
	}

	/**
	 * Set the IoC container instance.
	 *
	 * @codeCoverageIgnore
	 * @param  Container $container
	 * @return void
	 */
	public function setContainer( $container ) {
		$this->container = $container;
	}

	/**
	 * Resolve a dependency from the IoC container.
	 *
	 * @param  string     $key
	 * @return mixed|null
	 */
	public function resolve( $key ) {
		if ( ! isset( $this->container()[ $key ] ) ) {
			return null;
		}

		return $this->container()[ $key ];
	}
}
