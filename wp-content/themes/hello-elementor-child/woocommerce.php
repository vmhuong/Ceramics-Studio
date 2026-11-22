<?php
/**
 * WooCommerce wrapper template for Ceramic Studio.
 * Style: Anh Ceramics (Wabi-Sabi Craft)
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

if ( is_singular( 'product' ) ) {
	wc_get_template( 'single-product.php' );
} else {
	wc_get_template( 'archive-product.php' );
}

get_footer();
