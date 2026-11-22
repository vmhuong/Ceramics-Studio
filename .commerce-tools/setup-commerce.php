<?php
/** Run once with PHP CLI. Original Elementor documents/options are kept in a non-autoloaded backup. */
if ( PHP_SAPI !== 'cli' ) {
	http_response_code( 403 );
	exit;
}
require dirname( __DIR__ ) . '/wp-load.php';

$source = json_decode( file_get_contents( __DIR__ . '/royal-woo-grid-v2.json' ), true );
if ( empty( $source['content'][0]['elements'][0]['elements'][0]['settings'] ) ) {
	throw new RuntimeException( 'Missing official Royal Woo Grid v2 template.' );
}
$header_id = 92;
$shop_id = 553;
$cart_id = 554;
$checkout_id = 555;
$option_names = [ 'woocommerce_shop_page_id', 'woocommerce_cart_page_id', 'woocommerce_checkout_page_id', 'wpr_override_woo_cart', 'woocommerce_coming_soon', 'woocommerce_flat_rate_settings' ];
$backup = [ 'posts' => [], 'options' => [], 'menu' => wp_get_nav_menu_items( 4 ) ];
foreach ( [ $header_id, 102, $shop_id, $cart_id, $checkout_id ] as $id ) {
	$backup['posts'][ $id ] = [ 'post' => get_post( $id, ARRAY_A ), 'meta' => get_post_meta( $id ) ];
}
foreach ( $option_names as $name ) {
	$backup['options'][ $name ] = get_option( $name );
}
add_option( 'bv_commerce_design_backup_20260914', $backup, '', false );

function bv_save_elementor_document( $id, $data ) {
	update_post_meta( $id, '_elementor_data', wp_slash( wp_json_encode( $data ) ) );
	update_post_meta( $id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $id, '_elementor_version', ELEMENTOR_VERSION );
	delete_post_meta( $id, '_elementor_element_cache' );
	delete_post_meta( $id, '_elementor_page_assets' );
	\Elementor\Core\Files\CSS\Post::create( $id )->update();
}

$header = json_decode( get_post_meta( $header_id, '_elementor_data', true ), true );
$header[0]['settings']['css_classes'] = 'bv-site-header';
$header[0]['elements'][0]['settings']['css_classes'] = 'bv-header-logo';
$header[0]['elements'][1]['settings']['css_classes'] = 'bv-header-nav';
$header[0]['elements'][2]['settings']['css_classes'] = 'bv-header-actions';
unset( $header[0]['elements'][2]['settings']['hide_mobile'] );
$header[0]['elements'][0]['elements'][0]['settings']['image']['url'] = wp_get_attachment_url( 108 );
$header[0]['elements'][0]['elements'][0]['settings']['retina_image'] = [ 'id' => '', 'url' => '' ];
$header[0]['elements'][1]['elements'][0]['settings']['toggle_btn_color'] = '#282828';
$booking = $header[0]['elements'][2]['elements'][0];
// Keep reruns idempotent if the cart widget was already inserted.
foreach ( $header[0]['elements'][2]['elements'] as $element ) {
	if ( ( $element['widgetType'] ?? '' ) === 'button' ) {
		$booking = $element;
	}
}
$booking['settings']['link']['url'] = get_permalink( 144 );
$booking['settings']['text'] = 'Đặt lịch ngay';
$booking['settings']['__globals__'] = [];
$booking['settings']['background_color'] = '#3D6357';
$booking['settings']['button_text_color'] = '#FFFFFF';
$booking['settings']['button_background_hover_color'] = '#2C4C41';
$booking['settings']['border_radius'] = [ 'unit' => 'px', 'top' => '4', 'right' => '4', 'bottom' => '4', 'left' => '4', 'isLinked' => true ];
$booking['settings']['button_box_shadow_box_shadow_type'] = '';
$booking['settings']['text_shadow_text_shadow_type'] = '';
$booking['settings']['typography_text_transform'] = 'none';
$booking['settings']['typography_font_size'] = [ 'unit' => 'px', 'size' => 14 ];
$booking['settings']['typography_font_size_mobile'] = [ 'unit' => 'px', 'size' => 12 ];
$booking['settings']['text_padding'] = [ 'unit' => 'px', 'top' => '13', 'right' => '19', 'bottom' => '13', 'left' => '19', 'isLinked' => false ];
$booking['settings']['text_padding_mobile'] = [ 'unit' => 'px', 'top' => '11', 'right' => '12', 'bottom' => '11', 'left' => '12', 'isLinked' => false ];
$header[0]['elements'][2]['elements'] = [
	[ 'id' => 'bvcart1', 'elType' => 'widget', 'widgetType' => 'shortcode', 'settings' => [ 'shortcode' => '[bookverse_header_cart]', '_css_classes' => 'bv-header-cart-widget' ], 'elements' => [] ],
	$booking,
];
bv_save_elementor_document( $header_id, $header );

$footer = json_decode( get_post_meta( 102, '_elementor_data', true ), true );
foreach ( $footer as &$section ) {
	foreach ( $section['elements'] as &$column ) {
		foreach ( $column['elements'] as &$widget ) {
			if ( ( $widget['widgetType'] ?? '' ) === 'wpr-logo' ) {
				$widget['settings']['_element_width'] = 'inherit';
				unset( $widget['settings']['_element_custom_width'] );
				$widget['settings']['image']['url'] = wp_get_attachment_url( 108 );
				$widget['settings']['retina_image'] = [ 'id' => '', 'url' => '' ];
			}
		}
	}
}
unset( $section, $column, $widget );
bv_save_elementor_document( 102, $footer );

$grid_section = $source['content'][0];
$grid_section['settings'] = [ 'padding' => [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ], 'layout' => 'full_width' ];
$grid_section['elements'][0]['settings']['_inline_size'] = null;
$grid = &$grid_section['elements'][0]['elements'][0];
$grid['settings']['_css_classes'] = 'bv-shop-grid';
$settings = &$grid['settings'];
$settings['query_selection'] = 'dynamic';
$settings['query_manual_products'] = [];
$settings['query_posts_per_page'] = 12;
$settings['query_orderby'] = 'date';
$settings['query_not_found_text'] = 'Chưa có sản phẩm trong bộ sưu tập này.';
$settings['layout_select'] = 'fitRows';
$settings['layout_columns'] = 3;
$settings['layout_columns_tablet'] = 2;
$settings['layout_columns_mobile'] = 1;
$settings['layout_image_crop_size'] = 'woocommerce_single';
$settings['layout_gutter_hr'] = [ 'unit' => 'px', 'size' => 28 ];
$settings['layout_gutter_vr'] = [ 'unit' => 'px', 'size' => 36 ];
$settings['layout_filters'] = 'yes';
$settings['filters_all_text'] = 'Tất cả sản phẩm';
$settings['filters_hide_empty'] = 'yes';
$settings['pagination_load_more_text'] = 'Xem thêm sản phẩm';
$settings['pagination_finish_text'] = 'Bạn đã xem hết bộ sưu tập.';
$settings['grid_item_padding'] = [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ];
$settings['grid_item_border_width'] = [ 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ];
$settings['title_typography_font_family'] = 'Inter';
$settings['title_typography_font_size'] = [ 'unit' => 'px', 'size' => 18 ];
$settings['title_typography_line_height'] = [ 'unit' => 'em', 'size' => 1.5 ];
$settings['title_margin'] = [ 'unit' => 'px', 'top' => '14', 'right' => '0', 'bottom' => '16', 'left' => '0', 'isLinked' => false ];
$settings['product_price_color'] = '#AF4B2F';
$settings['product_price_margin'] = [ 'unit' => 'px', 'top' => '14', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ];
$settings['categories_color'] = '#666B66';
$settings['categories_margin'] = [ 'unit' => 'px', 'top' => '8', 'right' => '0', 'bottom' => '16', 'left' => '0', 'isLinked' => false ];
$settings['categories_typography_letter_spacing'] = [ 'unit' => 'px', 'size' => 0 ];
$settings['add_to_cart_color'] = '#3D6357';
$settings['add_to_cart_color_hr'] = '#AF4B2F';
$settings['add_to_cart_margin'] = [ 'unit' => 'px', 'top' => '6', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => false ];
$settings['add_to_cart_typography_font_size'] = [ 'unit' => 'px', 'size' => 13 ];
$settings['filters_color_hr'] = '#3D6357';
$settings['pagination_bg_color'] = '#3D6357';
$settings['pagination_bg_color_hr'] = '#2C4C41';
foreach ( $settings['grid_elements'] as &$element ) {
	$element['element_addcart_simple_txt'] = 'Thêm vào giỏ';
	$element['element_addcart_grouped_txt'] = 'Xem sản phẩm';
	$element['element_addcart_variable_txt'] = 'Chọn mẫu';
	if ( empty( $element['element_select'] ) ) {
		$element['element_select'] = 'title';
	}
}
unset( $element, $settings, $grid );
$intro = [
	'id' => 'bvintro', 'elType' => 'section', 'settings' => [ 'layout' => 'full_width' ], 'isInner' => false,
	'elements' => [ [ 'id' => 'bvintrocol', 'elType' => 'column', 'settings' => [ '_column_size' => 100 ], 'isInner' => false,
		'elements' => [ [ 'id' => 'bvintrohtml', 'elType' => 'widget', 'widgetType' => 'html', 'settings' => [
			'html' => '<header class="bv-shop-intro"><p class="bv-eyebrow">Từ đôi bàn tay, đến ngôi nhà bạn</p><h1>Gốm thủ công</h1><p>Ly, chén, bình hoa và những món quà bằng gốm. Mỗi dáng hình, mỗi sắc men đều mang một nét riêng.</p></header>',
		], 'elements' => [] ] ],
	] ],
];
bv_save_elementor_document( $shop_id, [ $intro, $grid_section ] );
update_post_meta( $shop_id, '_elementor_template_type', 'wp-page' );
update_post_meta( $shop_id, '_wp_page_template', 'default' );
update_post_meta( $shop_id, '_bv_royal_template_source', 'https://royal-elementor-addons.com/library/premade-styles/woo-grid/v2.json' );
wp_update_post( [ 'ID' => $shop_id, 'post_title' => 'Cửa hàng gốm', 'post_content' => '' ] );
wp_update_post( [ 'ID' => $cart_id, 'post_title' => 'Giỏ hàng', 'post_content' => '[woocommerce_cart]' ] );
wp_update_post( [ 'ID' => $checkout_id, 'post_title' => 'Thanh toán', 'post_content' => '[woocommerce_checkout]' ] );
update_option( 'woocommerce_shop_page_id', $shop_id );
update_option( 'woocommerce_cart_page_id', $cart_id );
update_option( 'woocommerce_checkout_page_id', $checkout_id );
update_option( 'wpr_override_woo_cart', 'off' );
$legacy_shipping = get_option( 'woocommerce_flat_rate_settings', [] );
$legacy_shipping['enabled'] = 'no';
update_option( 'woocommerce_flat_rate_settings', $legacy_shipping );
if ( in_array( wp_parse_url( home_url(), PHP_URL_HOST ), [ 'localhost', '127.0.0.1' ], true ) ) {
	update_option( 'woocommerce_coming_soon', 'no' );
}
foreach ( wp_get_nav_menu_items( 4 ) as $item ) {
	if ( in_array( (int) $item->object_id, [ 539, 554 ], true ) || strpos( $item->url, '/gio-hang' ) !== false ) {
		wp_remove_object_terms( $item->ID, 4, 'nav_menu' );
	}
}
flush_rewrite_rules( false );
echo "Imported official Royal Woo Grid v2. Updated header, store, cart and checkout.\n";
echo 'Backup: bv_commerce_design_backup_20260914' . PHP_EOL;
