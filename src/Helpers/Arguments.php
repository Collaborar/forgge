<?php


namespace Forgge\Helpers;

/**
 * A collection of tools dealing with urls
 */
class Arguments {
	/**
	 * Get a closure which will flip preceding optional arguments around.
	 * @example list( $argument1, $argument2 ) = Arguments::flip( $argument1, $argument2 );
	 *
	 * @return array
	 */
	public static function flip( ...$arguments ): array {
		// $arguments = func_get_args();
		$first_null = array_search( null, $arguments, true );

		if ( $first_null === false ) {
			return $arguments;
		}

		// Support int keys only.
		$first_null = (int) $first_null;

		$arguments = array_values( array_merge(
			array_slice( $arguments, $first_null ),
			array_slice( $arguments, 0, $first_null )
		) );

		return $arguments;
	}
}
