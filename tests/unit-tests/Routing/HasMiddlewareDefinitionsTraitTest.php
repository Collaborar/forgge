<?php

namespace ForggeTests\Routing;

use Closure;
use Mockery;
use Forgge\Exceptions\ConfigurationException;
use Forgge\Middleware\HasMiddlewareDefinitionsTrait;
use Forgge\Requests\RequestInterface;
use ForggeTestTools\TestCase;

/**
 * @coversDefaultClass \Forgge\Middleware\HasMiddlewareDefinitionsTrait
 */
class HasMiddlewareDefinitionsTraitTest extends TestCase {
	public function tear_down() {
		Mockery::close();
	}

	/**
	 * @covers ::uniqueMiddleware
	 */
	public function testUniqueMiddleware() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();

		$this->assertEquals(
			[
				'foo',
				['foo', 1],
				['foo', 2]
			],
			$subject->uniqueMiddleware( [
				'foo',
				['foo', 1],
				'foo',
				['foo', 1],
				['foo', 2]
			] )
		);
	}

	/**
	 * @covers ::expandMiddleware
	 */
	public function testExpandMiddleware() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();
		$subject->setMiddleware( [
			'short1' => 'long1',
			'short2' => 'long2',
		] );
		$subject->setMiddlewareGroups( [
			'group' => [
				'short1',
				HasMiddlewareDefinitionsTraitTestMiddlewareStub1::class,
			],
		] );

		$this->assertEquals( [
			['long2'],
			['long1'],
			[HasMiddlewareDefinitionsTraitTestMiddlewareStub1::class],
		], $subject->expandMiddleware( ['short2', 'group'] ) );
	}

	/**
	 * @covers ::expandMiddlewareGroup
	 */
	public function testExpandMiddlewareGroup_Predefined_HasForggeAndGlobalPrepended() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();
		$subject->setMiddleware( [
			'short' => 'long',
			'global-short' => 'global-long',
			'forgge-short' => 'forgge-long',
		] );
		$subject->setMiddlewareGroups( [
			'forgge' => ['forgge-short'],
			'global' => ['global-short'],
			'web' => [],
			'admin' => ['short'],
			'ajax' => ['admin'],
		] );

		$this->assertEquals( [['forgge-long'], ['global-long']], $subject->expandMiddlewareGroup( 'web' ) );
		$this->assertEquals( [['forgge-long'], ['global-long'], ['long']], $subject->expandMiddlewareGroup( 'admin' ) );
		$this->assertEquals( [['forgge-long'], ['global-long'], ['forgge-long'], ['global-long'], ['long']], $subject->expandMiddlewareGroup( 'ajax' ) );
	}

	/**
	 * @covers ::expandMiddlewareGroup
	 */
	public function testExpandMiddlewareGroup_Nested_RecursivelyExpandedGroup() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();
		$subject->setMiddleware( [
			'short' => 'long',
		] );
		$subject->setMiddlewareGroups( [
			'group1' => ['short'],
			'group2' => ['group1'],
			'group3' => ['group2'],
		] );

		$this->assertEquals( [['long']], $subject->expandMiddlewareGroup( 'group3' ) );
	}

	/**
	 * @covers ::expandMiddlewareGroup
	 */
	public function testExpandMiddlewareGroup_Invalid_Exception() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();

		$this->expectException( ConfigurationException::class );
		$this->expectExceptionMessage( 'Unknown middleware group' );
		$subject->expandMiddlewareGroup( 'undefined group' );
	}

	/**
	 * @covers ::expandMiddlewareMolecule
	 * @covers ::expandMiddlewareAtom
	 */
	public function testExpandMiddlewareMolecule_DefinedString_Expanded() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();
		$subject->setMiddleware( [
			'short' => 'long',
		] );

		$this->assertEquals( [['long']], $subject->expandMiddlewareMolecule( 'short' ) );
	}

	/**
	 * @covers ::expandMiddlewareMolecule
	 */
	public function testExpandMiddlewareMolecule_DefinedStringWithArguments_Expanded() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();
		$subject->setMiddleware( [
			'short' => 'long',
		] );

		$this->assertEquals( [['long', 'foo', 123]], $subject->expandMiddlewareMolecule( 'short:foo,123' ) );
	}

	/**
	 * @covers ::expandMiddlewareMolecule
	 * @covers ::expandMiddlewareAtom
	 */
	public function testExpandMiddlewareMolecule_Class_Formatted() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();

		$this->assertEquals(
			[[HasMiddlewareDefinitionsTraitTestMiddlewareStub1::class]],
			$subject->expandMiddlewareMolecule( HasMiddlewareDefinitionsTraitTestMiddlewareStub1::class )
		);
	}

	/**
	 * @covers ::expandMiddlewareMolecule
	 */
	public function testExpandMiddlewareMolecule_Group_Expanded() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();
		$subject->setMiddleware( [
			'short' => 'long',
		] );
		$subject->setMiddlewareGroups( [
			'group' => [
				'short',
				HasMiddlewareDefinitionsTraitTestMiddlewareStub1::class,
			],
		] );

		$this->assertEquals( [
			['long'],
			[HasMiddlewareDefinitionsTraitTestMiddlewareStub1::class],
		], $subject->expandMiddlewareMolecule( 'group' ) );
	}

	/**
	 * @covers ::expandMiddlewareMolecule
	 * @covers ::expandMiddlewareAtom
	 */
	public function testExpandMiddlewareMolecule_UndefinedString_Exception() {
		$subject = new HasMiddlewareDefinitionsTraitTestImplementation();

		$this->expectException( ConfigurationException::class );
		$this->expectExceptionMessage( 'Unknown middleware' );
		$subject->expandMiddlewareMolecule( 'undefined middleware' );
	}
}

class HasMiddlewareDefinitionsTraitTestImplementation {
	use HasMiddlewareDefinitionsTrait;
}

class HasMiddlewareDefinitionsTraitTestMiddlewareStub1 {
	public function handle( RequestInterface $request, Closure $next ) {}
}
