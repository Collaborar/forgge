<?php


/**
 * Layout template file for Whoops's pretty error output.
 *
 * @noinspection ALL
 */

$is_admin = function_exists( 'is_admin' ) && is_admin() && apply_filters( 'forgge.pretty_errors.apply_admin_styles', true );
$is_ajax = function_exists( 'wp_doing_ajax' ) && wp_doing_ajax();

if ( $is_admin && ! $is_ajax ) {
	?>
	<!--suppress CssUnusedSymbol -->
	<style>
		.forgge-whoops {
			position: relative;
			z-index: 1;
			margin: 20px 20px 0 0;
		}

		.forgge-whoops .stack-container {
			display: flex;
		}

		.forgge-whoops .left-panel {
			position: static;
			height: auto;
			overflow: visible;
		}

		.forgge-whoops .details-container {
			position: static;
			height: auto;
			overflow: visible;
		}

		@media (max-width: 600px) {
			.forgge-whoops {
				margin: 10px 10px 0 0;
			}

			.forgge-whoops .stack-container {
				display: block;
			}
		}
	</style>
	<!--suppress JSValidateTypes, JSValidateTypes -->
	<script>
		jQuery(window).load(function () {
			jQuery(window).scrollTop(0);

			jQuery('.frames-container').on('click', '.frame', function() {
				jQuery(window).scrollTop(0);
			});
		});
	</script>
	<?php
	require 'forgge-body.html.php';
	return;
}
?>
<!DOCTYPE html><?php echo $preface; ?>
<html lang="en_US">
<head>
	<meta charset="utf-8">
	<meta name="robots" content="noindex,nofollow"/>
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
	<?php // Avoid triggering the Theme Check sniff as this is not a WordPress template. ?>
	<?php echo '<' . 'title' . '>' . $tpl->escape( $page_title ) . '</' . 'title' . '>' ?>
</head>
<body>
	<?php require 'forgge-body.html.php'; ?>
</body>
</html>
