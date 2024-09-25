<?php


namespace Forgge\Middleware;

/**
 * Redirect users who do not have a capability to a specific URL.
 */
class ControllerMiddleware {
	/**
	 * Middleware.
	 *
	 * @var string[]
	 */
	protected array $middleware = [];

	/**
	 * Methods the middleware applies to.
	 *
	 * @var string[]
	 */
	protected array $whitelist = [];

	/**
	 * Methods the middleware does not apply to.
	 *
	 * @var string[]
	 */
	protected array $blacklist = [];

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param  string|string[] $middleware
	 */
	public function __construct( string|array $middleware ) {
		$this->middleware = (array) $middleware;
	}

	/**
	 * Get middleware.
	 *
	 * @codeCoverageIgnore
	 * @return string[]
	 */
	public function get(): array {
		return $this->middleware;
	}

	/**
	 * Set methods the middleware should apply to.
	 *
	 * @codeCoverageIgnore
	 * @param  string|string[] $methods
	 * @return static
	 */
	public function only( string|array $methods ): static {
		$this->whitelist = (array) $methods;

		return $this;
	}

	/**
	 * Set methods the middleware should not apply to.
	 *
	 * @codeCoverageIgnore
	 * @param  string|string[] $methods
	 * @return static
	 */
	public function except( string|array $methods ): static {
		$this->blacklist = (array) $methods;

		return $this;
	}

	/**
	 * Get whether the middleware applies to the specified method.
	 *
	 * @param  string $method
	 * @return bool
	 */
	public function appliesTo( string $method ): bool {
		if ( in_array( $method, $this->blacklist, true ) ) {
			return false;
		}

		if ( empty( $this->whitelist ) ) {
			return true;
		}

		return in_array( $method, $this->whitelist, true );
	}
}
