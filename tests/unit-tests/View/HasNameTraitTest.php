<?php

namespace ForggeTests\View;

use Forgge\View\HasNameTrait;
use ForggeTestTools\TestCase;

/**
 * @coversDefaultClass \Forgge\View\HasNameTrait
 */
class HasNameTraitTest extends TestCase {
	/**
	 * @covers ::getName
	 * @covers ::setName
	 */
	public function testGetNameContext() {
		$subject = $this->getMockForTrait( HasNameTrait::class );
		$expected = 'foo';

		$subject->setName( $expected );
		$this->assertEquals( $expected, $subject->getName() );
	}
}
