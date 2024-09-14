<?php


namespace Forgge\View;

/**
 * Interface that view finders must implement.
 */
interface ViewFinderInterface {
	/**
	 * Check if a view exists.
	 *
	 * @param  string  $view
	 * @returnbool
	 */
	public function exists( $view );

	/**
	 * Return a canonical string representation of the view name.
	 *
	 * @param  string $view
	 * @return string
	 */
	public function canonical( $view );
}
