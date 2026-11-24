<?php
/**
 * VietQR Webhook Listener for Auto Order Approval (SePay / Casso / MBBank compatible)
 *
 * @package HelloElementorChild
 */

defined( 'ABSPATH' ) || exit;

add_action( 'rest_api_init', function () {
	register_rest_route( 'bv-vietqr/v1', '/webhook', [
		'methods'             => 'POST',
		'callback'            => 'bv_handle_vietqr_webhook',
		'permission_callback' => '__return_true',
	] );

	// Also register /sepay/v1/webhook for plug-and-play compatibility
	register_rest_route( 'sepay/v1', '/webhook', [
		'methods'             => 'POST',
		'callback'            => 'bv_handle_vietqr_webhook',
		'permission_callback' => '__return_true',
	] );
} );

function bv_handle_vietqr_webhook( WP_REST_Request $request ) {
	$gateway_settings = get_option( 'woocommerce_vietqr_settings', [] );
	$is_enabled       = ( $gateway_settings['webhook_enabled'] ?? 'yes' ) === 'yes';
	$secret_token     = $gateway_settings['webhook_token'] ?? '';
	$prefix           = $gateway_settings['prefix'] ?? 'HD';

	if ( ! $is_enabled ) {
		return new WP_REST_Response( [ 'success' => false, 'message' => 'Webhook is disabled in WooCommerce settings.' ], 403 );
	}

	// Authenticate token if configured
	if ( ! empty( $secret_token ) ) {
		$auth_header    = $request->get_header( 'authorization' ) ?: $request->get_header( 'x-api-key' );
		$token_param    = $request->get_param( 'token' ) ?: $request->get_param( 'api_key' );
		$provided_token = '';

		if ( $auth_header ) {
			$provided_token = trim( str_ireplace( [ 'Bearer ', 'Apikey ' ], '', $auth_header ) );
		} elseif ( $token_param ) {
			$provided_token = trim( $token_param );
		}

		if ( empty( $provided_token ) || ! hash_equals( $secret_token, $provided_token ) ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Unauthorized: Invalid or missing API Token.' ], 401 );
		}
	}

	$params = $request->get_json_params() ?: $request->get_params();

	// Support both SePay & Casso payload formats
	$content = $params['content'] ?? $params['description'] ?? $params['transactionContent'] ?? '';
	$amount  = (float) ( $params['transferAmount'] ?? $params['amount'] ?? $params['amountIn'] ?? 0 );
	$ref_id  = sanitize_text_field( $params['referenceCode'] ?? $params['id'] ?? $params['transaction_id'] ?? '' );

	if ( empty( $content ) ) {
		return new WP_REST_Response( [ 'success' => false, 'message' => 'Missing transaction content.' ], 400 );
	}

	// Extract Order ID using prefix (e.g. "HD 1234", "HD1234")
	$pattern  = '/' . preg_quote( $prefix, '/' ) . '\s*(\d+)/i';
	$order_id = 0;

	if ( preg_match( $pattern, $content, $matches ) ) {
		$order_id = (int) $matches[1];
	} elseif ( preg_match( '/\b(\d{2,8})\b/', $content, $matches ) ) {
		// Fallback: search for numbers that match an existing order
		$candidate_id = (int) $matches[1];
		if ( wc_get_order( $candidate_id ) ) {
			$order_id = $candidate_id;
		}
	}

	if ( ! $order_id ) {
		return new WP_REST_Response( [ 'success' => false, 'message' => 'Order ID not found in transaction content: ' . $content ], 200 );
	}

	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return new WP_REST_Response( [ 'success' => false, 'message' => 'Order #' . $order_id . ' does not exist.' ], 404 );
	}

	// If order is already completed or processing, acknowledge without error
	if ( in_array( $order->get_status(), [ 'processing', 'completed' ], true ) ) {
		return new WP_REST_Response( [ 'success' => true, 'message' => 'Order #' . $order_id . ' was already confirmed earlier.' ], 200 );
	}

	// Verify amount (allow tolerance of rounding)
	$order_total = (float) $order->get_total();
	if ( $amount < ( $order_total - 100 ) ) {
		$order->add_order_note( sprintf(
			'CẢNH BÁO VIETQR: Nhận được tiền chuyển khoản %s nhưng tổng đơn hàng là %s. Mã GD: %s. Nội dung: %s',
			wc_price( $amount ),
			wc_price( $order_total ),
			$ref_id,
			$content
		) );
		return new WP_REST_Response( [ 'success' => false, 'message' => 'Payment amount less than order total.' ], 200 );
	}

	// Successful payment: complete order
	$note = sprintf(
		'Thanh toán VietQR tự động thành công qua Webhook ngân hàng. Số tiền: %s. Mã GD ngân hàng: %s.',
		wc_price( $amount ),
		$ref_id ?: 'N/A'
	);
	$order->add_order_note( $note );
	$order->payment_complete( $ref_id );

	// Check if order contains only virtual/downloadable products (e.g. Workshop tickets)
	$is_virtual_only = true;
	foreach ( $order->get_items() as $item ) {
		$prod = $item->get_product();
		if ( $prod && ! $prod->is_virtual() && ! $prod->is_downloadable() ) {
			$is_virtual_only = false;
			break;
		}
	}

	if ( $is_virtual_only ) {
		$order->update_status( 'completed', 'Đơn hàng vé workshop tự động hoàn tất sau khi nhận thanh toán.' );
	}

	return new WP_REST_Response( [
		'success'  => true,
		'message'  => 'Order #' . $order_id . ' approved successfully.',
		'order_id' => $order_id,
		'status'   => $order->get_status(),
	], 200 );
}
