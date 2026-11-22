<?php
if ( PHP_SAPI !== 'cli' ) { exit; }
require dirname( __DIR__ ) . '/wp-load.php';
$menu = wp_get_nav_menu_object( 4 );
$page = get_post( 144 );
if ( ! $menu || ! $page || 'publish' !== $page->post_status ) { exit( "Menu or booking page unavailable.\n" ); }
$items = wp_get_nav_menu_items( $menu->term_id ) ?: [];
add_option( 'bv_menu_before_booking_link', $items, '', false );
$id = 0;
foreach ( $items as $item ) {
    if ( ( 'page' === $item->object && 144 === (int) $item->object_id ) || untrailingslashit( $item->url ) === untrailingslashit( get_permalink( 144 ) ) ) { $id = $item->ID; break; }
}
$result = wp_update_nav_menu_item( $menu->term_id, $id, [
    'menu-item-title' => 'Đặt lịch',
    'menu-item-object' => 'page',
    'menu-item-object-id' => 144,
    'menu-item-type' => 'post_type',
    'menu-item-status' => 'publish',
] );
if ( is_wp_error( $result ) ) { exit( $result->get_error_message() ); }
delete_post_meta( 92, '_elementor_element_cache' );
echo 'Booking menu item: ' . $result . ', URL: ' . get_permalink( 144 ) . PHP_EOL;
