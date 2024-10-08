<?php

namespace ForggeTests\Routing;

use Mockery;
use Forgge\Exceptions\ConfigurationException;
use Forgge\Requests\RequestInterface;
use Forgge\Routing\Route;
use Forgge\Routing\Conditions\ConditionInterface;
use ForggeTestTools\TestCase;

/**
 * @coversDefaultClass \Forgge\Routing\Route
 */
class RouteTest extends TestCase {
	public function tear_down() {
		Mockery::close();
	}

	/**
	 * @covers ::isSatisfied
	 */
	public function testIsSatisfied() {
		$request = Mockery::mock( RequestInterface::class );
		$condition = Mockery::mock( ConditionInterface::class );

		$request->shouldReceive( 'getMethod' )
			->andReturn( 'FOO' );

		$condition->shouldReceive( 'isSatisfied' )
			->andReturn( true );

		$subject = (new Route())->attributes( [
			'methods' => ['BAR'],
			'condition' => $condition,
		] );
		$this->assertFalse( $subject->isSatisfied( $request ) );

		$subject = (new Route())->attributes( [
			'methods' => ['FOO'],
			'condition' => $condition,
		] );
		$this->assertTrue( $subject->isSatisfied( $request ) );

		$subject = (new Route())->attributes( [
			'methods' => ['FOO', 'BAR'],
			'condition' => $condition,
		] );

		$this->assertTrue( $subject->isSatisfied( $request ) );
	}

	/**
	 * @covers ::isSatisfied
	 */
	public function testIsSatisfied_ConditionFalse_False() {
		$request = Mockery::mock( RequestInterface::class );
		$condition = Mockery::mock( ConditionInterface::class );

		$request->shouldReceive( 'getMethod' )
			->andReturn( 'FOO' );

		$condition->shouldReceive( 'isSatisfied' )
			->andReturn( false );

		$subject = (new Route())->attributes( [
			'methods' => ['FOO'],
			'condition' => $condition,
		] );

		$this->assertFalse( $subject->isSatisfied( $request ) );
	}

	/**
	 * @covers ::isSatisfied
	 */
	public function testIsSatisfied_NoCondition_Exception() {
		$request = Mockery::mock( RequestInterface::class );

		$request->shouldReceive( 'getMethod' )
			->andReturn( 'FOO' );

		$subject = (new Route())->attributes( [
			'methods' => ['FOO'],
		] );

		$this->expectException( ConfigurationException::class );
		$this->expectExceptionMessage( 'Route does not have a condition' );
		$subject->isSatisfied( $request );
	}

	/**
	 * @covers ::getArguments
	 */
	public function testGetArguments_PassThroughCondition() {
		$request = Mockery::mock( RequestInterface::class );
		$condition = Mockery::mock( ConditionInterface::class );
		$expected = ['foo'];

		$condition->shouldReceive( 'getArguments' )
			->with( $request )
			->andReturn( $expected );

		$subject = (new Route())->attributes( [
			'condition' => $condition,
		] );

		$this->assertSame( $expected, $subject->getArguments( $request ) );
	}

	/**
	 * @covers ::getArguments
	 */
	public function testGetArguments_NoCondition_Exception() {
		$request = Mockery::mock( RequestInterface::class );

		$subject = (new Route())->attributes( [
			'methods' => ['FOO'],
		] );

		$this->expectException( ConfigurationException::class );
		$this->expectExceptionMessage( 'Route does not have a condition' );
		$subject->getArguments( $request );
	}
}
