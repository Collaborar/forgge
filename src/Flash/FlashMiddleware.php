<?php


namespace Forgge\Flash;

use Closure;
use Forgge\Requests\RequestInterface;

/**
 * Store current request data and clear old request data
 */
class FlashMiddleware {
	/**
	 * Flash service.
	 *
	 * @var Flash
	 */
	protected $flash = null;

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param Flash $flash
	 */
	public function __construct( Flash $flash ) {
		$this->flash = $flash;
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle( RequestInterface $request, Closure $next ) {
		$response = $next( $request );

		if ( $this->flash->enabled() ) {
			$this->flash->shift();
			$this->flash->save();
		}

		return $response;
	}
}
