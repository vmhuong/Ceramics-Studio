<?php
/** Commerce presentation; orders and cart state remain managed by WooCommerce. */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_enqueue_scripts', function () {
	if ( ! function_exists( 'WC' ) ) {
		return;
	}
	wp_enqueue_style( 'elementor-icons' );
	wp_enqueue_script( 'wc-cart-fragments' );
	wp_enqueue_script( 'wc-add-to-cart' );
	wp_enqueue_style( 'bv-commerce', get_stylesheet_directory_uri() . '/assets/commerce.css', [ 'hello-elementor-child' ], time() );
	if ( is_account_page() ) {
		wp_enqueue_style( 'bv-account', get_stylesheet_directory_uri() . '/assets/account.css', [ 'bv-commerce' ], time() );
	}
	wp_enqueue_script( 'bv-commerce', get_stylesheet_directory_uri() . '/assets/commerce.js', [ 'jquery', 'wc-cart-fragments' ], time(), true );
}, 40 );

function bv_header_cart() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
		return '';
	}
	$count = WC()->cart->get_cart_contents_count();
	return '<a class="bv-header-cart" href="' . esc_url( wc_get_cart_url() ) . '" title="Xem giỏ hàng"><span class="bv-cart-label">Giỏ hàng</span> <span class="bv-cart-num">(<span class="bv-cart-count">' . esc_html( $count ) . '</span>)</span></a>';
}
function bv_header_account() {
	$logged_in = is_user_logged_in();
	$label = $logged_in ? 'Tài khoản' : 'Đăng nhập';
	return '<a class="bv-header-account' . ( $logged_in ? ' is-signed-in' : '' ) . '" href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '" title="' . esc_attr( $label ) . '"><span class="bv-account-label">' . esc_html( $label ) . '</span></a>';
}
add_shortcode( 'bookverse_header_cart', function () {
	return function_exists( 'WC' ) ? '<span class="bv-header-tools">' . bv_header_cart() . bv_header_account() . '</span>' : '';
} );
add_filter( 'woocommerce_add_to_cart_fragments', function ( $fragments ) {
	$fragments['a.bv-header-cart'] = bv_header_cart();
	return $fragments;
} );

add_filter( 'body_class', function ( $classes ) {
	if ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) ) {
		$classes[] = 'bv-commerce-page';
	}
	return $classes;
} );

// Resolve after Elementor and Royal's canvas filters, retaining the shared header/footer.
add_filter( 'template_include', function ( $template ) {
	if ( ! function_exists( 'is_woocommerce' ) || is_embed() ) {
		return $template;
	}
	if ( is_woocommerce() ) {
		return get_stylesheet_directory() . '/woocommerce.php';
	}
	if ( is_cart() || is_checkout() || is_account_page() ) {
		return get_stylesheet_directory() . '/commerce-page.php';
	}
	return $template;
}, 10000 );

function bv_commerce_steps() {
	$received = is_order_received_page();
	$step = is_cart() ? 1 : ( $received ? 3 : 2 );
	$steps = [ [ 'Giỏ hàng', wc_get_cart_url() ], [ 'Thanh toán', wc_get_checkout_url() ], [ 'Hoàn tất', '' ] ];
	echo '<ol class="bv-checkout-steps" aria-label="Các bước mua hàng">';
	foreach ( $steps as $index => $item ) {
		$number = $index + 1;
		echo '<li class="' . ( $number === $step ? 'is-current' : ( $number < $step ? 'is-complete' : '' ) ) . '"' . ( $number === $step ? ' aria-current="step"' : '' ) . '>';
		$linked = $number < $step && ! $received;
		echo $linked ? '<a href="' . esc_url( $item[1] ) . '">' : '<span>';
		echo '<b aria-hidden="true">' . esc_html( $number ) . '</b>' . esc_html( $item[0] );
		echo $linked ? '</a>' : '</span>';
		echo '</li>';
	}
	echo '</ol>';
}

add_action( 'woocommerce_before_quantity_input_field', function () {
	echo '<button type="button" class="bv-quantity-step" data-step="-1" aria-label="Giảm số lượng" title="Giảm số lượng">-</button>';
} );
add_action( 'woocommerce_after_quantity_input_field', function () {
	echo '<button type="button" class="bv-quantity-step" data-step="1" aria-label="Tăng số lượng" title="Tăng số lượng">+</button>';
} );

add_filter( 'woocommerce_cart_item_name', function ( $name, $item ) {
	if ( is_checkout() && ! is_wc_endpoint_url() && isset( $item['data'] ) && did_action( 'woocommerce_before_mini_cart' ) === did_action( 'woocommerce_after_mini_cart' ) ) {
		return '<span class="bv-order-product">' . $item['data']->get_image( 'woocommerce_thumbnail', [ 'class' => 'bv-order-product__image' ] ) . '<span>' . $name . '</span></span>';
	}
	return $name;
}, 10, 2 );

add_action( 'woocommerce_cart_is_empty', function () {
	echo '<div class="bv-empty-cart"><h2>Giỏ hàng đang trống</h2><p>Một món gốm nhỏ, thêm chút ấm áp cho mỗi ngày.</p></div>';
}, 5 );

add_filter( 'woocommerce_return_to_shop_text', function () { return 'Khám phá gốm thủ công'; } );
add_filter( 'gettext_with_context', function ( $translation, $text, $context, $domain ) {
	if ( 'woocommerce' === $domain && 'shipping packages' === $context ) {
		$labels = [ 'Shipment' => 'Giao hàng', 'Shipment %d' => 'Giao hàng %d' ];
		return $labels[ $text ] ?? $translation;
	}
	return $translation;
}, 10, 4 );

// Customize WooCommerce My Account Navigation Menu Items
add_filter( 'woocommerce_account_menu_items', function ( $items ) {
	unset( $items['downloads'] ); // Remove downloads for ceramic craft studio
	if ( isset( $items['dashboard'] ) ) {
		$items['dashboard'] = 'Tổng quan tài khoản';
	}
	if ( isset( $items['orders'] ) ) {
		$items['orders'] = 'Đơn hàng của tôi';
	}
	if ( isset( $items['edit-address'] ) ) {
		$items['edit-address'] = 'Sổ địa chỉ nhận gốm';
	}
	if ( isset( $items['edit-account'] ) ) {
		$items['edit-account'] = 'Thông tin & Mật khẩu';
	}
	if ( isset( $items['customer-logout'] ) ) {
		$items['customer-logout'] = 'Đăng xuất';
	}
	return $items;
} );

// Ensure shop archive displays exact multiples of 4 columns (12 products = 3 full rows)
add_filter( 'loop_shop_per_page', function ( $cols ) {
	return 12;
}, 20 );

add_action( 'pre_get_posts', function ( $query ) {
	if ( ! is_admin() && $query->is_main_query() && ( $query->is_post_type_archive( 'product' ) || $query->is_tax( get_object_taxonomies( 'product' ) ) ) ) {
		$query->set( 'posts_per_page', 12 );
	}
} );

require_once __DIR__ . '/shopping-panels.php';
require_once __DIR__ . '/product-detail.php';
