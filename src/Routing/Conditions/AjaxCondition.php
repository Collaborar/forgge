<?php


namespace Forgge\Routing\Conditions;

use Forgge\Requests\RequestInterface;

/**
 * Check against the current ajax action.
 *
 * @codeCoverageIgnore
 */
class AjaxCondition implements ConditionInterface, UrlableInterface {
	/**
	 * Ajax action to check against.
	 *
	 * @var string
	 */
	protected string $action = '';

	/**
	 * Flag whether to check against ajax actions which run for authenticated users.
	 *
	 * @var bool
	 */
	protected bool $private = true;

	/**
	 * Flag whether to check against ajax actions which run for unauthenticated users.
	 *
	 * @var bool
	 */
	protected bool $public = false;

	/**
	 * Constructor
	 *
	 * @codeCoverageIgnore
	 * @param string  $action
	 * @param bool $private
	 * @param bool $public
	 */
	public function __construct( string $action, bool $private = true, bool $public = false ) {
		$this->action = $action;
		$this->private = $private;
		$this->public = $public;
	}

	/**
	 * Check if the private authentication requirement matches.
	 *
	 * @return bool
	 */
	protected function matchesPrivateRequirement(): bool {
		return $this->private && is_user_logged_in();
	}

	/**
	 * Check if the public authentication requirement matches.
	 *
	 * @return bool
	 */
	protected function matchesPublicRequirement(): bool {
		return $this->public && ! is_user_logged_in();
	}

	/**
	 * Check if the ajax action matches the requirement.
	 *
	 * @param  RequestInterface $request
	 * @return bool
	 */
	protected function matchesActionRequirement( RequestInterface $request ): bool {
		return $this->action === $request->body( 'action', $request->query( 'action' ) );
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( RequestInterface $request ): bool {
		if ( ! wp_doing_ajax() ) {
			return false;
		}

		if ( ! $this->matchesActionRequirement( $request ) ) {
			return false;
		}

		return $this->matchesPrivateRequirement() || $this->matchesPublicRequirement();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getArguments( RequestInterface $request ): array {
		return ['action' => $this->action];
	}

	/**
	 * {@inheritDoc}
	 */
	public function toUrl( array $arguments = [] ): string {
		return add_query_arg( 'action', $this->action, self_admin_url( 'admin-ajax.php' ) );
	}
}
