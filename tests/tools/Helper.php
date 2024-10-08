<?php

namespace ForggeTestTools;

class Helper {
	public static function createLayoutView() {
		$layout = FORGGE_TEST_DIR . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'layout.php';
		$view_template = FORGGE_TEST_DIR . DIRECTORY_SEPARATOR . 'fixtures' . DIRECTORY_SEPARATOR . 'view-with-layout.php';
		$view_contents = file_get_contents( $view_template );
		$handle = tmpfile();
		fwrite( $handle, sprintf( $view_contents, $layout ) );
		$view = stream_get_meta_data( $handle )['uri'];
		return [$view, $layout, 'foobar', $handle];
	}

	public static function deleteLayoutView( $handle ) {
		fclose( $handle );
	}
}
