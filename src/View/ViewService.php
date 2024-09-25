<?php


namespace Forgge\View;

use Closure;
use Forgge\Helpers\Handler;
use Forgge\Helpers\HandlerFactory;
use Forgge\Helpers\MixedType;

/**
 * Provide general view-related functionality.
 */
class ViewService {
	/**
	 * Configuration.
	 *
	 * @var array<string, mixed>
	 */
	protected array $config = [];

	/**
	 * View engine.
	 *
	 * @var ViewEngineInterface
	 */
	protected ?ViewEngineInterface $engine = null;

	/**
	 * Handler factory.
	 *
	 * @var HandlerFactory
	 */
	protected ?HandlerFactory $handler_factory = null;

	/**
	 * Global variables.
	 *
	 * @var array
	 */
	protected array $globals = [];

	/**
	 * View composers.
	 *
	 * @var array
	 */
	protected array $composers = [];

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param array<string, mixed> $config
	 * @param ViewEngineInterface  $engine
	 * @param HandlerFactory       $handler_factory
	 */
	public function __construct( array $config, ViewEngineInterface $engine, HandlerFactory $handler_factory ) {
		$this->config = $config;
		$this->engine = $engine;
		$this->handler_factory = $handler_factory;
	}

	/**
	 * Get global variables.
	 *
	 * @return array
	 */
	public function getGlobals(): array {
		return $this->globals;
	}

	/**
	 * Set a global variable.
	 *
	 * @param  string $key
	 * @param  mixed  $value
	 * @return void
	 */
	public function addGlobal( $key, $value ): void {
		$this->globals[ $key ] = $value;
	}

	/**
	 * Set an array of global variables.
	 *
	 * @param  array $globals
	 * @return void
	 */
	public function addGlobals( array $globals ): void {
		foreach ( $globals as $key => $value ) {
			$this->addGlobal( $key, $value );
		}
	}

	/**
	 * Get view composer.
	 *
	 * @param  string    $view
	 * @return Handler[]
	 */
	public function getComposersForView( string $view ): array {
		$view = $this->engine->canonical( $view );

		$composers = [];

		foreach ( $this->composers as $composer ) {
			if ( in_array( $view, $composer['views'], true ) ) {
				$composers[] = $composer['composer'];
			}
		}

		return $composers;
	}

	/**
	 * Add view composer.
	 *
	 * @param  string|string[] $views
	 * @param  string|Closure  $composer
	 * @return void
	 */
	public function addComposer( string|array $views, string|Closure $composer ): void {
		$views = array_map( function ( $view ) {
			return $this->engine->canonical( $view );
		}, MixedType::toArray( $views ) );

		$handler = $this->handler_factory->make( $composer, 'compose', $this->config['namespace'] );

		$this->composers[] = [
			'views' => $views,
			'composer' => $handler,
		];
	}

	/**
	 * Composes a view instance with contexts in the following order: Global, Composers, Local.
	 *
	 * @param  ViewInterface $view
	 * @return void
	 */
	public function compose( ViewInterface $view ): void {
		$global = ['global' => $this->getGlobals()];
		$local = $view->getContext();

		$view->with( $global );

		$composers = $this->getComposersForView( $view->getName() );
		foreach ( $composers as $composer ) {
			$composer->execute( $view );
		}

		$view->with( $local );
	}

	/**
	 * Check if a view exists.
	 *
	 * @param  string  $view
	 * @return bool
	 */
	public function exists( string $view ): bool {
		return $this->engine->exists( $view );
	}

	/**
	 * Return a canonical string representation of the view name.
	 *
	 * @param  string $view
	 * @return string
	 */
	public function canonical( string $view ): string {
		return $this->engine->canonical( $view );
	}

	/**
	 * Create a view instance from the first view name that exists.
	 *
	 * @param  string|string[] $views
	 * @return ViewInterface
	 */
	public function make( string|array $views ): ViewInterface {
		return $this->engine->make( MixedType::toArray( $views ) );
	}

	/**
	 * Trigger core hooks for a partial, if any.
	 *
	 * @codeCoverageIgnore
	 * @param  string $name
	 * @return void
	 */
	public function triggerPartialHooks( string $name ): void {
		if ( ! function_exists( 'apply_filters' ) ) {
			// We are not in a WordPress environment - skip triggering hooks.
			return;
		}

		$core_partial = '/^(header|sidebar|footer)(?:-(.*?))?(\.|$)/i';
		$matches = [];
		$is_partial = preg_match( $core_partial, $name, $matches );

		if ( $is_partial && apply_filters( "forgge.partials.{$matches[1]}.hook", true ) ) {
			do_action( "get_{$matches[1]}", $matches[2] );
		}
	}

	/**
	 * Render a view.
	 *
	 * @codeCoverageIgnore
	 * @param  string|string[]      $views
	 * @param  array<string, mixed> $context
	 * @return void
	 */
	public function render( string|array $views, array $context = [] ): void {
		$view = $this->make( $views )->with( $context );
		$this->triggerPartialHooks( $view->getName() );
		echo $view->toString();
	}
}
