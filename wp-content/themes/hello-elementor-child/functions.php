<?php
/**
 * Hello Elementor child theme setup.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action(
	'wp_enqueue_scripts',
	function () {
		wp_enqueue_style(
			'hello-elementor-child',
			get_stylesheet_uri(),
			[ 'hello-elementor' ],
			filemtime( get_stylesheet_directory() . '/style.css' )
		);
		wp_enqueue_script( 'bv-sticky-header', get_stylesheet_directory_uri() . '/assets/sticky-header.js', [], filemtime( get_stylesheet_directory() . '/assets/sticky-header.js' ), true );
		if ( is_front_page() ) {
			wp_enqueue_style( 'bv-wabi-sabi-home', get_stylesheet_directory_uri() . '/assets/wabi-sabi-home.css', [ 'hello-elementor-child' ], filemtime( get_stylesheet_directory() . '/assets/wabi-sabi-home.css' ) );
		}
	},
	30
);

require_once get_stylesheet_directory() . '/inc/commerce.php';
require_once get_stylesheet_directory() . '/inc/class-wc-gateway-vietqr.php';
require_once get_stylesheet_directory() . '/inc/workshop-booking.php';
require_once get_stylesheet_directory() . '/inc/zalo-contact.php';
require_once get_stylesheet_directory() . '/inc/performance.php';
require_once get_stylesheet_directory() . '/inc/product-reviews.php';
