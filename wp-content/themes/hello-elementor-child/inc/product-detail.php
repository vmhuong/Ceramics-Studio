<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp', function () {
    if ( ! function_exists( 'is_product' ) || ! is_product() ) { return; }
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
    add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 6 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
    remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
    add_action( 'woocommerce_single_product_summary', 'woocommerce_output_product_data_tabs', 45 );
    // Bound the sticky gallery to the detail row, never to related products.
    add_action( 'woocommerce_before_single_product_summary', function () { echo '<div class="bv-product-main">'; }, 1 );
    add_action( 'woocommerce_after_single_product_summary', function () { echo '</div>'; }, 1 );
} );

add_filter( 'wc_add_to_cart_message_html', function ( $message, $products ) {
    $count = array_sum( $products );
    return $message . '<span class="bv-added-message" data-bv-count="' . esc_attr( number_format_i18n( $count ) ) . '"></span>';
}, 10, 2 );
add_filter( 'woocommerce_product_single_add_to_cart_text', function ( $text, $product ) {
    return $product->is_type( [ 'simple', 'variable', 'grouped' ] ) ? 'Thêm vào giỏ' : $text;
}, 10, 2 );

add_action( 'wp_enqueue_scripts', function () {
    if ( ! function_exists( 'is_product' ) || ! is_product() ) { return; }
    wp_enqueue_style( 'bv-product-detail', get_stylesheet_directory_uri() . '/assets/product-detail.css', [ 'bv-commerce' ], filemtime( __DIR__ . '/../assets/product-detail.css' ) );
    wp_enqueue_script( 'bv-product-detail', get_stylesheet_directory_uri() . '/assets/product-detail.js', [ 'jquery', 'wc-add-to-cart-variation', 'wc-single-product' ], filemtime( __DIR__ . '/../assets/product-detail.js' ), true );
    wp_localize_script( 'bv-product-detail', 'bvProduct', [ 'colors' => [ 'San hô' => '#c77b4b', 'Hồng phấn' => '#ceb8a5', 'Xanh sương' => '#afc4ca' ] ] );
}, 60 );

add_filter( 'woocommerce_output_related_products_args', function ( $args ) {
    $args['posts_per_page'] = 4;
    $args['columns'] = 4;
    return $args;
} );
add_filter( 'woocommerce_product_related_products_heading', function () { return 'Sản phẩm liên quan'; } );
