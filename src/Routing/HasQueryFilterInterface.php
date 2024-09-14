<?php


namespace Forgge\Routing;

use Forgge\Requests\RequestInterface;

/**
 * Represent an object which has a WordPress query filter.
 */
interface HasQueryFilterInterface {
	/**
	 * Apply the query filter, if any.
	 *
	 * @param  RequestInterface $request
	 * @param  array            $query_vars
	 * @return array
	 */
	public function applyQueryFilter( $request, $query_vars );
}
