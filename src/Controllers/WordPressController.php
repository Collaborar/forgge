<?php  /** @noinspection PhpUnusedParameterInspection */
namespace Forgge\Controllers;

use Psr\Http\Message\ResponseInterface;
use Forgge\Exceptions\ConfigurationException;
use Forgge\Requests\RequestInterface;
use Forgge\View\ViewService;

/**
 * Handles normal WordPress requests without interfering
 * Useful if you only want to add a middleware to a route without handling the output
 *
 * @codeCoverageIgnore
 */
class WordPressController {
	/**
	 * View service.
	 *
	 * @var ViewService
	 */
	protected ?ViewService $view_service = null;

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param ViewService $view_service
	 */
	public function __construct( ViewService $view_service ) {
		$this->view_service = $view_service;
	}

	/**
	 * Default WordPress handler.
	 *
	 * @param  RequestInterface  $request
	 * @param  string            $view
	 * @return ResponseInterface
	 */
	public function handle( RequestInterface $request, string $view = '' ): ResponseInterface {
		if ( is_admin() || wp_doing_ajax() ) {
			throw new ConfigurationException(
				'Attempted to run the default WordPress controller on an ' .
				'admin or AJAX page. Did you miss to specify a custom handler for ' .
				'a route or accidentally used \App::route()->all() during admin ' .
				'requests?'
			);
		}

		if ( empty( $view ) ) {
			throw new ConfigurationException(
				'No view loaded for default WordPress controller. ' .
				'Did you miss to specify a custom handler for an ajax or admin route?'
			);
		}

		$status_code = http_response_code();

		return $this->view_service->make( $view )
			->toResponse()
			->withStatus( is_int( $status_code ) ? $status_code : 200 );
	}
}
