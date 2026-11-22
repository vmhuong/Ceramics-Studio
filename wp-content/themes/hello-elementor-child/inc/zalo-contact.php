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
		<a href="<?php echo esc_url( $zalo_url ); ?>" target="_blank" rel="noopener noreferrer" class="bv-zalo-btn" aria-label="Chat Zalo với Xưởng gốm (0363292271)">
			<span class="bv-zalo-pulse" aria-hidden="true"></span>
			<span class="bv-zalo-icon" aria-hidden="true">
				<svg width="28" height="28" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M24 4C12.954 4 4 12.059 4 22c0 5.438 2.684 10.297 6.945 13.625l-2.094 6.782a1.5 1.5 0 001.914 1.843l7.781-3.11c1.72.563 3.562.86 5.454.86 11.046 0 20-8.059 20-18S35.046 4 24 4z" fill="#0068FF"/>
					<path d="M19.78 28.5h-5.46a1 1 0 01-1-1v-1a1 1 0 011-1h3.31l-4.14-5.26a1 1 0 01-.17-.59v-1.15a1 1 0 011-1h5.36a1 1 0 011 1v1a1 1 0 01-1 1h-3.21l4.14 5.25a1 1 0 01.17.6v1.15a1 1 0 01-1 1zm8.72 0h-1.5a1 1 0 01-.98-.8l-.34-1.7h-3.36l-.34 1.7a1 1 0 01-.98.8h-1.5a1 1 0 01-.98-1.2l3.2-11a1 1 0 01.98-.8h1.7a1 1 0 01.98.8l3.2 11a1 1 0 01-.98 1.2zm-3.22-4.5l-1.28-6.4-1.28 6.4h2.56zm8.5 4.5h-4.5a1 1 0 01-1-1v-11a1 1 0 011-1h1.5a1 1 0 011 1v9.5h3a1 1 0 011 1v.5a1 1 0 01-1 1zm9 0h-2.5a5.5 5.5 0 01-5.5-5.5v-2a5.5 5.5 0 015.5-5.5h2.5a5.5 5.5 0 015.5 5.5v2a5.5 5.5 0 01-5.5 5.5zm-2.5-11h-1.5a2.5 2.5 0 00-2.5 2.5v2a2.5 2.5 0 002.5 2.5h1.5a2.5 2.5 0 002.5-2.5v-2a2.5 2.5 0 00-2.5-2.5z" fill="#FFF"/>
				</svg>
			</span>
			<span class="bv-zalo-tooltip">Tư vấn Zalo</span>
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
		<div class="bv-zalo-order-icon">
			<svg width="40" height="40" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M24 4C12.954 4 4 12.059 4 22c0 5.438 2.684 10.297 6.945 13.625l-2.094 6.782a1.5 1.5 0 001.914 1.843l7.781-3.11c1.72.563 3.562.86 5.454.86 11.046 0 20-8.059 20-18S35.046 4 24 4z" fill="#0068FF"/>
				<path d="M19.78 28.5h-5.46a1 1 0 01-1-1v-1a1 1 0 011-1h3.31l-4.14-5.26a1 1 0 01-.17-.59v-1.15a1 1 0 011-1h5.36a1 1 0 011 1v1a1 1 0 01-1 1h-3.21l4.14 5.25a1 1 0 01.17.6v1.15a1 1 0 01-1 1zm8.72 0h-1.5a1 1 0 01-.98-.8l-.34-1.7h-3.36l-.34 1.7a1 1 0 01-.98.8h-1.5a1 1 0 01-.98-1.2l3.2-11a1 1 0 01.98-.8h1.7a1 1 0 01.98.8l3.2 11a1 1 0 01-.98 1.2zm-3.22-4.5l-1.28-6.4-1.28 6.4h2.56zm8.5 4.5h-4.5a1 1 0 01-1-1v-11a1 1 0 011-1h1.5a1 1 0 011 1v9.5h3a1 1 0 011 1v.5a1 1 0 01-1 1zm9 0h-2.5a5.5 5.5 0 01-5.5-5.5v-2a5.5 5.5 0 015.5-5.5h2.5a5.5 5.5 0 015.5 5.5v2a5.5 5.5 0 01-5.5 5.5zm-2.5-11h-1.5a2.5 2.5 0 00-2.5 2.5v2a2.5 2.5 0 002.5 2.5h1.5a2.5 2.5 0 002.5-2.5v-2a2.5 2.5 0 00-2.5-2.5z" fill="#FFF"/>
			</svg>
		</div>
		<div class="bv-zalo-order-content">
			<h4>Cần xác nhận đơn hàng hoặc dặn dò thêm?</h4>
			<p>Nhắn tin Zalo trực tiếp với xưởng qua số <strong>0363292271</strong> (Mạnh Hướng) để được hỗ trợ đóng gói nhanh chóng nhất.</p>
		</div>
		<div class="bv-zalo-order-action">
			<a href="<?php echo esc_url( $zalo_url ); ?>" target="_blank" rel="noopener noreferrer" class="bv-btn-zalo-chat">
				<span>Nhắn tin Zalo</span>
				<i class="eicon-arrow-right" aria-hidden="true"></i>
			</a>
		</div>
	</div>
	<?php
}, 20 );
