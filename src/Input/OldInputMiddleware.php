<?php


namespace Forgge\Input;

use Closure;
use Forgge\Requests\RequestInterface;

/**
 * Store current request data and clear old request data
 */
class OldInputMiddleware {
	/**
	 * OldInput service.
	 *
	 * @var OldInput
	 */
	protected ?OldInput $old_input = null;

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param OldInput $old_input
	 */
	public function __construct( OldInput $old_input ) {
		$this->old_input = $old_input;
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle( RequestInterface $request, Closure $next ): RequestInterface {
		if ( $this->old_input->enabled() && $request->isPost() ) {
			$this->old_input->set( $request->body() );
		}

		return $next( $request );
	}
}
