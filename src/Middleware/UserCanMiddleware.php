<?php


namespace Forgge\Middleware;

use Closure;
use Forgge\Requests\RequestInterface;
use Forgge\Responses\ResponseService;

/**
 * Redirect users who do not have a capability to a specific URL.
 */
class UserCanMiddleware {
	/**
	 * Response service.
	 *
	 * @var ResponseService
	 */
	protected $response_service = null;

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
	public function handle( RequestInterface $request, Closure $next, $capability = '', $object_id = '0', $url = '' ) {
		$capability = apply_filters( 'forgge.middleware.user.can.capability', $capability, $request );
		$object_id = apply_filters( 'forgge.middleware.user.can.object_id', (int) $object_id, $capability, $request );
		$args = [$capability];

		if ( $object_id !== 0 ) {
			$args[] = $object_id;
		}

		if ( call_user_func_array( 'current_user_can', $args ) ) {
			return $next( $request );
		}

		if ( empty( $url ) ) {
			$url = home_url();
		}

		$url = apply_filters( 'forgge.middleware.user.can.redirect_url', $url, $request );

		return $this->response_service->redirect( $request )->to( $url );
	}
}
