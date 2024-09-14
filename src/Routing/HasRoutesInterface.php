<?php


namespace Forgge\Routing;

/**
 * Interface for HasRoutesTrait
 */
interface HasRoutesInterface {
	/**
	 * Get routes.
	 *
	 * @return RouteInterface[]
	 */
	public function getRoutes();

	/**
	 * Add a route.
	 *
	 * @param  RouteInterface $route
	 * @return void
	 */
	public function addRoute( RouteInterface $route );

	/**
	 * Remove a route.
	 *
	 * @param  RouteInterface $route
	 * @return void
	 */
	public function removeRoute( RouteInterface $route );
}
