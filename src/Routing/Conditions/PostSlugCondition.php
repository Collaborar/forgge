<?php


namespace Forgge\Routing\Conditions;

use Forgge\Requests\RequestInterface;

/**
 * Check against the current post's slug.
 *
 * @codeCoverageIgnore
 */
class PostSlugCondition implements ConditionInterface {
	/**
	 * Post slug to check against
	 *
	 * @var string
	 */
	protected string $post_slug = '';

	/**
	 * Constructor
	 *
	 * @codeCoverageIgnore
	 * @param string $post_slug
	 */
	public function __construct( string $post_slug ) {
		$this->post_slug = $post_slug;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( RequestInterface $request ): bool {
		$post = get_post();
		return ( is_singular() && $post && $this->post_slug === $post->post_name );
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( RequestInterface $request ): array {
		return ['post_slug' => $this->post_slug];
	}
}
