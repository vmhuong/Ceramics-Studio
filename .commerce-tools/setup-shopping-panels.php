<?php
if ( PHP_SAPI !== 'cli' ) { exit; }
require dirname( __DIR__ ) . '/wp-load.php';
add_option( 'bv_mini_cart_template_backup', [ 'wpr_override_woo_mini_cart' => get_option( 'wpr_override_woo_mini_cart', 'on' ) ], '', false );
update_option( 'wpr_override_woo_mini_cart', 'off' );
echo "Native WooCommerce mini-cart enabled; Royal product grid unchanged.\n";
