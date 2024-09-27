<?php


namespace Forgge\Routing;

use Pimple\Container;
use Forgge\Routing\Conditions\ConditionFactory;
use Forgge\ServiceProviders\ExtendsConfigTrait;
use Forgge\ServiceProviders\ServiceProviderInterface;

/**
 * Provide routing dependencies
 *
 * @codeCoverageIgnore
 */
class RoutingServiceProvider implements ServiceProviderInterface {
	use ExtendsConfigTrait;

	/**
	 * Key=>Class dictionary of condition types
	 *
	 * @var array<string, string>
	 */
	protected static $condition_types = [
		'url' => Conditions\UrlCondition::class,
		'custom' => Conditions\CustomCondition::class,
		'multiple' => Conditions\MultipleCondition::class,
		'negate' => Conditions\NegateCondition::class,
		'post_id' => Conditions\PostIdCondition::class,
		'post_slug' => Conditions\PostSlugCondition::class,
		'post_status' => Conditions\PostStatusCondition::class,
		'post_template' => Conditions\PostTemplateCondition::class,
		'post_type' => Conditions\PostTypeCondition::class,
		'query_var' => Conditions\QueryVarCondition::class,
		'ajax' => Conditions\AjaxCondition::class,
		'admin' => Conditions\AdminCondition::class,
		'rest' => Conditions\RestCondition::class,
	];

	/**
	 * {@inheritDoc}
	 */
	public function register( Container $container ): void {
		$namespace = $container[ FORGGE_CONFIG_KEY ]['namespace'];

		$this->extendConfig( $container, 'routes', [
			'web' => [
				'definitions' => '',
				'attributes'  => [
					'middleware' => ['web'],
					'namespace' => $namespace . 'Controllers\\Web\\',
					'handler' => 'Forgge\\Controllers\\WordPressController@handle',
				],
			],
			'admin' => [
				'definitions' => '',
				'attributes'  => [
					'middleware' => ['admin'],
					'namespace' => $namespace . 'Controllers\\Admin\\',
				],
			],
			'ajax' => [
				'definitions' => '',
				'attributes'  => [
					'middleware' => ['ajax'],
					'namespace' => $namespace . 'Controllers\\Ajax\\',
				],
			],
			'rest' => [
				'definitions' => '',
				'attributes'  => [
					'middleware' => ['rest'],
					'namespace' => $namespace . 'Controllers\\Rest\\',
				],
			],
		] );

		/** @var Container $container */
		$container[ FORGGE_ROUTING_CONDITION_TYPES_KEY ] = static::$condition_types;

		$container[ FORGGE_ROUTING_ROUTER_KEY ] = function ( $c ) {
			return new Router(
				$c[ FORGGE_ROUTING_CONDITIONS_CONDITION_FACTORY_KEY ],
				$c[ FORGGE_HELPERS_HANDLER_FACTORY_KEY ]
			);
		};

		$container[ FORGGE_ROUTING_CONDITIONS_CONDITION_FACTORY_KEY ] = function ( $c ) {
			return new ConditionFactory( $c[ FORGGE_ROUTING_CONDITION_TYPES_KEY ] );
		};

		$container[ FORGGE_ROUTING_ROUTE_BLUEPRINT_KEY ] = $container->factory( function ( $c ) {
			return new RouteBlueprint( $c[ FORGGE_ROUTING_ROUTER_KEY ], $c[ FORGGE_VIEW_SERVICE_KEY ] );
		} );

		$app = $container[ FORGGE_APPLICATION_KEY ];
		$app->alias( 'router', FORGGE_ROUTING_ROUTER_KEY );
		$app->alias( 'route', FORGGE_ROUTING_ROUTE_BLUEPRINT_KEY );
		$app->alias( 'routeUrl', FORGGE_ROUTING_ROUTER_KEY, 'getRouteUrl' );
	}

	/**
	 * {@inheritDoc}
	 */
	public function bootstrap( Container $container ): void {
		// Nothing to bootstrap.
	}
}
