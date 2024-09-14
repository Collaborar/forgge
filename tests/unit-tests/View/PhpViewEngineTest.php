<?php

namespace ForggeTests\View;

use Mockery;
use Forgge\Helpers\MixedType;
use Forgge\View\PhpView;
use Forgge\View\PhpViewEngine;
use Forgge\View\PhpViewFilesystemFinder;
use Forgge\View\ViewInterface;
use Forgge\View\ViewNotFoundException;
use ForggeTestTools\Helper;
use ForggeTestTools\TestCase;

/**
 * @coversDefaultClass \Forgge\View\PhpViewEngine
 */
class PhpViewEngineTest extends TestCase {
	public function set_up() {
		$this->compose_action = [Mockery::mock()->shouldIgnoreMissing(), '__invoke'];
		$this->finder = new PhpViewFilesystemFinder( [ get_stylesheet_directory(), get_template_directory() ] );
		$this->subject = new PhpViewEngine( $this->compose_action, $this->finder );
	}

	public function tear_down() {
		Mockery::close();

		unset( $this->compose_action );
		unset( $this->finder );
		unset( $this->subject );
	}

	/**
	 * @covers ::exists
	 */
	public function testExists() {
		$this->assertTrue( $this->subject->exists( FORGGE_TEST_DIR . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'view.php' ) );
		$this->assertTrue( $this->subject->exists( 'index.php' ) );
		$this->assertTrue( $this->subject->exists( 'index' ) );
		$this->assertFalse( $this->subject->exists( 'nonexistent' ) );
		$this->assertFalse( $this->subject->exists( '' ) );
	}

	/**
	 * @covers ::canonical
	 */
	public function testCanonical() {
		$expected = realpath( MixedType::normalizePath( locate_template( 'index.php', false ) ) );

		$this->assertEquals( $expected, $this->subject->canonical( $expected ) );
		$this->assertEquals( $expected, $this->subject->canonical( 'index.php' ) );
		$this->assertEquals( $expected, $this->subject->canonical( 'index' ) );
		$this->assertEquals( '', $this->subject->canonical( 'nonexistent' ) );
		$this->assertEquals( '', $this->subject->canonical( '' ) );
	}

	/**
	 * @covers ::make
	 * @covers ::makeView
	 */
	public function testMake_View() {
		$file = FORGGE_TEST_DIR . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'view.php';
		$view = $this->subject->make( [$file] );

		$this->assertInstanceOf( ViewInterface::class, $view );
		$this->assertEquals( $file, $view->getName() );
		$this->assertEquals( $file, $view->getFilepath() );
	}

	/**
	 * @covers ::makeView
	 * @covers ::getViewLayout
	 */
	public function testMake_WithLayout() {
		list( $view, $layout, $output, $handle ) = Helper::createLayoutView();
		$view = $this->subject->make( [$view] );

		$this->assertEquals( $layout, $view->getLayout()->getFilepath() );

		Helper::deleteLayoutView( $handle );
	}

	/**
	 * @covers ::getViewLayout
	 */
	public function testMake_WithIncorrectLayout() {
		// Rely on the fact that view-with-layout.php uses a sprintf() token instead of a real path so it fails.
		$view = FORGGE_TEST_DIR . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'view-with-layout.php';

		$this->expectException( ViewNotFoundException::class );
		$this->expectExceptionMessage( 'View layout not found' );
		$this->subject->make( [$view] );
	}

	/**
	 * @covers ::make
	 * @covers ::makeView
	 */
	public function testMake_NoView() {
		$this->expectException( ViewNotFoundException::class );
		$this->expectExceptionMessage( 'View not found' );
		$this->subject->make( [''], [] );
	}

	/**
	 * @covers ::renderView
	 */
	public function testRenderView_View_Render() {
		$view = Mockery::mock( PhpView::class );
		$file = FORGGE_TEST_DIR . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'view-with-context.php';
		$expected = "Hello World!\n";

		$view->shouldReceive( 'getContext' )
			->andReturn( ['world' => 'World'] );

		$view->shouldReceive( 'getFilepath' )
			->andReturn( $file );

		$this->subject->pushLayoutContent( $view );

		$this->assertEquals( $expected, $this->subject->getLayoutContent() );
	}

	/**
	 * @covers ::renderView
	 */
	public function testRenderView_NoView_EmptyString() {
		$this->assertEquals( '', $this->subject->getLayoutContent() );
	}
}
