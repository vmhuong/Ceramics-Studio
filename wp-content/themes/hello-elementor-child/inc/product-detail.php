<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', function () {
    if ( ! function_exists( 'is_product' ) || ! is_product() ) { return; }
    
    $prod_css = get_stylesheet_directory() . '/assets/ceramic-product.css';
    $prod_js  = get_stylesheet_directory() . '/assets/product-detail.js';

    wp_enqueue_style( 'bv-ceramic-product', get_stylesheet_directory_uri() . '/assets/ceramic-product.css', [ 'bv-commerce' ], file_exists( $prod_css ) ? filemtime( $prod_css ) : '1.0.0' );
    wp_enqueue_script( 'bv-product-detail', get_stylesheet_directory_uri() . '/assets/product-detail.js', [ 'jquery', 'wc-add-to-cart-variation', 'wc-single-product' ], file_exists( $prod_js ) ? filemtime( $prod_js ) : '1.0.0', true );
    
    wp_localize_script( 'bv-product-detail', 'bvProduct', [
        'colors' => [
            'San hô'      => '#C77B4B',
            'Hồng phấn'   => '#CEB8A5',
            'Xanh sương'  => '#AFC4CA',
            'Cam'         => '#C4572E',
            'Vàng'        => '#C99E4D',
            'Nâu'         => '#7A4F3E',
            'Đen'         => '#2C2523',
            'Trắng'       => '#FAF7F2',
            'Xanh biển'   => '#314E59',
            'Rêu'         => '#7B8C70',
            'Gốm mộc'     => '#E8DFC8',
            'Pale Yellow' => '#D4A359',
            'Plain'       => '#EFEBE4',
            'Gradient'    => '#8C5835',
            'Mộc be cát'  => '#E8DFC8',
            'Men tro'     => '#D8C3A5',
            'Đất nung'    => '#B88258',
        ]
    ] );
}, 60 );

add_filter( 'woocommerce_product_single_add_to_cart_text', function ( $text, $product ) {
    return $product->is_type( [ 'simple', 'variable', 'grouped' ] ) ? 'Thêm vào giỏ' : $text;
}, 10, 2 );

add_filter( 'woocommerce_output_related_products_args', function ( $args ) {
    $args['posts_per_page'] = 4;
    $args['columns'] = 4;
    return $args;
} );
add_filter( 'woocommerce_product_related_products_heading', function () { return 'Tác phẩm cùng bộ sưu tập'; } );
