<?php


namespace Forgge\Routing\Conditions;

use Forgge\Requests\RequestInterface;

/**
 * Check against an array of conditions in an AND logical relationship.
 */
class MultipleCondition implements ConditionInterface {
	/**
	 * Array of conditions to check.
	 *
	 * @var ConditionInterface[]
	 */
	protected array $conditions = [];

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param ConditionInterface[] $conditions
	 */
	public function __construct( array $conditions ) {
		$this->conditions = $conditions;
	}

	/**
	 * Get all assigned conditions
	 *
	 * @codeCoverageIgnore
	 * @return ConditionInterface[]
	 */
	public function getConditions(): array {
		return $this->conditions;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( RequestInterface $request ): bool {
		foreach ( $this->conditions as $condition ) {
			if ( ! $condition->isSatisfied( $request ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( RequestInterface $request ): array {
		$arguments = [];

		foreach ( $this->conditions as $condition ) {
			$arguments = array_merge( $arguments, $condition->getArguments( $request ) );
		}

		return $arguments;
	}
}
