<?php


namespace Forgge\Routing\Conditions;

use Forgge\Requests\RequestInterface;

/**
 * Negate another condition's result.
 */
class NegateCondition implements ConditionInterface {
	/**
	 * Condition to negate.
	 *
	 * @var ConditionInterface
	 */
	protected $condition = null;

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param ConditionInterface $condition
	 */
	public function __construct( $condition ) {
		$this->condition = $condition;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( RequestInterface $request ) {
		return ! $this->condition->isSatisfied( $request );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( RequestInterface $request ) {
		return $this->condition->getArguments( $request );
	}
}
