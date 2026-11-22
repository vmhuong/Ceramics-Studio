<?php
if ( PHP_SAPI !== 'cli' ) { exit; }
require dirname( __DIR__ ) . '/wp-load.php';
echo 'Home: ' . home_url() . ' cookie path: ' . COOKIEPATH . PHP_EOL;
echo 'Session rows: ' . $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}woocommerce_sessions" ) . PHP_EOL;
echo 'Database error: ' . $wpdb->last_error . PHP_EOL;
echo 'BACS account count: ' . count( array_filter( get_option( 'woocommerce_bacs_accounts', [] ), function ( $account ) { return ! empty( $account['account_number'] ) && ! empty( $account['bank_name'] ); } ) ) . PHP_EOL;
echo 'Checkout test fixture active: ' . ( get_option( 'bv_checkout_test' ) ? 'yes' : 'no' ) . PHP_EOL;
foreach ( [ 'woocommerce_cart_page_id', 'woocommerce_checkout_page_id', 'woocommerce_myaccount_page_id', 'woocommerce_enable_guest_checkout', 'woocommerce_ship_to_destination', 'woocommerce_default_country', 'woocommerce_currency', 'woocommerce_enable_signup_and_login_from_checkout' ] as $key ) { echo $key . '=' . get_option( $key ) . PHP_EOL; }
foreach ( WC()->payment_gateways()->payment_gateways() as $id => $gateway ) { if ( 'yes' === $gateway->enabled ) { echo 'Gateway: ' . $id . PHP_EOL; } }
echo 'Webhooks: ' . count( WC_Data_Store::load( 'webhook' )->get_webhooks_ids( 'active' ) ) . PHP_EOL;
echo 'Mail plugins: ' . implode( ', ', array_filter( get_option( 'active_plugins', [] ), function ( $name ) { return preg_match( '/mail|smtp/i', $name ); } ) ) . PHP_EOL;
foreach ( WC_Shipping_Zones::get_zones() as $zone ) { foreach ( $zone['shipping_methods'] as $method ) { if ( 'yes' === $method->enabled ) { echo 'Shipping: ' . $method->id . ' cost=' . $method->get_option( 'cost' ) . PHP_EOL; } } }
