<?php
// Temporary local test guard, inactive while stored in .commerce-tools.
defined( 'ABSPATH' ) || exit;
$bv_test = get_option( 'bv_checkout_test' );
if ( ! is_array( $bv_test ) || $bv_test['expires'] < time() || ! in_array( $_SERVER['REMOTE_ADDR'] ?? '', [ '127.0.0.1', '::1' ], true ) || ! hash_equals( $bv_test['token'], $_SERVER['HTTP_X_BV_CHECKOUT_TEST'] ?? '' ) ) { return; }
add_filter( 'pre_wp_mail', '__return_true', PHP_INT_MAX );
add_action( 'send_headers', function () { header( 'X-BV-Test-Mail: blocked' ); } );
add_action( 'woocommerce_checkout_create_order', function ( $order ) use ( $bv_test ) {
    foreach ( $order->get_items() as $item ) {
        if ( $item->get_product_id() !== $bv_test['product'] ) { throw new Exception( 'Only the isolated test product is allowed.' ); }
    }
    $order->update_meta_data( '_bv_checkout_test', $bv_test['token'] );
}, 10 );
add_action( 'woocommerce_checkout_order_created', function ( $order ) use ( $bv_test ) {
    $state = get_option( 'bv_checkout_test' );
    $state['orders'][] = $order->get_id();
    update_option( 'bv_checkout_test', $state, false );
} );
