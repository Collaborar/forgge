<?php

namespace ForggeTests\Input;

use Mockery;
use Psr\Http\Message\ResponseInterface;
use Forgge\Flash\FlashMiddleware;
use Forgge\Requests\RequestInterface;
use ForggeTestTools\TestCase;

/**
 * @coversDefaultClass \Forgge\Flash\FlashMiddleware
 */
class FlashMiddlewareTest extends TestCase {
	public function set_up() {
		$this->flash = Mockery::mock( \Forgge\Flash\Flash::class );
		$this->subject = new FlashMiddleware( $this->flash );
	}

	public function tear_down() {
		Mockery::close();

		unset( $this->flash );
		unset( $this->subject );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_Disabled_Ignore() {
		$request = Mockery::mock( RequestInterface::class );
		$response = Mockery::mock( ResponseInterface::class );

		$this->flash->shouldReceive( 'enabled' )
			->andReturn( false );
		$this->flash->shouldNotReceive( 'shift' );
		$this->flash->shouldNotReceive( 'save' );

		$result = $this->subject->handle( $request, function( $request ) use ($response) { return $response; } );
		$this->assertSame( $response, $result );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_Enabled_StoresAll() {
		$request = Mockery::mock( RequestInterface::class );
		$response = Mockery::mock( ResponseInterface::class );

		$this->flash->shouldReceive( 'enabled' )
			->andReturn( true )
			->ordered();
		$this->flash->shouldReceive( 'shift' )
			->ordered();
		$this->flash->shouldReceive( 'save' )
			->ordered();

		$result = $this->subject->handle( $request, function( $request ) use ($response) { return $response; } );
		$this->assertSame( $response, $result );
	}
}
