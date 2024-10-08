<?php


namespace Forgge\Routing\Conditions;

use Forgge\Requests\RequestInterface;

/**
 * Check against a query var value.
 *
 * @codeCoverageIgnore
 */
class QueryVarCondition implements ConditionInterface {
	/**
	 * Query var name to check against.
	 *
	 * @var string|null
	 */
	protected ?string $query_var = null;

	/**
	 * Query var value to check against.
	 *
	 * @var string|null
	 */
	protected ?string $value = '';

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param string      $query_var
	 * @param string|null $value
	 */
	public function __construct( string $query_var, ?string $value = null ) {
		$this->query_var = $query_var;
		$this->value = $value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( RequestInterface $request ): bool {
		$query_var_value = get_query_var( $this->query_var, null );

		if ( $query_var_value === null ) {
			return false;
		}

		if ( $this->value === null ) {
			return true;
		}

		return (string) $this->value === $query_var_value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( RequestInterface $request ): array {
		return ['query_var' => $this->query_var, 'value' => $this->value];
	}
}
