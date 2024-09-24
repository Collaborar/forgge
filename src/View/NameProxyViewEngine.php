<?php


namespace Forgge\View;

use Forgge\Application\Application;

/**
 * Render view files with different engines depending on their filename
 */
class NameProxyViewEngine implements ViewEngineInterface {
	/**
	 * Container key of default engine to use
	 *
	 * @var string
	 */
	protected string $default = FORGGE_VIEW_PHP_VIEW_ENGINE_KEY;

	/**
	 * Application.
	 *
	 * @var Application
	 */
	protected ?Application $app = null;

	/**
	 * Array of filename_suffix=>engine_container_key bindings
	 *
	 * @var array
	 */
	protected array $bindings = [];

	/**
	 * Constructor
	 *
	 * @param Application $app
	 * @param array       $bindings
	 * @param string      $default
	 */
	public function __construct( Application $app, array $bindings, string $default = '' ) {
		$this->app = $app;
		$this->bindings = $bindings;

		if ( ! empty( $default ) ) {
			$this->default = $default;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function exists( string $view ): bool {
		$engine_key = $this->getBindingForFile( $view );
		$engine = $this->app->resolve( $engine_key );
		return $engine->exists( $view );
	}

	/**
	 * {@inheritDoc}
	 */
	public function canonical( string $view ): string {
		$engine_key = $this->getBindingForFile( $view );
		$engine = $this->app->resolve( $engine_key );
		return $engine->canonical( $view );
	}

	/**
	 * {@inheritDoc}
	 * @throws ViewNotFoundException
	 */
	public function make( array $views ): ViewInterface {
		foreach ( $views as $view ) {
			if ( $this->exists( $view ) ) {
				$engine_key = $this->getBindingForFile( $view );
				$engine = $this->app->resolve( $engine_key );
				return $engine->make( [$view] );
			}
		}

		throw new ViewNotFoundException( 'View not found for "' . implode( ', ', $views ) . '"' );
	}

	/**
	 * Get the default binding
	 *
	 * @return string $binding
	 */
	public function getDefaultBinding(): string {
		return $this->default;
	}

	/**
	 * Get all bindings
	 *
	 * @return array  $bindings
	 */
	public function getBindings(): array {
		return $this->bindings;
	}

	/**
	 * Get the engine key binding for a specific file
	 *
	 * @param  string $file
	 * @return string
	 */
	public function getBindingForFile( string $file ): string {
		$engine_key = $this->default;

		foreach ( $this->bindings as $suffix => $engine ) {
			if ( substr( $file, -strlen( $suffix ) ) === $suffix ) {
				$engine_key = $engine;
				break;
			}
		}

		return $engine_key;
	}
}
