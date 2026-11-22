<?php
if ( PHP_SAPI !== 'cli' ) { exit; }
require dirname( __DIR__ ) . '/wp-load.php';
$settings = [
    'woocommerce_enable_myaccount_registration' => 'yes',
    'woocommerce_enable_signup_and_login_from_checkout' => 'yes',
    'woocommerce_enable_checkout_login_reminder' => 'yes',
    'woocommerce_enable_guest_checkout' => 'yes',
    'woocommerce_registration_generate_username' => 'yes',
    'woocommerce_registration_generate_password' => 'no',
];
$backup = [];
foreach ( $settings as $key => $value ) { $backup[$key] = get_option( $key ); }
$id = wc_get_page_id( 'myaccount' );
$backup['page'] = get_post( $id, ARRAY_A );
add_option( 'bv_account_setup_backup', $backup, '', false );
foreach ( $settings as $key => $value ) { update_option( $key, $value ); }
if ( $id <= 0 || ! get_post( $id ) ) {
    $id = wp_insert_post( [ 'post_type' => 'page', 'post_status' => 'publish', 'post_title' => 'Tài khoản của tôi', 'post_name' => 'tai-khoan', 'post_content' => '[woocommerce_my_account]' ], true );
    if ( is_wp_error( $id ) ) { exit( $id->get_error_message() ); }
    update_option( 'woocommerce_myaccount_page_id', $id );
} else {
    $post = get_post( $id );
    if ( ! has_shortcode( $post->post_content, 'woocommerce_my_account' ) ) {
        wp_update_post( [ 'ID' => $id, 'post_content' => $post->post_content . "\n[woocommerce_my_account]" ] );
    }
    wp_update_post( [ 'ID' => $id, 'post_title' => 'Tài khoản của tôi', 'post_status' => 'publish' ] );
}
flush_rewrite_rules( false );
echo 'Account URL: ' . get_permalink( $id ) . PHP_EOL;
