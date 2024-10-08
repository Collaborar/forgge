<?php

namespace ForggeTests\Helpers;

use Forgge\Helpers\Arguments;
use ForggeTestTools\TestCase;

/**
 * @coversDefaultClass \Forgge\Helpers\Arguments
 */
class ArgumentsTest extends TestCase {
	/**
	 * @covers ::flip
	 */
	public function testFlip() {
		$this->assertEquals( ['foo'], Arguments::flip( 'foo' ) );
		$this->assertEquals( [null, 'foo'], Arguments::flip( 'foo', null ) );
		$this->assertEquals( [null, null, 'foo'], Arguments::flip( 'foo', null, null ) );
		$this->assertEquals( [null, null, 'foo', 'bar'], Arguments::flip( 'foo', 'bar', null, null ) );
		$this->assertEquals( [null, 'foo', 'bar', 'baz'], Arguments::flip( 'foo', 'bar', 'baz', null ) );
		$this->assertEquals( ['foo', 'bar', 'baz', 'foobar'], Arguments::flip( 'foo', 'bar', 'baz', 'foobar' ) );
	}
}
