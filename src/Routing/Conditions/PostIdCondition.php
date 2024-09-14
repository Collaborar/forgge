<?php


namespace Forgge\Routing\Conditions;

use Forgge\Requests\RequestInterface;

/**
 * Check against the current post's id.
 *
 * @codeCoverageIgnore
 */
class PostIdCondition implements ConditionInterface, UrlableInterface {
	/**
	 * Post id to check against
	 *
	 * @var int
	 */
	protected $post_id = 0;

	/**
	 * Constructor
	 *
	 * @codeCoverageIgnore
	 * @param int $post_id
	 */
	public function __construct( $post_id ) {
		$this->post_id = (int) $post_id;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( RequestInterface $request ) {
		return ( is_singular() && $this->post_id === (int) get_the_ID() );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( RequestInterface $request ) {
		return ['post_id' => $this->post_id];
	}

	/**
	 * {@inheritDoc}
	 */
	public function toUrl( $arguments = [] ) {
		return get_permalink( $this->post_id );
	}
}
