<?php
if ( PHP_SAPI !== 'cli' ) { exit; }
require dirname( __DIR__ ) . '/wp-load.php';

$snapshot = [];
foreach ( [92,553] as $id ) { $snapshot[$id] = get_post_meta( $id ); }
add_option( 'bv_pottery_refinement_backup', $snapshot, '', false );

$header = json_decode( get_post_meta(92, '_elementor_data', true), true );
$backup = get_option('bv_commerce_design_backup_20260914');
$original = json_decode($backup['posts'][92]['meta']['_elementor_data'][0], true);
$old_button = $original[0]['elements'][2]['elements'][0]['settings'];
foreach ($header[0]['elements'][2]['elements'] as &$widget) {
    if (($widget['widgetType'] ?? '') === 'button') {
        $widget['settings'] = $old_button;
        $widget['settings']['link']['url'] = get_permalink(144);
        $widget['settings']['typography_font_size_mobile'] = ['unit'=>'px','size'=>12];
        $widget['settings']['text_padding_mobile'] = ['unit'=>'px','top'=>'10','right'=>'10','bottom'=>'10','left'=>'10','isLinked'=>true];
    }
}
unset($widget);
$header[0]['elements'][1]['elements'][0]['settings']['mobile_menu_bg_color_focus'] = '#7C292D';
$header[0]['elements'][1]['elements'][0]['settings']['toggle_btn_color_hover'] = '#7C292D';

$shop = json_decode(get_post_meta(553, '_elementor_data', true), true);
function bv_warm_settings(&$nodes) {
    $colors = ['#3D6357'=>'#7C292D','#2C4C41'=>'#5B1E23','#AF4B2F'=>'#7C292D','#666B66'=>'#796853','#FFFFFF'=>'#FFFCF5'];
    foreach ($nodes as &$node) {
        foreach (($node['settings'] ?? []) as $key=>$value) {
            if (is_string($value) && isset($colors[strtoupper($value)])) $node['settings'][$key]=$colors[strtoupper($value)];
        }
        if (($node['widgetType'] ?? '') === 'wpr-woo-grid') {
            $node['settings']['query_posts_per_page']=12;
            $node['settings']['layout_gutter_hr']=['unit'=>'px','size'=>28];
            $node['settings']['add_to_cart_typography_font_size']=['unit'=>'px','size'=>13];
        }
        if (!empty($node['elements'])) bv_warm_settings($node['elements']);
    }
}
bv_warm_settings($shop);
foreach ([92=>$header,553=>$shop] as $id=>$document) {
    update_post_meta($id,'_elementor_data',wp_slash(wp_json_encode($document)));
    delete_post_meta($id,'_elementor_element_cache');
    \Elementor\Core\Files\CSS\Post::create($id)->update();
}

$products = [
 ['BAT-MEN-BIEN','Bát gốm men biển',165000,'chen-bat',364,'Bát gốm dáng tròn với lớp men xanh biển loang nhẹ, phần chân giữ vẻ mộc của đất.','Miệng bát tròn, lòng sâu vừa phải tạo cảm giác gần gũi cho bàn ăn. Sắc men chuyển từ xanh đậm sang xanh nhạt, kết hợp cùng phần đất mộc ở chân bát.','Đường kính 12 cm, cao 6 cm','1 bát'],
 ['CAP-BAT-MOC','Cặp bát gốm men loang',310000,'set-qua-tang',364,'Hai chiếc bát đồng điệu về dáng, khác nhau ở từng vệt men; một gợi ý quà tặng giản dị.','Bộ đôi dành cho góc bàn ăn nhỏ hoặc làm quà tân gia. Dáng tròn gọn dễ sắp xếp, lớp men loang tạo điểm nhấn tự nhiên trên nền đất mộc.','Mỗi bát: đường kính 12 cm, cao 6 cm','2 bát'],
 ['CA-MEN-LOANG','Ca gốm men loang có quai',285000,'ly-gom',367,'Ca gốm có quai lớn và miệng rót nhẹ, nổi bật với lớp men loang tông đất.','Thân ca đầy đặn, quai cong tạo điểm nhấn cho góc bếp. Những vệt men chảy ngẫu hứng mang lại vẻ riêng cho từng chiếc. Có thể trưng bày cùng hoa khô hoặc dùng trong bộ sưu tập gốm của bạn.','Cao 13 cm, dung tích tham khảo 450 ml','1 ca'],
 ['CHEN-CHAM-LAM','Chén gốm chấm men lam',125000,'chen-bat',377,'Nền men ngà kết hợp những chấm xanh lam phóng khoáng, gọn nhẹ và dễ phối bộ.','Họa tiết chấm lam được bố trí tự do trên nền men sáng. Dáng chén nhỏ thích hợp để hoàn thiện một bộ gốm bàn ăn mang nét trẻ trung.','Đường kính 9 cm, cao 6 cm','1 chén'],
 ['BO-CHEN-LAM','Bộ chén gốm nét lam',450000,'set-qua-tang',377,'Bốn chén gốm với họa tiết sọc và chấm lam, tạo nhịp điệu nhẹ nhàng cho bàn ăn.','Bộ chén phối các nét vẽ xanh lam trên nền ngà, mỗi chiếc mang một họa tiết riêng. Phù hợp làm quà cho người yêu gốm hoặc phối cùng đồ dùng bàn ăn đơn sắc.','Mỗi chén: đường kính 9 cm, cao 6 cm','4 chén'],
 ['DIA-NET-LAM','Đĩa gốm nhỏ họa tiết lam',95000,'dia-gom',423,'Đĩa nhỏ lòng nông, điểm những nét lam trên nền men ngà dịu mắt.','Dáng đĩa nhỏ xinh làm điểm nhấn cho bàn trà hoặc góc trưng bày. Họa tiết nét lam mang tinh thần tự do và mộc mạc của đồ gốm thủ công.','Đường kính 10 cm, cao 2 cm','1 đĩa'],
 ['BINH-MIEN-HOA','Bình gốm miệng hoa',390000,'binh-hoa',370,'Dáng bình mềm mại với miệng uốn lượn như cánh hoa, phù hợp cho một nhành hoa nhỏ.','Bình gốm gợi cảm giác thanh thoát nhờ phần miệng tạo hình mềm và thân bo tròn. Đặt riêng trên kệ hoặc phối cùng các bình khác để tạo một góc nhỏ có chiều sâu.','Cao tham khảo 18 cm','1 bình'],
 ['CHAU-CHAN-DUNG','Chậu gốm chân dung',320000,'gom-trang-tri',379,'Chậu gốm tạo hình khuôn mặt với sắc men ngà, một điểm nhấn vui mắt cho không gian sống.','Những đường nét khuôn mặt được cách điệu trên thân chậu, kết hợp cùng gam men sáng dễ phối nội thất. Có thể dùng như chậu trang trí bên ngoài một chậu cây nhỏ có khay lót.','Cao tham khảo 15 cm','1 chậu, không kèm cây'],
];
if (!term_exists('gom-trang-tri','product_cat')) wp_insert_term('Gốm trang trí','product_cat',['slug'=>'gom-trang-tri']);
foreach ($products as [$sku,$name,$price,$category,$image,$short,$story,$size,$included]) {
    $sku='DEMO-GOM-'.$sku;
    $existing=wc_get_product_id_by_sku($sku);
    if ($existing) { echo 'Exists '.$existing.PHP_EOL; continue; }
    $product=new WC_Product_Simple();
    $product->set_name($name);
    $product->set_sku($sku);
    $product->set_status('publish');
    $product->set_regular_price((string)$price);
    $product->set_short_description($short);
    $product->set_description('<p>'.$story.'</p><h2>Thông tin sản phẩm</h2><ul><li>Kích thước: '.$size.'.</li><li>Bộ sản phẩm: '.$included.'.</li><li>Chất liệu: gốm phủ men.</li></ul><h2>Chăm sóc gốm</h2><p>Lau rửa nhẹ bằng khăn hoặc miếng bọt biển mềm. Tránh va đập và thay đổi nhiệt độ đột ngột.</p><p><em>Sản phẩm demo: giá, kích thước và mô tả dùng để minh họa cửa hàng; ảnh từ thư viện hiện có.</em></p>');
    $product->set_image_id($image);
    $term=get_term_by('slug',$category,'product_cat');
    $product->set_category_ids([$term->term_id]);
    $product->set_manage_stock(true);
    $product->set_stock_quantity(12);
    $product->update_meta_data('_bv_demo_product',true);
    echo 'Created '.$product->save().' '.$name.PHP_EOL;
}
echo 'Published total: '.wp_count_posts('product')->publish.PHP_EOL;
