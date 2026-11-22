<?php
if ( PHP_SAPI !== 'cli' ) { exit( 1 ); }
require dirname( __DIR__ ) . '/wp-load.php';
add_filter( 'pre_wp_mail', '__return_true', PHP_INT_MAX );
$command = $argv[1] ?? 'create';
$state = get_option( 'bv_checkout_test' );
if ( 'create' === $command ) {
    if ( $state || count( WC_Data_Store::load( 'webhook' )->get_webhooks_ids( 'active' ) ) ) { exit( 1 ); }
    $token = bin2hex( random_bytes( 24 ) );
    $product = new WC_Product_Simple();
    $product->set_name( 'BV checkout test - temporary' );
    $product->set_status( 'publish' );
    $product->set_catalog_visibility( 'hidden' );
    $product->set_regular_price( '165000' );
    $product->set_manage_stock( true );
    $product->set_stock_quantity( 5 );
    $product->update_meta_data( '_bv_checkout_test', $token );
    $state = [ 'token' => $token, 'expires' => time() + 1800, 'product' => $product->save(), 'orders' => [] ];
    update_option( 'bv_checkout_test', $state, false );
    echo json_encode( $state );
    exit;
}
if ( ! $state ) { exit( 1 ); }
if ( 'inspect' === $command ) {
    $orders = [];
    foreach ( $state['orders'] as $id ) {
        $order = wc_get_order( $id );
        if ( ! $order || $order->get_meta( '_bv_checkout_test' ) !== $state['token'] ) { exit( 1 ); }
        $orders[] = [ 'id' => $id, 'status' => $order->get_status(), 'payment' => $order->get_payment_method(), 'total' => $order->get_total(), 'shipping' => $order->get_shipping_total(), 'note' => $order->get_customer_note(), 'customer' => $order->get_customer_id(), 'quantity' => $order->get_item_count(), 'address' => $order->get_shipping_address_1() ];
    }
    echo json_encode( [ 'orders' => $orders, 'stock' => wc_get_product( $state['product'] )->get_stock_quantity() ] );
    exit;
}
if ( 'delete' !== $command ) { exit( 1 ); }
foreach ( $state['orders'] as $id ) {
    $order = wc_get_order( $id );
    if ( ! $order || $order->get_meta( '_bv_checkout_test' ) !== $state['token'] ) { exit( 1 ); }
    $order->update_status( 'cancelled' );
    $order->delete( true );
}
$product = wc_get_product( $state['product'] );
if ( ! $product || $product->get_meta( '_bv_checkout_test' ) !== $state['token'] ) { exit( 1 ); }
$product->delete( true );
delete_option( 'bv_checkout_test' );
echo "Test orders and product removed.\n";
