<?php


namespace Forgge\Routing\Conditions;

use Forgge\Requests\RequestInterface;

/**
 * Check against the current post's type.
 *
 * @codeCoverageIgnore
 */
class PostTypeCondition implements ConditionInterface {
	/**
	 * Post type to check against
	 *
	 * @var string
	 */
	protected $post_type = '';

	/**
	 * Constructor
	 *
	 * @codeCoverageIgnore
	 * @param string $post_type
	 */
	public function __construct( $post_type ) {
		$this->post_type = $post_type;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( RequestInterface $request ) {
		return ( is_singular() && $this->post_type === get_post_type() );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( RequestInterface $request ) {
		return ['post_type' => $this->post_type];
	}
}
