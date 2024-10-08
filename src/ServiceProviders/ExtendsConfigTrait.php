<?php


namespace Forgge\ServiceProviders;

use Pimple\Container;
use Forgge\Support\Arr;

/**
 * Allows objects to extend the config.
 */
trait ExtendsConfigTrait {
	/**
	 * Recursively replace default values with the passed config.
	 * - If either value is not an array, the config value will be used.
	 * - If both are an indexed array, the config value will be used.
	 * - If either is a keyed array, array_replace will be used with config having priority.
	 *
	 * @param  mixed $default
	 * @param  mixed $config
	 * @return mixed
	 */
	protected function replaceConfig( mixed $default, mixed $config ): mixed {
		if ( ! is_array( $default ) || ! is_array( $config ) ) {
			return $config;
		}

		$default_is_indexed = array_keys( $default ) === range( 0, count( $default ) - 1 );
		$config_is_indexed = array_keys( $config ) === range( 0, count( $config ) - 1 );

		if ( $default_is_indexed && $config_is_indexed ) {
			return $config;
		}

		$result = $default;

		foreach ( $config as $key => $value ) {
			$result[ $key ] = $this->replaceConfig( Arr::get( $default, $key ), $value );
		}

		return $result;
	}

	/**
	 * Extends the Forgge config in the container with a new key.
	 *
	 * @param  Container $container
	 * @param  string    $key
	 * @param  mixed     $default
	 * @return void
	 */
	public function extendConfig( Container $container, string $key, mixed $default ): void {
		$config = isset( $container[ FORGGE_CONFIG_KEY ] ) ? $container[ FORGGE_CONFIG_KEY ] : [];
		$config = Arr::get( $config, $key, $default );

		$container[ FORGGE_CONFIG_KEY ] = array_merge(
			$container[ FORGGE_CONFIG_KEY ],
			[$key => $this->replaceConfig( $default, $config )]
		);
	}
}
