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

	// Ensure the Wabi-Sabi front-page.php, woocommerce.php, and page-about-us.php are always used, overriding Elementor canvas/template hijack
	add_filter( 'template_include', function ( $template ) {
		if ( is_front_page() ) {
			$front = get_stylesheet_directory() . '/front-page.php';
			if ( file_exists( $front ) ) {
				return $front;
			}
		}
		if ( is_page( 'about-us' ) || is_page( 42 ) ) {
			$about = get_stylesheet_directory() . '/page-about-us.php';
			if ( file_exists( $about ) ) {
				return $about;
			}
		}
		if ( is_page( 'contact-us' ) || is_page( 18 ) ) {
			$contact = get_stylesheet_directory() . '/page-contact-us.php';
			if ( file_exists( $contact ) ) {
				return $contact;
			}
		}
		if ( is_page( 'workshop' ) || is_page( 24 ) ) {
			$ws = get_stylesheet_directory() . '/page-workshop.php';
			if ( file_exists( $ws ) ) {
				return $ws;
			}
		}
		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			$woo = get_stylesheet_directory() . '/woocommerce.php';
			if ( file_exists( $woo ) ) {
				return $woo;
			}
		}
		return $template;
	}, 99999 );
}

// 2. Enqueue Global Wabi-Sabi Header & Footer Assets & Ceramic Shop Assets
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

	// Ceramic About Page Assets
	if ( is_page( 'about-us' ) || is_page( 42 ) ) {
		$about_css = get_stylesheet_directory() . '/assets/ceramic-about.css';
		wp_enqueue_style( 'bv-ceramic-about', get_stylesheet_directory_uri() . '/assets/ceramic-about.css', [ 'bv-wabi-sabi-header-footer' ], file_exists( $about_css ) ? filemtime( $about_css ) : '1.0.0' );
	}

	// Ceramic Contact Page Assets
	if ( is_page( 'contact-us' ) || is_page( 18 ) ) {
		$contact_css = get_stylesheet_directory() . '/assets/ceramic-contact.css';
		wp_enqueue_style( 'bv-ceramic-contact', get_stylesheet_directory_uri() . '/assets/ceramic-contact.css', [ 'bv-wabi-sabi-header-footer' ], file_exists( $contact_css ) ? filemtime( $contact_css ) : '1.0.0' );
	}

	// Ceramic Workshop Page Assets
	if ( is_page( 'workshop' ) || is_page( 24 ) ) {
		$ws_css = get_stylesheet_directory() . '/assets/ceramic-workshop.css';
		wp_enqueue_style( 'bv-ceramic-workshop', get_stylesheet_directory_uri() . '/assets/ceramic-workshop.css', [ 'bv-wabi-sabi-header-footer' ], file_exists( $ws_css ) ? filemtime( $ws_css ) : '1.0.0' );
	}

	// Ceramic Shop & Collections Assets (Anh Ceramics Style)
	if ( function_exists( 'is_woocommerce' ) && ( is_shop() || is_product_taxonomy() ) ) {
		$shop_css = get_stylesheet_directory() . '/assets/ceramic-shop.css';
		$shop_js  = get_stylesheet_directory() . '/assets/ceramic-shop.js';
		wp_enqueue_style( 'bv-ceramic-shop', get_stylesheet_directory_uri() . '/assets/ceramic-shop.css', [ 'bv-wabi-sabi-header-footer' ], file_exists( $shop_css ) ? filemtime( $shop_css ) : '1.0.0' );
		wp_enqueue_script( 'bv-ceramic-shop', get_stylesheet_directory_uri() . '/assets/ceramic-shop.js', [ 'jquery' ], file_exists( $shop_js ) ? filemtime( $shop_js ) : '1.0.0', true );
	}

	// Ceramic Single Product Assets (Anh Ceramics Style)
	if ( function_exists( 'is_product' ) && is_product() ) {
		$prod_css = get_stylesheet_directory() . '/assets/ceramic-product.css';
		$shop_js  = get_stylesheet_directory() . '/assets/ceramic-shop.js';
		wp_enqueue_style( 'bv-ceramic-product', get_stylesheet_directory_uri() . '/assets/ceramic-product.css', [ 'bv-wabi-sabi-header-footer' ], file_exists( $prod_css ) ? filemtime( $prod_css ) : '1.0.0' );
		wp_enqueue_script( 'bv-ceramic-shop', get_stylesheet_directory_uri() . '/assets/ceramic-shop.js', [ 'jquery' ], file_exists( $shop_js ) ? filemtime( $shop_js ) : '1.0.0', true );
	}
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
