<?php
if ( PHP_SAPI !== 'cli' ) { exit( 1 ); }
require dirname( __DIR__ ) . '/wp-load.php';
add_option( 'bv_payment_settings_before_cod_only', [
    'bacs' => get_option( 'woocommerce_bacs_settings', [] ),
    'cod' => get_option( 'woocommerce_cod_settings', [] ),
    'default' => get_option( 'woocommerce_default_gateway' ),
], '', false );
foreach ( [ 'bacs' => 'no', 'cod' => 'yes' ] as $gateway => $enabled ) {
    $settings = get_option( 'woocommerce_' . $gateway . '_settings', [] );
    $settings['enabled'] = $enabled;
    update_option( 'woocommerce_' . $gateway . '_settings', $settings );
}
update_option( 'woocommerce_default_gateway', 'cod' );
echo "COD enabled; bank transfer disabled.\n";
