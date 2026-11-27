<?php
/**
 * Hello Elementor child theme setup.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'bv_asset_ver' ) ) {
	/**
	 * Return file modification timestamp for cache-busting without defeating browser cache.
	 */
	function bv_asset_ver( $rel_path ) {
		$full = get_stylesheet_directory() . $rel_path;
		return file_exists( $full ) ? (string) filemtime( $full ) : '1.0.0';
	}
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
		
		// Global Smooth Scroll Reveal
		$reveal_css = get_stylesheet_directory() . '/assets/scroll-reveal.css';
		$reveal_js  = get_stylesheet_directory() . '/assets/scroll-reveal.js';
		wp_enqueue_style( 'bv-scroll-reveal', get_stylesheet_directory_uri() . '/assets/scroll-reveal.css', [ 'hello-elementor-child' ], file_exists( $reveal_css ) ? filemtime( $reveal_css ) : '1.0.0' );
		wp_enqueue_script( 'bv-scroll-reveal', get_stylesheet_directory_uri() . '/assets/scroll-reveal.js', [], file_exists( $reveal_js ) ? filemtime( $reveal_js ) : '1.0.0', true );

		if ( is_front_page() ) {
			wp_enqueue_style( 'bv-wabi-sabi-home', get_stylesheet_directory_uri() . '/assets/wabi-sabi-home.css', [ 'hello-elementor-child' ], bv_asset_ver( '/assets/wabi-sabi-home.css' ) );
		}
	},
	30
);

require_once get_stylesheet_directory() . '/inc/commerce.php';
require_once get_stylesheet_directory() . '/inc/class-wc-gateway-vietqr.php';
require_once get_stylesheet_directory() . '/inc/class-wc-vietqr-webhook.php';
require_once get_stylesheet_directory() . '/inc/workshop-booking.php';
require_once get_stylesheet_directory() . '/inc/admin-workshop-schedule.php';
require_once get_stylesheet_directory() . '/inc/zalo-contact.php';
require_once get_stylesheet_directory() . '/inc/performance.php';
require_once get_stylesheet_directory() . '/inc/product-reviews.php';
require_once get_stylesheet_directory() . '/inc/header-footer.php';
require_once get_stylesheet_directory() . '/inc/emails.php';

// Chan lo danh sach user qua REST API khi chua dang nhap
add_filter(
	'rest_endpoints',
	function ( $endpoints ) {
		if ( ! is_user_logged_in() ) {
			unset( $endpoints['/wp/v2/users'] );
			unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
		}
		return $endpoints;
	}
);
