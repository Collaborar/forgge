<?php


namespace Forgge\Helpers;

use Closure;
use Forgge\Application\GenericFactory;

/**
 * Handler factory.
 */
class HandlerFactory {
	/**
	 * Injection Factory.
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
	 * Make a Handler.
	 *
	 * @codeCoverageIgnore
	 * @param  string|Closure $raw_handler
	 * @param  string         $default_method
	 * @param  string         $namespace
	 * @return Handler
	 */
	public function make( string|Closure $raw_handler, string $default_method = '', string $namespace = '' ): Handler {
		return new Handler( $this->factory, $raw_handler, $default_method, $namespace );
	}
}
