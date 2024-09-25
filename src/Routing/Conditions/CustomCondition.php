<?php


namespace Forgge\Routing\Conditions;

use Forgge\Requests\RequestInterface;

/**
 * Check against a custom callable.
 */
class CustomCondition implements ConditionInterface {
	/**
	 * Callable to use
	 *
	 * @var callable
	 */
	protected $callable = null;

	/**
	 * Arguments to pass to the callable and controller
	 *
	 * @var array
	 */
	protected array $arguments = [];

	/**
	 * Constructor
	 *
	 * @codeCoverageIgnore
	 * @param callable $callable
	 * @param mixed    ,...$arguments
	 */
	public function __construct( callable $callable ) {
		$this->callable = $callable;
		$this->arguments = array_values( array_slice( func_get_args(), 1 ) );
	}

	/**
	 * Get the assigned callable
	 *
	 * @codeCoverageIgnore
	 * @return callable
	 */
	public function getCallable(): ?callable {
		return $this->callable;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( RequestInterface $request ): bool {
		return call_user_func_array( $this->callable, $this->arguments );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( RequestInterface $request ): array {
		return $this->arguments;
	}
}
