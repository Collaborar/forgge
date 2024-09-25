<?php


namespace Forgge\View;

use GuzzleHttp\Psr7;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Render a view file with php.
 */
class PhpView implements ViewInterface {
	use HasNameTrait, HasContextTrait;

	/**
	 * PHP view engine.
	 *
	 * @var PhpViewEngine
	 */
	protected ?PhpViewEngine $engine = null;

	/**
	 * Filepath to view.
	 *
	 * @var string
	 */
	protected string $filepath = '';

	/**
	 * Layout to use.
	 *
	 * @var ViewInterface|null
	 */
	protected ?ViewInterface $layout = null;

	/**
	 * Constructor.
	 *
	 * @codeCoverageIgnore
	 * @param PhpViewEngine $engine
	 */
	public function __construct( PhpViewEngine $engine ) {
		$this->engine = $engine;
	}

	/**
	 * Get filepath.
	 *
	 * @return string
	 */
	public function getFilepath(): string {
		return $this->filepath;
	}

	/**
	 * Set filepath.
	 *
	 * @param  string $filepath
	 * @return static $this
	 */
	public function setFilepath( $filepath ): self {
		$this->filepath = $filepath;
		return $this;
	}

	/**
	 * Get layout.
	 *
	 * @return ViewInterface|null
	 */
	public function getLayout(): ?ViewInterface {
		return $this->layout;
	}

	/**
	 * Set layout.
	 *
	 * @param  ViewInterface|null $layout
	 * @return static             $this
	 */
	public function setLayout( ?ViewInterface $layout ): self {
		$this->layout = $layout;
		return $this;
	}

	/**
	 * {@inheritDoc}
	 * @throws ViewException
	 */
	public function toString(): string {
		if ( empty( $this->getName() ) ) {
			throw new ViewException( 'View must have a name.' );
		}

		if ( empty( $this->getFilepath() ) ) {
			throw new ViewException( 'View must have a filepath.' );
		}

		$this->engine->pushLayoutContent( $this );

		if ( $this->getLayout() !== null ) {
			return $this->getLayout()->toString();
		}

		return $this->engine->getLayoutContent();
	}

	/**
	 * {@inheritDoc}
	 * @throws ViewException
	 */
	public function toResponse(): ResponseInterface {
		return (new Response())
			->withHeader( 'Content-Type', 'text/html' )
			->withBody( Psr7\Utils::streamFor( $this->toString() ) );
	}
}
