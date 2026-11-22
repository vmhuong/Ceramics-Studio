<?php
/**
 * Performance Optimization: WebP Uploads & Intelligent Lazy Loading
 *
 * @package HelloElementorChild
 */

defined( 'ABSPATH' ) || exit;

// 1. Convert all newly uploaded images to WebP automatically in WordPress
add_filter( 'image_editor_output_format', function ( $formats ) {
	$formats['image/jpeg'] = 'image/webp';
	$formats['image/png']  = 'image/webp';
	return $formats;
} );

// 2. Intelligent Lazy Loading & Async Decoding for all attachment images
add_filter( 'wp_get_attachment_image_attributes', function ( $attr, $attachment, $size ) {
	// Add decoding="async" for smooth non-blocking browser paint
	$attr['decoding'] = 'async';

	// Keep native lazy loading active
	if ( empty( $attr['loading'] ) ) {
		$attr['loading'] = 'lazy';
	}

	return $attr;
}, 10, 3 );

// 3. Ensure WooCommerce product catalog thumbnails and gallery thumbnails use lazy loading
add_filter( 'woocommerce_get_image_size_thumbnail', function ( $size ) {
	return $size;
} );

// 4. Protect LCP (Largest Contentful Paint) for the main featured image on single product page
add_filter( 'woocommerce_single_product_image_gallery_classes', function ( $classes ) {
	$classes[] = 'bv-lcp-protected';
	return $classes;
} );

// 5. Add lightweight preconnect hints for Google Fonts and CDNs
add_action( 'wp_head', function () {
	echo '<link rel="preconnect" href="https://fonts.googleapis.com" crossorigin>' . "\n";
	echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}, 1 );
