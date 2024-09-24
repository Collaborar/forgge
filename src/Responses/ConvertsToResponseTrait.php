<?php


namespace Forgge\Responses;

/**
 * Converts values to a response.
 */
trait ConvertsToResponseTrait {
	/**
	 * Get a Response Service instance.
	 *
	 * @return ResponseService
	 */
	abstract protected function getResponseService(): ResponseService;

	/**
	 * Convert a user returned response to a ResponseInterface instance if possible.
	 * Return the original value if unsupported.
	 *
	 * @param  mixed $response
	 * @return mixed
	 */
	protected function toResponse( mixed $response ): mixed {
		if ( is_string( $response ) ) {
			return $this->getResponseService()->output( $response );
		}

		if ( is_array( $response ) ) {
			return $this->getResponseService()->json( $response );
		}

		if ( $response instanceof ResponsableInterface ) {
			return $response->toResponse();
		}

		return $response;
	}
}
