<?php

namespace Forgge\Routing\Conditions;

use Forgge\Requests\RequestInterface;

class RestCondition implements ConditionInterface {
	/**
	 * Rest route.
	 */
	protected string $route = '';

	public function __construct( string $route ) {
		$this->route = $route;
	}

	public function isSatisfied( RequestInterface $request ): bool {
		return is_rest();
	}

	public function getArguments( RequestInterface $request ): array {
		return [
			'route' => $this->route,
			'test' => 'here',
		];
	}
}
