<?php


namespace Forgge\View;

use Pimple\Container;
use Forgge\Helpers\MixedType;
use Forgge\ServiceProviders\ExtendsConfigTrait;
use Forgge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide view dependencies
 *
 * @codeCoverageIgnore
 */
class ViewServiceProvider implements ServiceProviderInterface {
	use ExtendsConfigTrait;

	/**
	 * {@inheritDoc}
	 */
	public function register( $container ) {
		/** @var Container $container */
		$namespace = $container[ FORGGE_CONFIG_KEY ]['namespace'];

		$this->extendConfig( $container, 'views', [get_stylesheet_directory(), get_template_directory()] );

		$this->extendConfig( $container, 'view_composers', [
			'namespace' => $namespace . 'ViewComposers\\',
		] );

		$container[ FORGGE_VIEW_SERVICE_KEY ] = function ( $c ) {
			return new ViewService(
				$c[ FORGGE_CONFIG_KEY ]['view_composers'],
				$c[ FORGGE_VIEW_ENGINE_KEY ],
				$c[ FORGGE_HELPERS_HANDLER_FACTORY_KEY ]
			);
		};

		$container[ FORGGE_VIEW_COMPOSE_ACTION_KEY ] = function ( $c ) {
			return function ( ViewInterface $view ) use ( $c ) {
				$view_service = $c[ FORGGE_VIEW_SERVICE_KEY ];
				$view_service->compose( $view );
				return $view;
			};
		};

		$container[ FORGGE_VIEW_PHP_VIEW_ENGINE_KEY ] = function ( $c ) {
			$finder = new PhpViewFilesystemFinder( MixedType::toArray( $c[ FORGGE_CONFIG_KEY ]['views'] ) );
			return new PhpViewEngine( $c[ FORGGE_VIEW_COMPOSE_ACTION_KEY ], $finder );
		};

		$container[ FORGGE_VIEW_ENGINE_KEY ] = function ( $c ) {
			return $c[ FORGGE_VIEW_PHP_VIEW_ENGINE_KEY ];
		};

		$app = $container[ FORGGE_APPLICATION_KEY ];
		$app->alias( 'views', FORGGE_VIEW_SERVICE_KEY );

		$app->alias( 'view', function () use ( $app ) {
			return call_user_func_array( [$app->views(), 'make'], func_get_args() );
		} );

		$app->alias( 'render', function () use ( $app ) {
			return call_user_func_array( [$app->views(), 'render'], func_get_args() );
		} );

		$app->alias( 'layoutContent', function () use ( $app ) {
			/** @var PhpViewEngine $engine */
			$engine = $app->resolve( FORGGE_VIEW_PHP_VIEW_ENGINE_KEY );

			echo $engine->getLayoutContent();
		} );
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( $container ) {
		// Nothing to bootstrap.
	}
}
