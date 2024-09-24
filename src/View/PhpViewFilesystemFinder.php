<?php


namespace Forgge\View;

use Forgge\Helpers\MixedType;

/**
 * Render view files with php.
 */
class PhpViewFilesystemFinder implements ViewFinderInterface {
	/**
	 * Custom views directories to check first.
	 *
	 * @var string[]
	 */
	protected array $directories = [];

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param string[] $directories
	 */
	public function __construct( array $directories = [] ) {
		$this->setDirectories( $directories );
	}

	/**
	 * Get the custom views directories.
	 *
	 * @codeCoverageIgnore
	 * @return string[]
	 */
	public function getDirectories(): array {
		return $this->directories;
	}

	/**
	 * Set the custom views directories.
	 *
	 * @codeCoverageIgnore
	 * @param  string[] $directories
	 * @return void
	 */
	public function setDirectories( array $directories ): void {
		$this->directories = array_filter( array_map( [MixedType::class, 'removeTrailingSlash'], $directories ) );
	}

	/**
	 * {@inheritDoc}
	 */
	public function exists( string $view ): bool {
		return ! empty( $this->resolveFilepath( $view ) );
	}

	/**
	 * {@inheritDoc}
	 */
	public function canonical( string $view ): string {
		return $this->resolveFilepath( $view );
	}

	/**
	 * Resolve a view to an absolute filepath.
	 *
	 * @param  string $view
	 * @return string
	 */
	public function resolveFilepath( string $view ): string {
		$file = $this->resolveFromAbsoluteFilepath( $view );

		if ( ! $file ) {
			$file = $this->resolveFromCustomDirectories( $view );
		}

		return $file;
	}

	/**
	 * Resolve a view if it is a valid absolute filepath.
	 *
	 * @param  string $view
	 * @return string
	 */
	protected function resolveFromAbsoluteFilepath( string $view ): string {
		$path = realpath( MixedType::normalizePath( $view ) );

		if ( ! empty( $path ) && ! is_file( $path ) ) {
			$path = '';
		}

		return $path ? $path : '';
	}

	/**
	 * Resolve a view if it exists in the custom views directories.
	 *
	 * @param  string $view
	 * @return string
	 */
	protected function resolveFromCustomDirectories( string $view ): string {
		$directories = $this->getDirectories();

		foreach ( $directories as $directory ) {
			$file = MixedType::normalizePath( $directory . DIRECTORY_SEPARATOR . $view );

			if ( ! is_file( $file ) ) {
				// Try adding a .php extension.
				$file .= '.php';
			}

			$file = realpath( $file );

			if ( $file && is_file( $file ) ) {
				return $file;
			}
		}

		return '';
	}
}
