<?php
/**
 * Custom Wabi-Sabi Email Templates & Distinct Subjects/Content
 * Separates Workshop Ticket emails from Ceramic Product Order emails.
 *
 * @package HelloElementorChild
 */

defined( 'ABSPATH' ) || exit;

/**
 * Determine whether a WooCommerce order is a Workshop booking.
 *
 * @param WC_Order|mixed $order Order instance.
 * @return bool
 */
function bv_is_workshop_order( $order ) {
	if ( ! is_a( $order, 'WC_Order' ) ) {
		return false;
	}
	foreach ( $order->get_items() as $item ) {
		if ( $item->get_meta( '_ws_date_raw' ) ) {
			return true;
		}
	}
	return false;
}

// 1. Brand palette & footer in WooCommerce emails (replaces default purple)
add_filter( 'option_woocommerce_email_base_color', function () { return '#B3592B'; } );
add_filter( 'option_woocommerce_email_background_color', function () { return '#FAF7F2'; } );
add_filter( 'option_woocommerce_email_body_background_color', function () { return '#FFFFFF'; } );
add_filter( 'option_woocommerce_email_text_color', function () { return '#2C2523'; } );
add_filter( 'option_woocommerce_email_footer_text', function () {
	return 'Ceramic Studio — Xưởng Gốm Mộc Bát Tràng | Thôn 1 Giang Cao, Gia Lâm, Hà Nội | Zalo: 0363 292 271';
} );

// Hide product thumbnails in emails (avoids broken localhost images in Gmail)
add_filter( 'woocommerce_email_order_items_args', function ( $args ) {
	$args['show_image'] = false;
	return $args;
} );

// 2. Avoid sending duplicate customer email when a Workshop order jumps from pending -> completed
add_filter( 'woocommerce_email_enabled_customer_processing_order', function ( $enabled, $order ) {
	if ( bv_is_workshop_order( $order ) ) {
		return false;
	}
	return $enabled;
}, 10, 2 );

// 3. Distinct Email Subjects
add_filter( 'woocommerce_email_subject_new_order', function ( $subject, $order ) {
	if ( ! is_a( $order, 'WC_Order' ) ) {
		return $subject;
	}
	$num  = $order->get_order_number();
	$name = trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() );
	if ( bv_is_workshop_order( $order ) ) {
		return sprintf( '[Ceramic Studio] Vé Workshop mới #%s — %s', $num, $name ?: 'Khách hàng' );
	}
	return sprintf( '[Ceramic Studio] Đơn hàng gốm mới #%s — %s', $num, $name ?: 'Khách hàng' );
}, 10, 2 );

add_filter( 'woocommerce_email_subject_customer_on_hold_order', function ( $subject, $order ) {
	if ( ! is_a( $order, 'WC_Order' ) ) {
		return $subject;
	}
	return sprintf( '[Ceramic Studio] Xác nhận đơn hàng #%s — Chờ thanh toán', $order->get_order_number() );
}, 10, 2 );

add_filter( 'woocommerce_email_subject_customer_processing_order', function ( $subject, $order ) {
	if ( ! is_a( $order, 'WC_Order' ) ) {
		return $subject;
	}
	return sprintf( '[Ceramic Studio] Đơn hàng #%s đang được chuẩn bị tại xưởng', $order->get_order_number() );
}, 10, 2 );

add_filter( 'woocommerce_email_subject_customer_completed_order', function ( $subject, $order ) {
	if ( ! is_a( $order, 'WC_Order' ) ) {
		return $subject;
	}
	$num = $order->get_order_number();
	if ( bv_is_workshop_order( $order ) ) {
		return sprintf( '[Ceramic Studio] Xác nhận Vé Workshop #%s — Lịch hẹn làm gốm của bạn', $num );
	}
	return sprintf( '[Ceramic Studio] Đơn hàng #%s đã hoàn tất — Cảm ơn bạn!', $num );
}, 10, 2 );

// 4. Distinct Email Headings
add_filter( 'woocommerce_email_heading_customer_on_hold_order', function ( $heading, $order ) {
	if ( ! is_a( $order, 'WC_Order' ) ) {
		return $heading;
	}
	return sprintf( 'Đơn Hàng #%s Đang Chờ Thanh Toán', $order->get_order_number() );
}, 10, 2 );

add_filter( 'woocommerce_email_heading_customer_processing_order', function ( $heading, $order ) {
	if ( ! is_a( $order, 'WC_Order' ) ) {
		return $heading;
	}
	return sprintf( 'Xưởng Đang Chuẩn Bị Đơn Hàng #%s', $order->get_order_number() );
}, 10, 2 );

add_filter( 'woocommerce_email_heading_customer_completed_order', function ( $heading, $order ) {
	if ( ! is_a( $order, 'WC_Order' ) ) {
		return $heading;
	}
	if ( bv_is_workshop_order( $order ) ) {
		return sprintf( 'Xác Nhận Vé Workshop #%s', $order->get_order_number() );
	}
	return sprintf( 'Đơn Hàng #%s Đã Hoàn Tất', $order->get_order_number() );
}, 10, 2 );

// 5. Custom Branded Intro & Workshop Ticket Card inside Email Body
add_action( 'woocommerce_email_before_order_table', function ( $order, $sent_to_admin, $plain_text, $email ) {
	if ( ! is_a( $order, 'WC_Order' ) || $sent_to_admin ) {
		return;
	}

	$customer_name = trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ) ?: 'Quý khách';
	$is_workshop   = bv_is_workshop_order( $order );
	$email_id      = is_object( $email ) && isset( $email->id ) ? $email->id : '';

	if ( $is_workshop ) {
		$ws_items = [];
		foreach ( $order->get_items() as $item ) {
			if ( $item->get_meta( '_ws_date_raw' ) ) {
				$ws_items[] = [
					'package' => $item->get_meta( 'Gói workshop' ) ?: $item->get_name(),
					'date'    => $item->get_meta( 'Ngày trải nghiệm' ),
					'slot'    => $item->get_meta( 'Ca học' ),
					'qty'     => $item->get_quantity(),
				];
			}
		}

		if ( $plain_text ) {
			echo "\nXin chào {$customer_name},\n";
			echo "Thanh toán của bạn đã được xác nhận! Dưới đây là thông tin Vé Workshop tại Ceramic Studio:\n";
			foreach ( $ws_items as $w ) {
				echo "- Gói: {$w['package']} | Ngày: {$w['date']} | Ca: {$w['slot']} | Số người: {$w['qty']}\n";
			}
			echo "Địa chỉ xưởng: Thôn 1 Giang Cao, Bát Tràng, Gia Lâm, Hà Nội\n";
			echo "Lưu ý: Vui lòng đến trước giờ hẹn 10 phút. Hỗ trợ Zalo: 0363 292 271.\n\n";
			return;
		}
		?>
		<div style="margin: 0 0 22px; font-size: 14.5px; line-height: 1.65; color: #2C2523;">
			<p style="margin: 0 0 12px;">Xin chào <strong><?php echo esc_html( $customer_name ); ?></strong>,</p>
			<p style="margin: 0;">Thanh toán của bạn đã được xác nhận thành công. Xưởng gốm đã giữ chỗ chính thức cho buổi trải nghiệm của bạn với thông tin vé dưới đây:</p>
		</div>

		<?php foreach ( $ws_items as $w ) : ?>
			<div style="margin: 0 0 22px; padding: 18px 20px; background: #FAF7F2; border: 1px solid #E2D2C3; border-left: 4px solid #B3592B; border-radius: 6px;">
				<div style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.2px; color: #B3592B; margin-bottom: 8px;">VÉ THAM GIA WORKSHOP LÀM GỐM</div>
				<table style="width: 100%; border-collapse: collapse; font-size: 14px; color: #2C2523;">
					<tr><td style="padding: 4px 0; color: #6E5344; width: 130px;">Gói trải nghiệm:</td><td><strong><?php echo esc_html( $w['package'] ); ?></strong></td></tr>
					<tr><td style="padding: 4px 0; color: #6E5344;">Ngày tham gia:</td><td><strong><?php echo esc_html( $w['date'] ); ?></strong></td></tr>
					<tr><td style="padding: 4px 0; color: #6E5344;">Ca học:</td><td><strong><?php echo esc_html( $w['slot'] ); ?></strong></td></tr>
					<tr><td style="padding: 4px 0; color: #6E5344;">Số người:</td><td><strong><?php echo esc_html( $w['qty'] ); ?> học viên</strong></td></tr>
					<tr><td style="padding: 4px 0; color: #6E5344;">Địa điểm:</td><td>Thôn 1 Giang Cao, Bát Tràng, Gia Lâm, Hà Nội</td></tr>
				</table>
				<p style="margin: 12px 0 0; font-size: 12.5px; color: #6E5344;">
					* Vui lòng đến trước giờ hẹn 10 phút. Cần đổi lịch hoặc chỉ đường, nhắn Zalo <strong>0363 292 271</strong> (Mr. Hướng).
				</p>
			</div>
		<?php endforeach; ?>
		<?php
		return;
	}

	// Regular Ceramic Product Order intro messages
	if ( $plain_text ) {
		return;
	}

	$intro = 'Cảm ơn bạn đã tin chọn các tác phẩm gốm thủ công tại Ceramic Studio.';
	if ( 'customer_on_hold_order' === $email_id ) {
		$intro = 'Xưởng đã tiếp nhận đơn hàng của bạn và đang chờ xác nhận thanh toán để tiến hành đóng gói chống sốc.';
	} elseif ( 'customer_processing_order' === $email_id ) {
		$intro = 'Đơn hàng của bạn đã được xác nhận. Các nghệ nhân tại xưởng đang kiểm tra kỹ men gốm và đóng gói chống sốc cẩn thận để gửi đến bạn.';
	} elseif ( 'customer_completed_order' === $email_id ) {
		$intro = 'Đơn hàng gốm của bạn đã hoàn tất. Ceramic Studio hy vọng tác phẩm sẽ mang lại sự bình yên và ấm áp cho không gian của bạn (bảo hành bể vỡ 100% trong quá trình vận chuyển).';
	}
	?>
	<div style="margin: 0 0 20px; padding: 14px 18px; background: #FAF7F2; border-left: 4px solid #B3592B; border-radius: 4px; font-size: 14px; line-height: 1.65; color: #2C2523;">
		<p style="margin: 0 0 8px;">Xin chào <strong><?php echo esc_html( $customer_name ); ?></strong>,</p>
		<p style="margin: 0;"><?php echo esc_html( $intro ); ?></p>
	</div>
	<?php
}, 5, 4 );
