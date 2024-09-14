<?php

namespace ForggeTests\Routing\Conditions;

use Mockery;
use Forgge\Routing\Conditions\CustomCondition;
use Forgge\Routing\Conditions\MultipleCondition;
use Forgge\Requests\RequestInterface;
use ForggeTestTools\TestCase;

/**
 * @coversDefaultClass \Forgge\Routing\Conditions\MultipleCondition
 */
class MultipleConditionTest extends TestCase {
	/**
	 * @covers ::isSatisfied
	 */
	public function testIsSatisfied() {
		$condition1 = new CustomCondition( '__return_true' );
		$condition2 = new CustomCondition( '__return_false' );
		$request = Mockery::mock( RequestInterface::class )->shouldIgnoreMissing();

		$subject1 = new MultipleCondition( [$condition1] );
		$this->assertTrue( $subject1->isSatisfied( $request ) );

		$subject2 = new MultipleCondition( [$condition2] );
		$this->assertFalse( $subject2->isSatisfied( $request ) );

		$subject3 = new MultipleCondition( [$condition1, $condition2] );
		$this->assertFalse( $subject3->isSatisfied( $request ) );
	}

	/**
	 * @covers ::getArguments
	 */
	public function testGetArguments() {
		$condition1 = new CustomCondition( '__return_true', 'custom_arg_1', 'custom_arg_2' );
		$condition2 = new CustomCondition( function() { return false; }, 'custom_arg_3' );
		$request = Mockery::mock( RequestInterface::class )->shouldIgnoreMissing();

		$subject = new MultipleCondition( [$condition1, $condition2] );

		$this->assertEquals( ['custom_arg_1', 'custom_arg_2', 'custom_arg_3'], $subject->getArguments( $request ) );
	}
}
