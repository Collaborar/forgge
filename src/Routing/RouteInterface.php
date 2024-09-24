<?php


namespace Forgge\Routing;

use Forgge\Helpers\HasAttributesInterface;
use Forgge\Requests\RequestInterface;

/**
 * Interface that routes must implement
 */
interface RouteInterface extends HasAttributesInterface {
	/**
	 * Get whether the route is satisfied.
	 *
	 * @param  RequestInterface $request
	 * @return bool
	 */
	public function isSatisfied( RequestInterface $request ): bool;

	/**
	 * Get arguments.
	 *
	 * @param  RequestInterface $request
	 * @return array
	 */
	public function getArguments( RequestInterface $request ): array;
}
