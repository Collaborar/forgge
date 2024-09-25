<?php


namespace Forgge\Application;

use Pimple\Container;
use Forgge\Helpers\HandlerFactory;
use Forgge\Helpers\MixedType;
use Forgge\ServiceProviders\ExtendsConfigTrait;
use Forgge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide application dependencies.
 *
 * @codeCoverageIgnore
 */
class ApplicationServiceProvider implements ServiceProviderInterface {
	use ExtendsConfigTrait;

	/**
	 * {@inheritDoc}
	 */
	public function register( Container $container ): void {
		$this->extendConfig( $container, 'providers', [] );
		$this->extendConfig( $container, 'namespace', 'App\\' );

		$upload_dir = wp_upload_dir();
		$cache_dir = MixedType::addTrailingSlash( $upload_dir['basedir'] ) . 'forgge' . DIRECTORY_SEPARATOR . 'cache';
		$this->extendConfig( $container, 'cache', [
			'path' => $cache_dir,
		] );

		$container[ FORGGE_APPLICATION_GENERIC_FACTORY_KEY ] = fn ( Container $c ): GenericFactory =>
			new GenericFactory( $c );

		$container[ FORGGE_APPLICATION_CLOSURE_FACTORY_KEY ] = fn ( Container $c ): ClosureFactory =>
			new ClosureFactory( $c[ FORGGE_APPLICATION_GENERIC_FACTORY_KEY ] );

		$container[ FORGGE_HELPERS_HANDLER_FACTORY_KEY ] = fn ( Container $c ): HandlerFactory =>
			new HandlerFactory( $c[ FORGGE_APPLICATION_GENERIC_FACTORY_KEY ] );

		$container[ FORGGE_APPLICATION_FILESYSTEM_KEY ] = function ( $c ): object {
			global $wp_filesystem;

			/** @psalm-suppress MissingFile */
			require_once ABSPATH . '/wp-admin/includes/file.php';

			WP_Filesystem();

			return $wp_filesystem;
		};

		$app = $container[ FORGGE_APPLICATION_KEY ];
		$app->alias( 'app', FORGGE_APPLICATION_KEY );
		$app->alias( 'closure', FORGGE_APPLICATION_CLOSURE_FACTORY_KEY );
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( Container $container ): void {
		$cache_dir = $container[ FORGGE_CONFIG_KEY ]['cache']['path'];
		wp_mkdir_p( $cache_dir );
	}
}
