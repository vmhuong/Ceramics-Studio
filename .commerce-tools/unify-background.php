<?php
if ( PHP_SAPI !== 'cli' ) { exit; }
require dirname( __DIR__ ) . '/wp-load.php';
$posts = get_posts( [ 'post_type' => [ 'page', 'wpr_templates', 'elementor_library' ], 'post_status' => 'publish', 'numberposts' => -1 ] );
$backup = [];
function bv_mark_surface( &$nodes ) {
    foreach ( $nodes as &$node ) {
        $s = $node['settings'] ?? [];
        if ( in_array( $node['elType'] ?? '', [ 'section', 'column', 'container' ], true ) ) {
            $color = $s['background_color'] ?? '';
            if ( preg_match( '/^#([a-f0-9]{6})$/i', $color, $match ) ) {
                $rgb = array_map( 'hexdec', str_split( $match[1], 2 ) );
                if ( min( $rgb ) >= 220 && empty( $s['background_image']['url'] ) && ( $s['background_background'] ?? 'classic' ) !== 'gradient' ) {
                    $classes = preg_split( '/\s+/', trim( $s['_css_classes'] ?? '' ), -1, PREG_SPLIT_NO_EMPTY );
                    if ( ! in_array( 'bv-page-surface', $classes, true ) ) { $classes[] = 'bv-page-surface'; }
                    $node['settings']['_css_classes'] = implode( ' ', $classes );
                }
            }
        }
        if ( ! empty( $node['elements'] ) ) { bv_mark_surface( $node['elements'] ); }
    }
}
foreach ( $posts as $post ) {
    $raw = get_post_meta( $post->ID, '_elementor_data', true );
    $nodes = json_decode( $raw, true );
    if ( ! is_array( $nodes ) ) { continue; }
    $before = $nodes;
    bv_mark_surface( $nodes );
    if ( $nodes === $before ) { continue; }
    $backup[$post->ID] = $raw;
    if ( ! get_option( 'bv_surface_backup_' . $post->ID ) ) { add_option( 'bv_surface_backup_' . $post->ID, $raw, '', false ); }
    update_post_meta( $post->ID, '_elementor_data', wp_slash( wp_json_encode( $nodes ) ) );
    delete_post_meta( $post->ID, '_elementor_element_cache' );
    if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) { ( new \Elementor\Core\Files\CSS\Post( $post->ID ) )->update(); }
    echo $post->ID . ': ' . $post->post_title . PHP_EOL;
}
