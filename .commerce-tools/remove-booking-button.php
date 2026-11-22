<?php
if ( PHP_SAPI !== 'cli' ) { exit; }
require dirname( __DIR__ ) . '/wp-load.php';
$raw = get_post_meta( 92, '_elementor_data', true );
$nodes = json_decode( $raw, true );
if ( ! is_array( $nodes ) ) { exit( 1 ); }
add_option( 'bv_header_before_remove_booking', $raw, '', false );
function bv_remove_booking( &$nodes ) {
    foreach ( $nodes as $index => &$node ) {
        if ( ( $node['widgetType'] ?? '' ) === 'button' && ( ( $node['settings']['button_css_id'] ?? '' ) === 'regester' || ( $node['settings']['_element_id'] ?? '' ) === 'regester' ) ) {
            unset( $nodes[$index] );
            continue;
        }
        if ( ! empty( $node['elements'] ) ) { bv_remove_booking( $node['elements'] ); }
    }
    unset( $node );
    $nodes = array_values( $nodes );
}
bv_remove_booking( $nodes );
update_post_meta( 92, '_elementor_data', wp_slash( wp_json_encode( $nodes ) ) );
delete_post_meta( 92, '_elementor_element_cache' );
if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) { ( new \Elementor\Core\Files\CSS\Post( 92 ) )->update(); }
echo "Header updated.\n";
