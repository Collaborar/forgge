<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'forgge.kernels.http_kernel.respond' );
remove_all_filters( 'forgge.kernels.http_kernel.respond' );
