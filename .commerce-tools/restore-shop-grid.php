<?php
if ( PHP_SAPI !== 'cli' ) { exit; }
require dirname( __DIR__ ) . '/wp-load.php';
$id = wc_get_page_id( 'shop' );
$raw = get_post_meta( $id, '_elementor_data', true );
$nodes = json_decode( $raw, true );
$backup = get_option( 'bv_pottery_refinement_backup' );
$saved = json_decode( $backup[$id]['_elementor_data'][0] ?? '[]', true );
function bv_find_grid( $nodes ) {
    foreach ( $nodes as $node ) {
        if ( ( $node['widgetType'] ?? '' ) === 'wpr-woo-grid' ) { return $node; }
        $found = bv_find_grid( $node['elements'] ?? [] );
        if ( $found ) { return $found; }
    }
    return null;
}
$grid = bv_find_grid( $saved );
if ( ! is_array( $nodes ) || ! $grid ) { exit( "Shop data or backup unavailable.\n" ); }
add_option( 'bv_shop_before_recovery_' . gmdate( 'Ymd_His' ), $raw, '', false );
$colors = [ '#3D6357' => '#7C292D', '#2C4C41' => '#5B1E23', '#AF4B2F' => '#7C292D', '#666B66' => '#796853', '#FFFFFF' => '#F4F0EE' ];
foreach ( $grid['settings'] as &$value ) {
    if ( is_string( $value ) && isset( $colors[strtoupper( $value )] ) ) { $value = $colors[strtoupper( $value )]; }
}
unset( $value );
$grid['settings']['query_posts_per_page'] = 12;
function bv_restore_grid_column( &$nodes, $grid ) {
    foreach ( $nodes as &$node ) {
        if ( ( $node['id'] ?? '' ) === '7f5f07d8' ) {
            $node['elements'][] = $grid;
            return true;
        }
        if ( ! empty( $node['elements'] ) && bv_restore_grid_column( $node['elements'], $grid ) ) { return true; }
    }
    return false;
}
if ( ! bv_find_grid( $nodes ) && ! bv_restore_grid_column( $nodes, $grid ) ) { exit( "Original grid column not found; no changes saved.\n" ); }
$nodes = array_values( array_filter( $nodes, function ( $node ) { return ( $node['id'] ?? '' ) !== 'bvintro'; } ) );
update_post_meta( $id, '_elementor_data', wp_slash( wp_json_encode( $nodes ) ) );
delete_post_meta( $id, '_elementor_element_cache' );
\Elementor\Core\Files\CSS\Post::create( $id )->update();
clean_post_cache( $id );
echo 'Restored grid and removed intro. Products unchanged: ' . wp_count_posts( 'product' )->publish . PHP_EOL;
