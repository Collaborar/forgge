<?php

namespace ForggeTests\Routing\Conditions;

use Mockery;
use Forgge\Routing\Conditions\ConditionInterface;
use Forgge\Requests\RequestInterface;
use Forgge\Routing\Conditions\NegateCondition;
use ForggeTestTools\TestCase;

/**
 * @coversDefaultClass \Forgge\Routing\Conditions\NegateCondition
 */
class NegateConditionTest extends TestCase {
	/**
	 * @covers ::isSatisfied
	 */
	public function testIsSatisfied() {
		$request = Mockery::mock( RequestInterface::class )->shouldIgnoreMissing();
		$condition = Mockery::mock( ConditionInterface::class );

		$condition->shouldReceive( 'isSatisfied' )
			->with( $request )
			->andReturn( true );

		$subject = new NegateCondition( $condition );

		$this->assertFalse( $subject->isSatisfied( $request ) );

		$condition = Mockery::mock( ConditionInterface::class );

		$condition->shouldReceive( 'isSatisfied' )
			->with( $request )
			->andReturn( false );

		$subject = new NegateCondition( $condition );

		$this->assertTrue( $subject->isSatisfied( $request ) );
	}

	/**
	 * @covers ::getArguments
	 */
	public function testGetArguments() {
		$request = Mockery::mock( RequestInterface::class )->shouldIgnoreMissing();
		$condition = Mockery::mock( ConditionInterface::class );

		$condition->shouldReceive( 'getArguments' )
			->with( $request )
			->andReturn( ['foo'] );

		$subject = new NegateCondition( $condition );

		$this->assertEquals( ['foo'], $subject->getArguments( $request ) );
	}
}
