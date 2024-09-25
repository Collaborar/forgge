<?php


namespace Forgge\Routing;

use Forgge\Exceptions\ConfigurationException;
use Forgge\Requests\RequestInterface;
use Forgge\Routing\Conditions\CanFilterQueryInterface;

/**
 * Represent an object which has a WordPress query filter attribute.
 */
trait HasQueryFilterTrait {
	/**
	 * Query filter.
	 *
	 * @var callable|null
	 */
	protected $query_filter = null;

	/**
	 * Get attribute.
	 *
	 * @param  string $attribute
	 * @param  mixed  $default
	 * @return mixed
	 */
	abstract public function getAttribute( string $attribute, mixed $default = '' ): mixed;

	/**
	 * Apply the query filter, if any.
	 *
	 * @param  RequestInterface $request
	 * @param  array            $query_vars
	 * @return array
	 */
	public function applyQueryFilter( RequestInterface $request, array $query_vars ): array {
		$query = $this->getAttribute( 'query' );
		$condition = $this->getAttribute( 'condition' );

		if ( $query === null ) {
			return $query_vars;
		}

		if ( ! $condition instanceof CanFilterQueryInterface ) {
			throw new ConfigurationException(
				'Only routes with a condition implementing the ' . CanFilterQueryInterface::class . ' ' .
				'interface can apply query filters. ' .
				'Make sure your route has a URL condition and it is not in a non-URL route group.'
			);
		}

		return call_user_func_array(
			$query,
			array_merge(
				[$query_vars],
				array_values( $condition->getArguments( $request ) )
			)
		);
	}
}
