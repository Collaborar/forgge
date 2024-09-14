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
	protected $action = '';

	/**
	 * Flag whether to check against ajax actions which run for authenticated users.
	 *
	 * @varbool
	 */
	protected $private = true;

	/**
	 * Flag whether to check against ajax actions which run for unauthenticated users.
	 *
	 * @varbool
	 */
	protected $public = false;

	/**
	 * Constructor
	 *
	 * @codeCoverageIgnore
	 * @param string  $action
	 * @parambool $private
	 * @parambool $public
	 */
	public function __construct( $action, $private = true, $public = false ) {
		$this->action = $action;
		$this->private = $private;
		$this->public = $public;
	}

	/**
	 * Check if the private authentication requirement matches.
	 *
	 * @returnbool
	 */
	protected function matchesPrivateRequirement() {
		return $this->private && is_user_logged_in();
	}

	/**
	 * Check if the public authentication requirement matches.
	 *
	 * @returnbool
	 */
	protected function matchesPublicRequirement() {
		return $this->public && ! is_user_logged_in();
	}

	/**
	 * Check if the ajax action matches the requirement.
	 *
	 * @param  RequestInterface $request
	 * @returnbool
	 */
	protected function matchesActionRequirement( RequestInterface $request ) {
		return $this->action === $request->body( 'action', $request->query( 'action' ) );
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSatisfied( RequestInterface $request ) {
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
	public function getArguments( RequestInterface $request ) {
		return ['action' => $this->action];
	}

	/**
	 * {@inheritDoc}
	 */
	public function toUrl( $arguments = [] ) {
		return add_query_arg( 'action', $this->action, self_admin_url( 'admin-ajax.php' ) );
	}
}
