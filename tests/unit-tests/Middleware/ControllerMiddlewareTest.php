<?php

namespace ForggeTests\Middleware;

use Mockery;
use Forgge\Middleware\ControllerMiddleware;
use ForggeTestTools\TestCase;

/**
 * @coversDefaultClass \Forgge\Middleware\ControllerMiddleware
 */
class ControllerMiddlewareTest extends TestCase {
	public function tear_down() {
		Mockery::close();
	}

	/**
	 * @covers ::appliesTo
	 */
	public function testAppliesTo_Empty_All() {
		$subject = new ControllerMiddleware( [] );

		$this->assertTrue( $subject->appliesTo( 'foo' ) );
	}

	/**
	 * @covers ::appliesTo
	 */
	public function testAppliesTo_Only() {
		$subject = new ControllerMiddleware( [] );

		$subject->only( 'foo' );

		$this->assertTrue( $subject->appliesTo( 'foo' ) );
		$this->assertFalse( $subject->appliesTo( 'bar' ) );
	}

	/**
	 * @covers ::appliesTo
	 */
	public function testAppliesTo_Except() {
		$subject = new ControllerMiddleware( [] );

		$subject->except( 'foo' );

		$this->assertFalse( $subject->appliesTo( 'foo' ) );
		$this->assertTrue( $subject->appliesTo( 'bar' ) );
	}

	/**
	 * @covers ::appliesTo
	 */
	public function testAppliesTo_OnlyExcept() {
		$subject = new ControllerMiddleware( [] );

		$subject->only( ['foo', 'bar'] )->except( ['bar', 'baz'] );

		$this->assertTrue( $subject->appliesTo( 'foo' ) );
		$this->assertFalse( $subject->appliesTo( 'bar' ) );
		$this->assertFalse( $subject->appliesTo( 'baz' ) );
		$this->assertFalse( $subject->appliesTo( 'foobarbaz' ) );
	}
}
