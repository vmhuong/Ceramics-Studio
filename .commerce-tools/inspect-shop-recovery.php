<?php
if ( PHP_SAPI !== 'cli' ) { exit; }
require dirname( __DIR__ ) . '/wp-load.php';
echo 'SHOP=' . wc_get_page_id( 'shop' ) . ' PRODUCTS=' . wp_count_posts( 'product' )->publish . PHP_EOL;
function bv_inspect_nodes( $nodes, $depth = 0 ) {
    foreach ( $nodes as $n ) {
        echo str_repeat( ' ', $depth ) . wp_json_encode( [ 'id' => $n['id'], 'type' => $n['widgetType'] ?? $n['elType'], 'settings' => $n['settings'] ?? [] ], JSON_UNESCAPED_UNICODE ) . PHP_EOL;
        bv_inspect_nodes( $n['elements'] ?? [], $depth + 1 );
    }
}
bv_inspect_nodes( json_decode( get_post_meta( wc_get_page_id( 'shop' ), '_elementor_data', true ), true ) ?: [] );
echo 'BACKUP:' . PHP_EOL;
$backup = get_option( 'bv_pottery_refinement_backup' );
bv_inspect_nodes( json_decode( $backup[553]['_elementor_data'][0] ?? '[]', true ) ?: [] );
