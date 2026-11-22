<?php
/**
 * Header & Footer Integration - Wabi-Sabi Zen Aesthetic
 * Theme: Hello Elementor Child for Ceramic Studio
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 1. Bypass Royal Elementor Addons Theme Builder on frontend to use our custom Wabi-Sabi header & footer
if ( ! is_admin() ) {
	add_filter( 'pre_option_wpr_header_conditions', function () {
		return '[]';
	} );

	add_filter( 'pre_option_wpr_footer_conditions', function () {
		return '[]';
	} );

	// Ensure the Wabi-Sabi front-page.php is always used for the homepage, overriding Elementor canvas/template hijack
	add_filter( 'template_include', function ( $template ) {
		if ( is_front_page() ) {
			$front = get_stylesheet_directory() . '/front-page.php';
			if ( file_exists( $front ) ) {
				return $front;
			}
		}
		return $template;
	}, 99999 );
}

// 2. Enqueue Global Wabi-Sabi Header & Footer Assets
add_action( 'wp_enqueue_scripts', function () {
	$css_file = get_stylesheet_directory() . '/assets/wabi-sabi-header-footer.css';
	$js_file  = get_stylesheet_directory() . '/assets/wabi-sabi-header-footer.js';

	wp_enqueue_style(
		'bv-wabi-sabi-header-footer',
		get_stylesheet_directory_uri() . '/assets/wabi-sabi-header-footer.css',
		[ 'hello-elementor-child' ],
		file_exists( $css_file ) ? filemtime( $css_file ) : '1.0.0'
	);

	wp_enqueue_script(
		'bv-wabi-sabi-header-footer',
		get_stylesheet_directory_uri() . '/assets/wabi-sabi-header-footer.js',
		[ 'jquery' ],
		file_exists( $js_file ) ? filemtime( $js_file ) : '1.0.0',
		true
	);
}, 25 );

// 3. Sync WooCommerce Cart Fragments for the Header Cart Badge
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	if ( function_exists( 'WC' ) && WC()->cart ) {
		$count = WC()->cart->get_cart_contents_count();
		$display_count = $count > 99 ? '99+' : $count;
		$fragments['.bv-cart-count'] = '<span class="bv-cart-count" aria-hidden="true">' . esc_html( $display_count ) . '</span>';
	}
	return $fragments;
} );
