<?php


namespace Forgge\Responses;

use Psr\Http\Message\ResponseInterface;

interface ResponsableInterface {
	/**
	 * Convert to Psr\Http\Message\ResponseInterface.
	 *
	 * @return ResponseInterface
	 */
	public function toResponse(): ResponseInterface;
}
