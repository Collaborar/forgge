<?php


namespace Forgge\Routing\Conditions;

use Forgge\Requests\RequestInterface;

/**
 * Interface that condition types must implement
 */
interface ConditionInterface {
	/**
	 * Get whether the condition is satisfied
	 *
	 * @param  RequestInterface $request
	 * @return bool
	 */
	public function isSatisfied( RequestInterface $request ): bool;

	/**
	 * Get an array of arguments for use in request
	 *
	 * @param  RequestInterface $request
	 * @return array
	 */
	public function getArguments( RequestInterface $request ): array;
}
