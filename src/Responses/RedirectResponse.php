<?php


namespace Forgge\Responses;

use GuzzleHttp\Psr7\Response as Psr7Response;
use Psr\Http\Message\ResponseInterface;
use Forgge\Requests\RequestInterface;

/**
 * A collection of tools for the creation of responses
 */
class RedirectResponse extends Psr7Response {
	/**
	 * Current request.
	 *
	 * @var RequestInterface
	 */
	protected $request = null;

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param RequestInterface $request
	 */
	public function __construct( RequestInterface $request ) {
		parent::__construct();
		$this->request = $request;
	}

	/**
	 * Get a response redirecting to a specific url.
	 *
	 * @param  string            $url
	 * @param  int           $status
	 * @return ResponseInterface
	 */
	public function to( $url, $status = 302 ): ResponseInterface {
		return $this
			->withHeader( 'Location', $url )
			->withStatus( $status );
	}

	/**
	 * Get a response redirecting back to the referrer or a fallback.
	 *
	 * @param  string            $fallback
	 * @param  int           $status
	 * @return ResponseInterface
	 */
	public function back( $fallback = '', $status = 302 ) {
		$url = (string) $this->request->getHeaderLine( 'Referer' );

		if ( empty( $url ) ) {
			$url = $fallback;
		}

		if ( empty( $url ) ) {
			$url = $this->request->getUrl();
		}

		return $this->to( $url, $status );
	}
}
