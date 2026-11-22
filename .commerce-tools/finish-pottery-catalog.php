<?php
if ( PHP_SAPI !== 'cli' ) { exit( 1 ); }
require dirname( __DIR__ ) . '/wp-load.php';
$shop_id = wc_get_page_id( 'shop' );
$raw = get_post_meta( $shop_id, '_elementor_data', true );
$nodes = json_decode( $raw, true, 512, JSON_THROW_ON_ERROR );
add_option( 'bv_shop_before_catalog_20260923', $raw, '', false );
function bv_catalog_pagination( &$nodes ) {
    foreach ( $nodes as &$node ) {
        if ( ( $node['widgetType'] ?? '' ) === 'wpr-woo-grid' ) {
            $node['settings']['layout_pagination'] = 'yes';
            $node['settings']['pagination_type'] = 'load-more';
            $node['settings']['query_posts_per_page'] = 12;
            $node['settings']['layout_image_crop_size'] = 'large';
            $node['settings']['pagination_load_more_text'] = 'Xem thêm sản phẩm';
            $node['settings']['pagination_finish_text'] = 'Bạn đã xem hết bộ sưu tập.';
        }
        if ( ! empty( $node['elements'] ) ) { bv_catalog_pagination( $node['elements'] ); }
    }
}
bv_catalog_pagination( $nodes );
update_post_meta( $shop_id, '_elementor_data', wp_slash( wp_json_encode( $nodes ) ) );
delete_post_meta( $shop_id, '_elementor_element_cache' );
if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) { \Elementor\Core\Files\CSS\Post::create( $shop_id )->update(); }
clean_post_cache( $shop_id );
$ids = get_posts( [ 'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => -1, 'fields' => 'ids', 'meta_key' => '_bv_demo_catalog', 'meta_value' => 'ceramics-20260923' ] );
$images = [];
foreach ( $ids as $id ) {
    $product = wc_get_product( $id );
    $images[] = $product->get_image_id();
    if ( preg_match( '/tranh|gach/', mb_strtolower( remove_accents( $product->get_name() ) ) ) ) {
        $term = get_term_by( 'slug', 'gom-trang-tri', 'product_cat' );
        $product->set_category_ids( [ $term->term_id ] );
        $story = 'Một tác phẩm gốm trang trí với nhịp điệu riêng của hình khối, đường nét và bề mặt men. Có thể đặt ở góc trưng bày hoặc phối trong một mảng tường nghệ thuật, kết hợp ánh sáng dịu để làm rõ các chi tiết.';
        $product->set_short_description( $story );
        $product->set_description( '<p>' . $story . '</p><h3>Thông tin sản phẩm</h3><ul><li>Tên mẫu: ' . esc_html( $product->get_name() ) . '.</li><li>Nhóm sản phẩm: gốm trang trí.</li><li>Kích thước, khung và phụ kiện treo cần được xác nhận khi thay bằng sản phẩm thực tế.</li></ul><h3>Chăm sóc và trưng bày</h3><p>Lau bụi bằng khăn mềm. Chọn bề mặt đặt chắc chắn; với sản phẩm treo tường, cần dùng phụ kiện phù hợp với trọng lượng và loại tường.</p><p class="bv-demo-notice">Sản phẩm demo: giá và tồn kho dùng để trải nghiệm cửa hàng. Ảnh tham khảo từ Anh Ceramics; cần xác nhận quyền sử dụng trước khi kinh doanh.</p>' );
        $product->save();
    }
    foreach ( array_merge( [ $product->get_image_id() ], $product->get_gallery_image_ids() ) as $image ) {
        if ( ! is_file( get_attached_file( $image ) ) ) { throw new RuntimeException( 'Missing local attachment ' . $image ); }
    }
}
if ( count( $ids ) !== 20 || count( array_unique( $images ) ) !== 20 ) { throw new RuntimeException( 'Expected 20 products with distinct primary images.' ); }
echo 'Verified: 20 new products, 20 distinct local primary images. Total published: ' . wp_count_posts( 'product' )->publish . PHP_EOL;
