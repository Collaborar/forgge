<?php


namespace Forgge\Routing;

use Forgge\Exceptions\ConfigurationException;
use Forgge\Helpers\HasAttributesTrait;
use Forgge\Requests\RequestInterface;
use Forgge\Routing\Conditions\ConditionInterface;

/**
 * Represent a route
 */
class Route implements RouteInterface, HasQueryFilterInterface {
	use HasAttributesTrait;
	use HasQueryFilterTrait;

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( RequestInterface $request ): bool {
		$methods = $this->getAttribute( 'methods', [] );
		$condition = $this->getAttribute( 'condition' );

		if ( ! in_array( $request->getMethod(), $methods ) ) {
			return false;
		}

		if ( ! $condition instanceof ConditionInterface ) {
			throw new ConfigurationException( 'Route does not have a condition.' );
		}

		return $condition->isSatisfied( $request );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( RequestInterface $request ): array {
		$condition = $this->getAttribute( 'condition' );

		if ( ! $condition instanceof ConditionInterface ) {
			throw new ConfigurationException( 'Route does not have a condition.' );
		}

		return $condition->getArguments( $request );
	}
}
