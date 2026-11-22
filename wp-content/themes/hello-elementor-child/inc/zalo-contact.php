<?php
/**
 * Smart Zalo Contact Widget & Order Support - Ceramic Studio
 *
 * @package HelloElementorChild
 */

defined( 'ABSPATH' ) || exit;

add_action( 'wp_enqueue_scripts', function () {
	wp_enqueue_style( 'bv-zalo-contact', get_stylesheet_directory_uri() . '/assets/zalo-contact.css', [], filemtime( __DIR__ . '/../assets/zalo-contact.css' ) );
} );

// Floating Zalo Button on all frontend pages
add_action( 'wp_footer', function () {
	// Do not display in WP admin or elementor preview editor
	if ( is_admin() || ( function_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) ) {
		return;
	}

	$phone = '0363292271';
	$zalo_url = 'https://zalo.me/' . $phone;
	?>
	<div class="bv-zalo-floating" id="bv-zalo-floating">
		<a href="<?php echo esc_url( $zalo_url ); ?>" target="_blank" rel="noopener noreferrer" class="bv-zalo-btn" aria-label="Tư vấn Zalo với Xưởng gốm (0363292271)">
			<span class="bv-zalo-label">Tư vấn Zalo: 0363 292 271</span>
		</a>
	</div>
	<?php
} );

// Zalo Support Box on Order Thank You Page
add_action( 'woocommerce_thankyou', function ( $order_id ) {
	if ( ! $order_id ) {
		return;
	}

	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return;
	}

	$phone = '0363292271';
	$order_total = number_format( $order->get_total(), 0, ',', '.' ) . ' ₫';
	$zalo_url = 'https://zalo.me/' . $phone;
	?>
	<div class="bv-zalo-order-box">
		<div class="bv-zalo-order-content">
			<h4>Cần xác nhận đơn hàng hoặc dặn dò thêm?</h4>
			<p>Nhắn tin Zalo trực tiếp với xưởng qua số <strong>0363 292 271</strong> (Mr. Mạnh Hướng) để được hỗ trợ đóng gói nhanh chóng nhất.</p>
		</div>
		<div class="bv-zalo-order-action">
			<a href="<?php echo esc_url( $zalo_url ); ?>" target="_blank" rel="noopener noreferrer" class="bv-btn-zalo-chat">
				<span>Nhắn tin Zalo: 0363 292 271</span>
			</a>
		</div>
	</div>
	<?php
}, 20 );
