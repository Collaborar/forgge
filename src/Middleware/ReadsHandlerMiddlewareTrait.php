<?php


namespace Forgge\Middleware;

use Forgge\Helpers\Handler;

/**
 * Describes how a request is handled.
 */
trait ReadsHandlerMiddlewareTrait {
	/**
	 * Get middleware registered with the given handler.
	 *
	 * @param  Handler  $handler
	 * @return string[]
	 */
	protected function getHandlerMiddleware( Handler $handler ) {
		$instance = $handler->make();

		if ( ! $instance instanceof HasControllerMiddlewareInterface ) {
			return [];
		}

		return $instance->getMiddleware( $handler->get()['method'] );
	}
}
