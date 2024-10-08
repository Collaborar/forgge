<?php

namespace ForggeTests\Routing;

use Mockery;
use Forgge\Exceptions\ConfigurationException;
use Forgge\Requests\RequestInterface;
use Forgge\Routing\Conditions\CanFilterQueryInterface;
use Forgge\Routing\HasQueryFilterTrait;
use ForggeTestTools\TestCase;

/**
 * @coversDefaultClass \Forgge\Routing\HasQueryFilterTrait
 */
class HasQueryFilterTraitTest extends TestCase {
	public function set_up() {
		$this->subject = $this->getMockForTrait( HasQueryFilterTrait::class );
	}

	public function tear_down() {
		Mockery::close();

		unset( $this->subject );
	}

	/**
	 * @covers ::applyQueryFilter
	 */
	public function testApplyQueryFilter_NoFilter_Unfiltered() {
		$expected = ['unfiltered'];
		$request = Mockery::mock( RequestInterface::class )->shouldIgnoreMissing();

		$this->assertEquals( $expected, $this->subject->applyQueryFilter( $request, $expected ) );
	}

	/**
	 * @covers ::applyQueryFilter
	 */
	public function testApplyQueryFilter_CanFilterQueryCondition_FilteredArray() {
		$arguments = ['arg1', 'arg2'];
		$request = Mockery::mock( RequestInterface::class )->shouldIgnoreMissing();
		$condition = Mockery::mock( CanFilterQueryInterface::class );

		$this->subject->expects( $this->exactly(2) )
			->method( 'getAttribute' )
			->will( $this->onConsecutiveCalls(
				function( $query_vars, $arg1, $arg2 ) {
					return array_merge( $query_vars, [$arg1, $arg2] );
				},
				$condition
			) );

		$condition->shouldReceive( 'isSatisfied' )
			  ->andReturn( true );

		$condition->shouldReceive( 'getArguments' )
			  ->andReturn( $arguments );

		$this->assertEquals( ['arg0', 'arg1', 'arg2'], $this->subject->applyQueryFilter( $request, ['arg0'] ) );
	}

	/**
	 * @covers ::applyQueryFilter
	 */
	public function testApplyQueryFilter_NonCanFilterQueryCondition_Exception() {
		$request = Mockery::mock( RequestInterface::class )->shouldIgnoreMissing();

		$this->subject->expects( $this->exactly( 2 ) )
			->method( 'getAttribute' )
			->will( $this->onConsecutiveCalls(
				function() {},
				null
			) );

		$this->expectException( ConfigurationException::class );
		$this->expectExceptionMessage( 'Only routes with a condition implementing the' );
		$this->subject->applyQueryFilter( $request, [] );
	}
}
