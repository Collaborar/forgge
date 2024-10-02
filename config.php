<?php


/**
 * Current version.
 */
if ( ! defined( 'FORGGE_VERSION' ) ) {
	define( 'FORGGE_VERSION', '1.0.0' );
}

/**
 * Absolute path to application's directory.
 */
if ( ! defined( 'FORGGE_DIR' ) ) {
	define( 'FORGGE_DIR', __DIR__ );
}

/**
 * Service container keys.
 */
if ( ! defined( 'FORGGE_CONFIG_KEY' ) ) {
	define( 'FORGGE_CONFIG_KEY', 'forgge.config' );
}

if ( ! defined( 'FORGGE_APPLICATION_KEY' ) ) {
	define( 'FORGGE_APPLICATION_KEY', 'forgge.application.application' );
}

if ( ! defined( 'FORGGE_APPLICATION_GENERIC_FACTORY_KEY' ) ) {
	define( 'FORGGE_APPLICATION_GENERIC_FACTORY_KEY', 'forgge.application.generic_factory' );
}

if ( ! defined( 'FORGGE_APPLICATION_CLOSURE_FACTORY_KEY' ) ) {
	define( 'FORGGE_APPLICATION_CLOSURE_FACTORY_KEY', 'forgge.application.closure_factory' );
}

if ( ! defined( 'FORGGE_APPLICATION_FILESYSTEM_KEY' ) ) {
	define( 'FORGGE_APPLICATION_FILESYSTEM_KEY', 'forgge.application.filesystem' );
}

if ( ! defined( 'FORGGE_HELPERS_HANDLER_FACTORY_KEY' ) ) {
	define( 'FORGGE_HELPERS_HANDLER_FACTORY_KEY', 'forgge.handlers.helper_factory' );
}

if ( ! defined( 'FORGGE_WORDPRESS_HTTP_KERNEL_KEY' ) ) {
	define( 'FORGGE_WORDPRESS_HTTP_KERNEL_KEY', 'forgge.kernels.wordpress_http_kernel' );
}

if ( ! defined( 'FORGGE_SESSION_KEY' ) ) {
	define( 'FORGGE_SESSION_KEY', 'forgge.session' );
}

if ( ! defined( 'FORGGE_REQUEST_KEY' ) ) {
	define( 'FORGGE_REQUEST_KEY', 'forgge.request' );
}

if ( ! defined( 'FORGGE_RESPONSE_KEY' ) ) {
	define( 'FORGGE_RESPONSE_KEY', 'forgge.response' );
}

if ( ! defined( 'FORGGE_EXCEPTIONS_ERROR_HANDLER_KEY' ) ) {
	define( 'FORGGE_EXCEPTIONS_ERROR_HANDLER_KEY', 'forgge.exceptions.error_handler' );
}

if ( ! defined( 'FORGGE_EXCEPTIONS_CONFIGURATION_ERROR_HANDLER_KEY' ) ) {
	define( 'FORGGE_EXCEPTIONS_CONFIGURATION_ERROR_HANDLER_KEY', 'forgge.exceptions.configuration_error_handler' );
}

if ( ! defined( 'FORGGE_RESPONSE_SERVICE_KEY' ) ) {
	define( 'FORGGE_RESPONSE_SERVICE_KEY', 'forgge.responses.response_service' );
}

if ( ! defined( 'FORGGE_ROUTING_ROUTER_KEY' ) ) {
	define( 'FORGGE_ROUTING_ROUTER_KEY', 'forgge.routing.router' );
}

if ( ! defined( 'FORGGE_ROUTING_ROUTE_BLUEPRINT_KEY' ) ) {
	define( 'FORGGE_ROUTING_ROUTE_BLUEPRINT_KEY', 'forgge.routing.route_registrar' );
}

if ( ! defined( 'FORGGE_ROUTING_CONDITIONS_CONDITION_FACTORY_KEY' ) ) {
	define( 'FORGGE_ROUTING_CONDITIONS_CONDITION_FACTORY_KEY', 'forgge.routing.conditions.condition_factory' );
}

if ( ! defined( 'FORGGE_ROUTING_CONDITION_TYPES_KEY' ) ) {
	define( 'FORGGE_ROUTING_CONDITION_TYPES_KEY', 'forgge.routing.conditions.condition_types' );
}

if ( ! defined( 'FORGGE_VIEW_SERVICE_KEY' ) ) {
	define( 'FORGGE_VIEW_SERVICE_KEY', 'forgge.view.view_service' );
}

if ( ! defined( 'FORGGE_VIEW_COMPOSE_ACTION_KEY' ) ) {
	define( 'FORGGE_VIEW_COMPOSE_ACTION_KEY', 'forgge.view.view_compose_action' );
}

if ( ! defined( 'FORGGE_VIEW_ENGINE_KEY' ) ) {
	define( 'FORGGE_VIEW_ENGINE_KEY', 'forgge.view.view_engine' );
}

if ( ! defined( 'FORGGE_VIEW_PHP_VIEW_ENGINE_KEY' ) ) {
	define( 'FORGGE_VIEW_PHP_VIEW_ENGINE_KEY', 'forgge.view.php_view_engine' );
}

if ( ! defined( 'FORGGE_SERVICE_PROVIDERS_KEY' ) ) {
	define( 'FORGGE_SERVICE_PROVIDERS_KEY', 'forgge.service_providers' );
}

if ( ! defined( 'FORGGE_FLASH_KEY' ) ) {
	define( 'FORGGE_FLASH_KEY', 'forgge.flash.flash' );
}

if ( ! defined( 'FORGGE_OLD_INPUT_KEY' ) ) {
	define( 'FORGGE_OLD_INPUT_KEY', 'forgge.old_input.old_input' );
}

if ( ! defined( 'FORGGE_CSRF_KEY' ) ) {
	define( 'FORGGE_CSRF_KEY', 'forgge.csrf.csrf' );
}
