<?php


namespace Forgge\Application;

use Forgge\Exceptions\ConfigurationException;

/**
 * Provides static access to an Application instance.
 *
 * @mixin ApplicationMixin
 */
trait ApplicationTrait {
	/**
	 * Application instance.
	 *
	 * @var Application|null
	 */
	public static $instance = null;

	/**
	 * Make and assign a new application instance.
	 *
	 * @return Application
	 */
	public static function make(): Application {
		static::setApplication( Application::make() );

		return static::getApplication();
	}

	/**
	 * Get the Application instance.
	 *
	 * @codeCoverageIgnore
	 * @return Application|null
	 */
	public static function getApplication(): ?Application {
		return static::$instance;
	}

	/**
	 * Set the Application instance.
	 *
	 * @codeCoverageIgnore
	 * @param  Application|null $application
	 * @return void
	 */
	public static function setApplication( ?Application $application ): void {
		static::$instance = $application;
	}

	/**
	 * Invoke any matching instance method for the static method being called.
	 *
	 * @param  string $method
	 * @param  array  $parameters
	 * @return mixed
	 */
	public static function __callStatic( string $method, array $parameters ): mixed {
		$application = static::getApplication();

		if ( ! $application ) {
			throw new ConfigurationException(
				'Application instance not created in ' . static::class . '. ' .
				'Did you miss to call ' . static::class . '::make()?'
			);
		}

		return $application->$method( ...$parameters );
	}
}
