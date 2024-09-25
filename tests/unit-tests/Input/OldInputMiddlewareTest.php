<?php

namespace ForggeTests\Input;

use Mockery;
use Psr\Http\Message\ResponseInterface;
use Forgge\Input\OldInput;
use Forgge\Input\OldInputMiddleware;
use Forgge\Requests\RequestInterface;
use ForggeTestTools\TestCase;

/**
 * @coversDefaultClass \Forgge\Input\OldInputMiddleware
 */
class OldInputMiddlewareTest extends TestCase {
	public function set_up() {
		$this->old_input = Mockery::mock( OldInput::class );
		$this->subject = new OldInputMiddleware( $this->old_input );
	}

	public function tear_down() {
		Mockery::close();

		unset( $this->old_input );
		unset( $this->subject );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_DisabledPostRequest_Ignore() {
		$request = Mockery::mock( RequestInterface::class );
		$response = Mockery::mock( ResponseInterface::class );

		$this->old_input->shouldReceive( 'enabled' )
			->andReturn( false );
		$this->old_input->shouldNotReceive( 'set' );

		$result = $this->subject->handle( $request, function( $request ) use ($response) { return $response; } );
		$this->assertSame( $response, $result );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_EnabledPostRequest_StoresAll() {
		$expected = ['foo' => 'bar'];
		$request = Mockery::mock( RequestInterface::class );
		$response = Mockery::mock( ResponseInterface::class );

		$request->shouldReceive( 'isPost' )
			->andReturn( true );

		$request->shouldReceive( 'body' )
			->andReturn( $expected );

		$this->old_input->shouldReceive( 'enabled' )
			->andReturn( true );
		$this->old_input->shouldReceive( 'set' )
			->with( $expected )
			->once();

		$result = $this->subject->handle( $request, function( $request ) use ($response) { return $response; } );
		$this->assertSame( $response, $result );
	}

	/**
	 * @covers ::handle
	 */
	public function testHandle_EnabledGetRequest_Ignore() {
		$request = Mockery::mock( RequestInterface::class );
		$response = Mockery::mock( ResponseInterface::class );

		$request->shouldReceive( 'isPost' )
			->andReturn( false );

		$this->old_input->shouldReceive( 'enabled' )
			->andReturn( true );
		$this->old_input->shouldNotReceive( 'set' );

		$result = $this->subject->handle( $request, function( $request ) use ($response) { return $response; } );
		$this->assertSame( $response, $result );
	}
}
