<?php


namespace Forgge\Csrf;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Forgge\Requests\RequestInterface;

/**
 * Store current request data and clear old request data
 */
class CsrfMiddleware {
	/**
	 * CSRF service.
	 *
	 * @var Csrf
	 */
	protected ?Csrf $csrf = null;

	/**
	 * Constructor.
	 *
	 * @param Csrf $csrf
	 */
	public function __construct( Csrf $csrf ) {
		$this->csrf = $csrf;
	}

	/**
	 * Reject requests that fail nonce validation.
	 *
	 * @param  RequestInterface     $request
	 * @param  Closure              $next
	 * @param  int|string           $action
	 * @return ResponseInterface
	 * @throws InvalidCsrfTokenException
	 */
	public function handle( RequestInterface $request, Closure $next, int|string $action = -1 ): ResponseInterface {
		if ( ! $request->isReadVerb() ) {
			$token = $this->csrf->getTokenFromRequest( $request );
			if ( ! $this->csrf->isValidToken( $token, $action ) ) {
				throw new InvalidCsrfTokenException();
			}
		}

		$this->csrf->generateToken( $action );

		return $next( $request );
	}
}
