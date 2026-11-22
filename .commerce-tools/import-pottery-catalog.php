<?php
if ( PHP_SAPI !== 'cli' ) { exit( 1 ); }
require dirname( __DIR__ ) . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
$manifest = json_decode( file_get_contents( __DIR__ . '/pottery-catalog/manifest.json' ), true, 512, JSON_THROW_ON_ERROR );
$index = isset( $argv[1] ) ? (int) $argv[1] : -1;
if ( ! isset( $manifest[$index] ) ) { exit( "Provide a product index from 0 to 19.\n" ); }
$source = $manifest[$index];
$sku = 'DEMO-CERAMICS-' . str_pad( (string) ( $index + 1 ), 2, '0', STR_PAD_LEFT );
if ( wc_get_product_id_by_sku( $sku ) ) { echo $sku . " already imported.\n"; exit; }
$name = mb_strtolower( $source['title'] );
$name = mb_strtoupper( mb_substr( $name, 0, 1 ) ) . mb_substr( $name, 1 );
if ( 0 === $index ) { $name = 'Bình gốm dáng rượu - bộ sưu tập men pastel'; }
$images = [];
foreach ( $source['assets'] as $asset ) {
    $existing = get_posts( [ 'post_type' => 'attachment', 'post_status' => 'inherit', 'meta_key' => '_bv_source_image_url', 'meta_value' => $asset['url'], 'fields' => 'ids', 'posts_per_page' => 1 ] );
    if ( $existing ) { $images[] = $existing[0]; continue; }
    $local = __DIR__ . '/pottery-catalog/' . basename( $asset['file'] );
    if ( ! is_file( $local ) ) { throw new RuntimeException( 'Missing downloaded image.' ); }
    $tmp = wp_tempnam( basename( $local ) );
    if ( ! copy( $local, $tmp ) ) { throw new RuntimeException( 'Cannot stage image.' ); }
    $id = media_handle_sideload( [ 'name' => 'gom-demo-' . ( $index + 1 ) . '-' . basename( $local ), 'tmp_name' => $tmp ], 0, $name );
    if ( is_wp_error( $id ) ) { @unlink( $tmp ); throw new RuntimeException( $id->get_error_message() ); }
    update_post_meta( $id, '_wp_attachment_image_alt', $name . ' - ảnh ' . ( count( $images ) + 1 ) );
    update_post_meta( $id, '_bv_source_image_url', $asset['url'] );
    update_post_meta( $id, '_bv_source_page', $source['source'] );
    update_post_meta( $id, '_bv_image_usage', 'Demo reference image. Obtain permission or replace before commercial publication.' );
    $images[] = $id;
}
$key = mb_strtolower( remove_accents( $name ) );
$category = 'gom-trang-tri';
if ( preg_match( '/bo |set |hop |combo/', $key ) && 0 !== $index ) { $category = 'set-qua-tang'; }
elseif ( str_contains( $key, 'binh' ) ) { $category = 'binh-hoa'; }
elseif ( preg_match( '/chen|bat|tho/', $key ) ) { $category = 'chen-bat'; }
elseif ( str_contains( $key, 'dia' ) ) { $category = 'dia-gom'; }
elseif ( preg_match( '/ly |coc|mug/', $key ) ) { $category = 'ly-gom'; }
$categories = [ 'gom-trang-tri' => 'Gốm trang trí', 'set-qua-tang' => 'Set quà tặng', 'binh-hoa' => 'Bình hoa', 'chen-bat' => 'Chén bát', 'dia-gom' => 'Đĩa gốm', 'ly-gom' => 'Ly gốm' ];
$term = get_term_by( 'slug', $category, 'product_cat' );
if ( ! $term ) {
    $created = wp_insert_term( $categories[$category], 'product_cat', [ 'slug' => $category ] );
    if ( is_wp_error( $created ) ) { throw new RuntimeException( $created->get_error_message() ); }
    $term = get_term( $created['term_id'], 'product_cat' );
}
$stories = [
    'gom-trang-tri' => 'Một điểm nhấn gốm cho góc sống yêu thích. Bề mặt và đường nét tạo hình đem lại chiều sâu khi đặt cạnh chất liệu gỗ, vải hoặc những món đồ đơn sắc. Có thể trưng bày riêng để làm nổi bật hình dáng của sản phẩm.',
    'set-qua-tang' => 'Một bộ gốm để cùng nhau sắp đặt góc bàn ăn hoặc bàn trà. Các món được phối thành nhóm, gợi ý cho một bữa cơm gần gũi hoặc món quà dành cho người yêu gốm. Ảnh bộ sản phẩm giúp bạn hình dung cách phối cùng khăn bàn và đồ dùng sẵn có.',
    'binh-hoa' => 'Dáng bình bo tròn với phần cổ gọn tạo nên một đường nét thanh thoát. Đặt trên bàn, kệ sách hoặc cạnh cửa sổ để tạo điểm nhấn nhẹ nhàng; có thể phối cùng một nhành hoa khô. Các sắc men trong thư viện ảnh đem lại những cảm giác khác nhau cho cùng một dáng bình.',
    'chen-bat' => 'Dáng chén bát gọn gàng, dễ kết hợp thành bộ với đĩa và đồ dùng bàn ăn. Những đường cong ở thành và miệng tạo nét mềm mại khi bày trên nền khăn vải. Phối cùng đồ gốm đơn sắc để dành sự chú ý cho từng chi tiết bề mặt.',
    'dia-gom' => 'Đĩa gốm tạo một lớp nền đẹp cho cách bày bàn theo mùa. Đường viền và bề mặt men là điểm nhấn khi phối cùng chén nhỏ hoặc ly gốm. Có thể kết hợp nhiều kích thước để bàn ăn có nhịp điệu mà vẫn gọn gàng.',
    'ly-gom' => 'Một chiếc ly gốm dành cho góc cà phê hoặc bàn trà. Hình dáng và sắc men mang lại nét riêng khi đặt cạnh sách, khăn vải hoặc một chiếc đĩa lót. Phối cùng các món gốm trong bộ sưu tập để hoàn thiện góc nghỉ nhỏ.',
];
$product = 0 === $index ? new WC_Product_Variable() : new WC_Product_Simple();
$product->set_name( $name );
$product->set_sku( $sku );
$product->set_status( 'draft' );
$product->set_catalog_visibility( 'visible' );
$product->set_category_ids( [ $term->term_id ] );
$product->set_image_id( $images[0] );
$product->set_gallery_image_ids( array_slice( $images, 1 ) );
$product->set_short_description( $stories[$category] );
$product->set_description( '<p>' . esc_html( $stories[$category] ) . '</p><h3>Thông tin sản phẩm</h3><ul><li>Tên mẫu: ' . esc_html( $name ) . '.</li><li>Nhóm sản phẩm: ' . esc_html( $categories[$category] ) . '.</li><li>Chất liệu minh họa: gốm phủ men.</li>' . ( 0 === $index ? '<li>Kích thước tham khảo: Nhỏ 8 × 13 cm; Vừa 12 × 15 cm; Lớn 15 × 19 cm.</li>' : '<li>Kích thước và thành phần bộ: cần xác nhận khi thay bằng sản phẩm kinh doanh thực tế.</li>' ) . '</ul><h3>Chăm sóc gốm</h3><p>Lau nhẹ bằng khăn mềm, tránh va đập và thay đổi nhiệt độ đột ngột. Chỉ dùng với thực phẩm, lò vi sóng hoặc máy rửa bát khi đã có xác nhận phù hợp từ nhà sản xuất.</p><p class="bv-demo-notice">Sản phẩm demo: giá, tồn kho và các lựa chọn dùng để trải nghiệm cửa hàng. Ảnh tham khảo từ Anh Ceramics; không phải cam kết về sản phẩm đang kinh doanh.</p>' );
$product->set_reviews_allowed( true );
$product->update_meta_data( '_bv_demo_product', true );
$product->update_meta_data( '_bv_demo_catalog', 'ceramics-20260923' );
$product->update_meta_data( '_bv_source_page', $source['source'] );
if ( 0 === $index ) {
    $attributes = [];
    foreach ( [ 'Kích thước' => [ 'Nhỏ', 'Vừa', 'Lớn' ], 'Màu men' => [ 'San hô', 'Hồng phấn', 'Xanh sương' ] ] as $label => $values ) {
        $attribute = new WC_Product_Attribute();
        $attribute->set_name( $label );
        $attribute->set_options( $values );
        $attribute->set_visible( true );
        $attribute->set_variation( true );
        $attributes[] = $attribute;
    }
    $product->set_attributes( $attributes );
    $product->set_default_attributes( [ 'kich-thuoc' => 'Vừa', 'mau-men' => 'San hô' ] );
} else {
    $product->set_regular_price( (string) $source['price'] );
    $product->set_manage_stock( true );
    $product->set_stock_quantity( 12 );
}
$id = $product->save();
if ( 0 === $index ) {
    foreach ( [ 'Nhỏ' => 225000, 'Vừa' => 285000, 'Lớn' => 345000 ] as $size => $price ) {
        foreach ( [ 'San hô', 'Hồng phấn', 'Xanh sương' ] as $color_index => $color ) {
            $variation = new WC_Product_Variation();
            $variation->set_parent_id( $id );
            $variation->set_attributes( [ 'kich-thuoc' => $size, 'mau-men' => $color ] );
            $variation->set_regular_price( (string) $price );
            $variation->set_manage_stock( true );
            $variation->set_stock_quantity( 8 );
            $variation->set_image_id( $images[$color_index] );
            $variation->set_sku( $sku . '-' . sanitize_title( $size ) . '-' . ( $color_index + 1 ) );
            $variation->save();
        }
    }
    WC_Product_Variable::sync( $id );
    $product = wc_get_product( $id );
}
$product->set_status( 'publish' );
$product->save();
echo 'Created ' . $id . ': ' . $name . ' | ' . get_permalink( $id ) . PHP_EOL;
