<?php
if ( PHP_SAPI !== 'cli' ) { exit; }
require dirname( __DIR__ ) . '/wp-load.php';
add_filter( 'pre_wp_mail', '__return_true' );
if ( ( $argv[1] ?? '' ) === 'delete' ) {
    $id = (int) ( $argv[2] ?? 0 );
    if ( get_user_meta( $id, '_bv_account_test', true ) !== 'yes' ) { exit( 1 ); }
    require_once ABSPATH . 'wp-admin/includes/user.php';
    wp_delete_user( $id );
    exit;
}
$login = 'bv_test_' . bin2hex( random_bytes( 5 ) );
$password = bin2hex( random_bytes( 16 ) );
$id = wp_insert_user( [ 'user_login' => $login, 'user_pass' => $password, 'user_email' => $login . '@example.invalid', 'role' => 'customer' ] );
if ( is_wp_error( $id ) ) { exit( 1 ); }
update_user_meta( $id, '_bv_account_test', 'yes' );
echo json_encode( [ 'id' => $id, 'login' => $login, 'password' => $password, 'can_manage_options' => user_can( $id, 'manage_options' ) ] );
