<?php


namespace Forgge\Middleware;

use Closure;
use Forgge\Requests\RequestInterface;
use Forgge\Responses\ResponseService;

/**
 * Redirect non-logged in users to a specific URL.
 */
class UserLoggedInMiddleware {
	/**
	 * Response service.
	 *
	 * @var ResponseService
	 */
	protected ?ResponseService $response_service = null;

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param ResponseService $response_service
	 */
	public function __construct( ResponseService $response_service ) {
		$this->response_service = $response_service;
	}

	/**
	 * {@inheritDoc}
	 */
	public function handle( RequestInterface $request, Closure $next, string $url = '' ): mixed {
		if ( is_user_logged_in() ) {
			return $next( $request );
		}

		if ( empty( $url ) ) {
			$url = wp_login_url( $request->getUrl() );
		}

		$url = apply_filters( 'forgge.middleware.user.logged_in.redirect_url', $url, $request );

		return $this->response_service->redirect( $request )->to( $url );
	}
}
