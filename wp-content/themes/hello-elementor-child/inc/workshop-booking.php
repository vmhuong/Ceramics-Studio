<?php
/**
 * Workshop Booking Engine for Ceramic Studio
 *
 * @package HelloElementorChild
 */

defined( 'ABSPATH' ) || exit;

// Register shortcode
add_shortcode( 'ceramic_workshop_booking', 'bv_render_workshop_booking' );

function bv_get_workshop_packages() {
	return [
		'ban-xoay' => [
			'id'          => 'ban-xoay',
			'name'        => 'Làm gốm bàn xoay thủ công',
			'price'       => 350000,
			'duration'    => '2.5 giờ',
			'description' => 'Học kỹ thuật định tâm, chuốt đất, kéo thành trên bàn xoay điện. Tự tay hoàn thiện 2 sản phẩm.',
			'badge'       => 'Phổ biến nhất',
		],
		'nan-tay' => [
			'id'          => 'nan-tay',
			'name'        => 'Nặn tay tự do & Điêu khắc',
			'price'       => 300000,
			'duration'    => '2.5 giờ',
			'description' => 'Tự do sáng tạo với kỹ thuật nặn khối, cuộn dải thừng, đắp nổi hoa văn mộc mạc.',
			'badge'       => 'Dễ trải nghiệm',
		],
		've-men' => [
			'id'          => 've-men',
			'name'        => 'Vẽ men màu nghệ thuật',
			'price'       => 280000,
			'duration'    => '2.0 giờ',
			'description' => 'Chọn 1 phôi gốm mộc có sẵn, thỏa sức phối màu men khoáng tự nhiên dưới sự hướng dẫn của nghệ nhân.',
			'badge'       => 'Thư giãn',
		],
		'combo-vip' => [
			'id'          => 'combo-vip',
			'name'        => 'Combo Trọn gói (Bàn xoay + Vẽ men)',
			'price'       => 550000,
			'duration'    => '4.0 giờ',
			'description' => 'Trải nghiệm trọn vẹn từ khâu tạo dáng trên bàn xoay đến khâu phối men hoàn thiện tác phẩm.',
			'badge'       => 'Đặc biệt',
		],
	];
}

function bv_get_workshop_slots() {
	return [
		'morning'   => [ 'id' => 'morning',   'label' => 'Ca Sáng',   'time' => '09:00 - 11:30', 'capacity' => 8 ],
		'afternoon' => [ 'id' => 'afternoon', 'label' => 'Ca Chiều', 'time' => '14:00 - 16:30', 'capacity' => 8 ],
		'evening'   => [ 'id' => 'evening',   'label' => 'Ca Tối',   'time' => '18:00 - 20:30', 'capacity' => 8 ],
	];
}

// Get booked counts for a given date and slot
function bv_get_booked_count( $date, $slot_id ) {
	$bookings = get_option( 'bv_workshop_bookings', [] );
	return isset( $bookings[ $date ][ $slot_id ] ) ? (int) $bookings[ $date ][ $slot_id ] : 0;
}

// Record a booking
function bv_add_booking_seat( $date, $slot_id, $seats = 1 ) {
	$bookings = get_option( 'bv_workshop_bookings', [] );
	if ( ! isset( $bookings[ $date ] ) ) {
		$bookings[ $date ] = [];
	}
	$current = isset( $bookings[ $date ][ $slot_id ] ) ? (int) $bookings[ $date ][ $slot_id ] : 0;
	$bookings[ $date ][ $slot_id ] = $current + (int) $seats;
	update_option( 'bv_workshop_bookings', $bookings );
}

// Release seats for expired unpaid pending Workshop orders (> 10 minutes)
function bv_release_single_workshop_order( $order ) {
	if ( ! is_a( $order, 'WC_Order' ) || 'pending' !== $order->get_status() ) {
		return false;
	}
	if ( 'yes' === $order->get_meta( '_ws_seats_released' ) ) {
		return false;
	}
	$created = $order->get_date_created();
	if ( ! $created || ( time() - $created->getTimestamp() ) < 600 ) {
		return false;
	}

	$bookings = get_option( 'bv_workshop_bookings', [] );
	$changed  = false;
	foreach ( $order->get_items() as $item ) {
		$date    = $item->get_meta( '_ws_date_raw' );
		$slot_id = $item->get_meta( '_ws_slot_id' );
		$qty     = (int) $item->get_quantity();
		if ( $date && $slot_id && isset( $bookings[ $date ][ $slot_id ] ) ) {
			$bookings[ $date ][ $slot_id ] = max( 0, (int) $bookings[ $date ][ $slot_id ] - $qty );
			$changed = true;
		}
	}
	if ( $changed ) {
		update_option( 'bv_workshop_bookings', $bookings );
	}
	$order->update_meta_data( '_ws_seats_released', 'yes' );
	$order->update_status( 'cancelled', 'Tự động hủy giữ chỗ Workshop do quá 10 phút chưa thanh toán.' );
	$order->save();
	return true;
}

function bv_release_expired_workshop_seats( $order_id = 0 ) {
	if ( ! function_exists( 'wc_get_order' ) ) {
		return;
	}
	if ( $order_id > 0 ) {
		$order = wc_get_order( (int) $order_id );
		if ( $order ) {
			bv_release_single_workshop_order( $order );
		}
		return;
	}
	if ( get_transient( 'bv_ws_cleanup_lock' ) ) {
		return;
	}
	set_transient( 'bv_ws_cleanup_lock', 1, 60 );
	$pending_orders = wc_get_orders( [
		'status'       => 'pending',
		'limit'        => 20,
		'date_created' => '<' . ( time() - 600 ),
	] );
	foreach ( $pending_orders as $order ) {
		bv_release_single_workshop_order( $order );
	}
}
add_action( 'bv_release_expired_workshop_order', 'bv_release_expired_workshop_seats' );

add_action( 'wp_enqueue_scripts', function () {
	wp_register_style( 'bv-workshop-booking', get_stylesheet_directory_uri() . '/assets/workshop-booking.css', [ 'bv-commerce' ], filemtime( __DIR__ . '/../assets/workshop-booking.css' ) );
	wp_register_script( 'bv-workshop-booking', get_stylesheet_directory_uri() . '/assets/workshop-booking.js', [ 'jquery' ], filemtime( __DIR__ . '/../assets/workshop-booking.js' ), true );
	wp_localize_script( 'bv-workshop-booking', 'bvWorkshopData', [
		'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
		'nonce'     => wp_create_nonce( 'bv_workshop_nonce' ),
		'cartUrl'   => wc_get_checkout_url(),
	] );
} );

function bv_render_workshop_booking() {
	if ( ! function_exists( 'WC' ) ) {
		return '<p>WooCommerce cần được kích hoạt để sử dụng hệ thống đặt lịch workshop.</p>';
	}

	wp_enqueue_style( 'bv-workshop-booking' );
	wp_enqueue_script( 'bv-workshop-booking' );
	bv_release_expired_workshop_seats();

	$packages = bv_get_workshop_packages();
	$slots    = bv_get_workshop_slots();
	$today    = date( 'Y-m-d' );
	$max_date = date( 'Y-m-d', strtotime( '+30 days' ) );

	ob_start();
	?>
	<div class="bv-workshop-app" id="bv-workshop-booking">
		<div class="bv-ws-intro">
			<span class="bv-ws-tag">Trải nghiệm thủ công</span>
			<h2>Đặt Lịch Workshop Làm Gốm</h2>
			<p>Dành một buổi rời xa màn hình, chạm tay vào đất sét mộc và tự tạo nên tác phẩm gốm mang dấu ấn của riêng bạn.</p>
		</div>

		<!-- STEPPER PROGRESS BAR -->
		<ol class="bv-ws-steps-nav" aria-label="Các bước đặt lịch workshop">
			<li class="is-current" data-step="1">
				<button type="button" class="bv-ws-step-btn" data-step-target="1">
					<b aria-hidden="true">1</b>
					<span>Chọn gói</span>
				</button>
			</li>
			<li data-step="2">
				<button type="button" class="bv-ws-step-btn" data-step-target="2">
					<b aria-hidden="true">2</b>
					<span>Chọn lịch</span>
				</button>
			</li>
			<li data-step="3">
				<button type="button" class="bv-ws-step-btn" data-step-target="3">
					<b aria-hidden="true">3</b>
					<span>Thông tin</span>
				</button>
			</li>
			<li data-step="4">
				<button type="button" class="bv-ws-step-btn" data-step-target="4">
					<b aria-hidden="true">4</b>
					<span>Thanh toán QR</span>
				</button>
			</li>
		</ol>

		<form id="bv-ws-form" class="bv-ws-form" method="post">
			<!-- BƯỚC 1: CHỌN GÓI TRẢI NGHIỆM -->
			<div class="bv-ws-wizard-panel is-active" data-panel="1">
				<div class="bv-panel-header">
					<h3 class="bv-panel-title">1. Chọn gói trải nghiệm gốm mộc</h3>
					<p class="bv-panel-desc">Mỗi gói học được thiết kế phù hợp cho cả người mới bắt đầu và người muốn sáng tạo chuyên sâu.</p>
				</div>
				<div class="bv-package-grid" role="radiogroup" aria-label="Gói trải nghiệm Workshop">
					<?php foreach ( $packages as $pkg ) : ?>
						<label class="bv-package-card">
							<input type="radio" name="ws_package" value="<?php echo esc_attr( $pkg['id'] ); ?>" data-name="<?php echo esc_attr( $pkg['name'] ); ?>" data-price="<?php echo esc_attr( $pkg['price'] ); ?>" data-duration="<?php echo esc_attr( $pkg['duration'] ); ?>" required <?php checked( $pkg['id'], 'ban-xoay' ); ?>>
							<div class="bv-package-content">
								<?php if ( ! empty( $pkg['badge'] ) ) : ?>
									<span class="bv-pkg-badge"><?php echo esc_html( $pkg['badge'] ); ?></span>
								<?php endif; ?>
								<h4 class="bv-pkg-title"><?php echo esc_html( $pkg['name'] ); ?></h4>
								<span class="bv-pkg-duration">Thời lượng: <?php echo esc_html( $pkg['duration'] ); ?></span>
								<p class="bv-pkg-desc"><?php echo esc_html( $pkg['description'] ); ?></p>
								<div class="bv-pkg-price"><?php echo number_format( $pkg['price'], 0, ',', '.' ); ?> ₫ <small>/ học viên</small></div>
							</div>
						</label>
					<?php endforeach; ?>
				</div>
				<div class="bv-ws-wizard-actions bv-ws-wizard-actions--single">
					<button type="button" class="bv-ws-btn-next" data-goto="2">
						Tiếp tục: Chọn ngày &amp; ca học →
					</button>
				</div>
			</div>

			<!-- BƯỚC 2: CHỌN NGÀY & CA HỌC -->
			<div class="bv-ws-wizard-panel" data-panel="2" style="display: none;">
				<div class="bv-panel-header">
					<h3 class="bv-panel-title">2. Chọn ngày &amp; ca học</h3>
					<div class="bv-ws-chosen-pill" id="bv-ws-chosen-pkg-badge">
						<span>Đang chọn: </span><strong id="bv-ws-summary-pkg-name">Làm gốm bàn xoay thủ công</strong>
						<button type="button" class="bv-ws-change-link" data-goto="1">Đổi gói</button>
					</div>
				</div>

				<div class="bv-ws-schedule-grid">
					<!-- Chọn ngày -->
					<div class="bv-date-picker-wrap">
						<label for="bv-ws-date" class="bv-field-label">Ngày dự kiến tham gia:</label>
						<div class="bv-input-wrap">
							<input type="date" id="bv-ws-date" name="ws_date" min="<?php echo esc_attr( $today ); ?>" max="<?php echo esc_attr( $max_date ); ?>" value="<?php echo esc_attr( $today ); ?>" required>
						</div>
						<small class="bv-hint">Xưởng mở cửa tất cả các ngày trong tuần. Ca Tối chỉ áp dụng Thứ 6, Thứ 7 &amp; Chủ Nhật.</small>
					</div>

					<!-- Chọn ca -->
					<div class="bv-slots-wrapper">
						<label class="bv-field-label">Chọn ca học &amp; Khung giờ:</label>
						<div class="bv-slots-grid" id="bv-slots-container" role="radiogroup" aria-label="Ca học">
							<?php foreach ( $slots as $slot ) : 
								$booked = bv_get_booked_count( $today, $slot['id'] );
								$remain = max( 0, $slot['capacity'] - $booked );
								$is_full = $remain <= 0;
							?>
								<label class="bv-slot-card <?php echo $is_full ? 'is-full' : ''; ?>">
									<input type="radio" name="ws_slot" value="<?php echo esc_attr( $slot['id'] ); ?>" data-label="<?php echo esc_attr( $slot['label'] . ' (' . $slot['time'] . ')' ); ?>" <?php disabled( $is_full ); ?> <?php checked( $slot['id'], 'morning' ); ?>>
									<div class="bv-slot-content">
										<span class="bv-slot-name"><?php echo esc_html( $slot['label'] ); ?></span>
										<strong class="bv-slot-time"><?php echo esc_html( $slot['time'] ); ?></strong>
										<span class="bv-slot-status <?php echo $remain <= 2 ? 'is-urgency' : ''; ?>">
											<?php echo $is_full ? 'Đã kín chỗ' : 'Còn ' . esc_html( $remain ) . ' chỗ'; ?>
										</span>
									</div>
								</label>
							<?php endforeach; ?>
						</div>
					</div>
				</div>

				<div class="bv-ws-wizard-actions">
					<button type="button" class="bv-ws-btn-prev" data-goto="1">
						← Quay lại chọn gói
					</button>
					<button type="button" class="bv-ws-btn-next" data-goto="3">
						Tiếp tục: Xác nhận đặt chỗ →
					</button>
				</div>
			</div>

			<!-- BƯỚC 3: XÁC NHẬN THÔNG TIN & ĐẶT VÉ -->
			<div class="bv-ws-wizard-panel" data-panel="3" style="display: none;">
				<div class="bv-panel-header">
					<h3 class="bv-panel-title">3. Xác nhận thông tin &amp; Đặt vé</h3>
					<p class="bv-panel-desc">Kiểm tra thông tin ca học và lựa chọn số lượng người tham gia trải nghiệm.</p>
				</div>

				<!-- Booking Confirmation Card -->
				<div class="bv-ws-confirm-card">
					<div class="bv-confirm-row">
						<div class="bv-confirm-col">
							<span class="bv-confirm-label">Gói trải nghiệm</span>
							<strong id="bv-conf-pkg">Làm gốm bàn xoay thủ công</strong>
							<small id="bv-conf-duration">Thời lượng: 2.5 giờ</small>
						</div>
						<div class="bv-confirm-col">
							<span class="bv-confirm-label">Thời gian tham gia</span>
							<strong id="bv-conf-date">Hôm nay</strong>
							<small id="bv-conf-slot">Ca Sáng (09:00 - 11:30)</small>
						</div>
						<div class="bv-confirm-col bv-confirm-col--action">
							<button type="button" class="bv-ws-change-btn" data-goto="2">Thay đổi lịch</button>
						</div>
					</div>
				</div>

				<div class="bv-attendees-wrap">
					<div class="bv-counter-box">
						<label for="bv-ws-qty">Số người tham gia:</label>
						<div class="bv-counter-control">
							<button type="button" class="bv-qty-btn" data-action="minus" aria-label="Giảm số người">-</button>
							<input type="number" id="bv-ws-qty" name="ws_qty" value="1" min="1" max="8" readonly>
							<button type="button" class="bv-qty-btn" data-action="plus" aria-label="Tăng số người">+</button>
						</div>
						<span class="bv-remain-note" id="bv-ws-remain-hint">Còn 8 chỗ</span>
					</div>
					<div class="bv-summary-box">
						<div class="bv-sum-row">
							<span>Tạm tính:</span>
							<strong id="bv-ws-total" class="bv-total-price">350.000 ₫</strong>
						</div>
						<small>Đã bao gồm trọn gói: Đất sét không giới hạn, dụng cụ tạo tác, men khoáng tự nhiên, nung 2 lần và trà thảo mộc.</small>
					</div>
				</div>

				<?php
				$cur_user       = is_user_logged_in() ? wp_get_current_user() : null;
				$prefill_name   = $cur_user ? trim( $cur_user->display_name ) : '';
				$prefill_email  = $cur_user ? $cur_user->user_email : '';
				$prefill_phone  = $cur_user ? get_user_meta( $cur_user->ID, 'billing_phone', true ) : '';
				?>
				<div class="bv-ws-customer-box">
					<h4 class="bv-ws-customer-heading">Thông tin người tham gia</h4>
					<p class="bv-ws-customer-sub">Xưởng giữ chỗ và gửi mã xác nhận qua Zalo / Email của bạn (không cần nhập địa chỉ giao hàng).</p>
					<div class="bv-ws-customer-grid">
						<div class="bv-ws-field">
							<label for="bv-ws-name">Họ và tên <span class="bv-req">*</span></label>
							<input type="text" id="bv-ws-name" name="ws_name" value="<?php echo esc_attr( $prefill_name ); ?>" placeholder="Ví dụ: Nguyễn Minh An" required>
						</div>
						<div class="bv-ws-field">
							<label for="bv-ws-phone">Số điện thoại / Zalo <span class="bv-req">*</span></label>
							<input type="tel" id="bv-ws-phone" name="ws_phone" value="<?php echo esc_attr( $prefill_phone ); ?>" placeholder="Ví dụ: 0912 345 678" required>
						</div>
						<div class="bv-ws-field">
							<label for="bv-ws-email">Email nhận vé</label>
							<input type="email" id="bv-ws-email" name="ws_email" value="<?php echo esc_attr( $prefill_email ); ?>" placeholder="email@example.com">
						</div>
						<div class="bv-ws-field">
							<label for="bv-ws-note">Ghi chú thêm</label>
							<input type="text" id="bv-ws-note" name="ws_note" placeholder="Ví dụ: Đi cùng trẻ nhỏ, khung giờ mong muốn...">
						</div>
					</div>
				</div>

				<div class="bv-ws-wizard-actions">
					<button type="button" class="bv-ws-btn-prev" data-goto="2">
						← Quay lại chọn lịch
					</button>
					<button type="submit" id="bv-ws-submit" class="bv-btn-book">
						Tạo Mã QR Thanh Toán →
					</button>
				</div>
				<div class="bv-ws-msg-box">
					<span id="bv-ws-message" class="bv-ws-message" role="status"></span>
				</div>
			</div>

			<!-- BƯỚC 4: THANH TOÁN VIETQR TẠI CHỖ & XÁC NHẬN WEBHOOK -->
			<div class="bv-ws-wizard-panel" data-panel="4" style="display: none;">
				<div class="bv-ws-qr-panel" id="bv-ws-qr-container">
					<!-- Được JS điền dữ liệu VietQR sau khi tạo đơn thành công -->
				</div>
			</div>
		</form>
	</div>
	<?php
	return ob_get_clean();
}

// AJAX: Fetch slot capacity for selected date
add_action( 'wp_ajax_bv_get_date_slots', 'bv_ajax_get_date_slots' );
add_action( 'wp_ajax_nopriv_bv_get_date_slots', 'bv_ajax_get_date_slots' );

function bv_ajax_get_date_slots() {
	check_ajax_referer( 'bv_workshop_nonce', 'nonce' );
	bv_release_expired_workshop_seats();
	$date = isset( $_POST['date'] ) ? sanitize_text_field( wp_unslash( $_POST['date'] ) ) : date( 'Y-m-d' );
	$slots = bv_get_workshop_slots();
	$day_of_week = (int) date( 'w', strtotime( $date ) ); // 0 = Sun, 5 = Fri, 6 = Sat

	$result = [];
	foreach ( $slots as $id => $s ) {
		// Evening slot only on Fri(5), Sat(6), Sun(0)
		$available_on_day = ( $id !== 'evening' ) || in_array( $day_of_week, [ 0, 5, 6 ], true );
		$booked = bv_get_booked_count( $date, $id );
		$remain = $available_on_day ? max( 0, $s['capacity'] - $booked ) : 0;

		$result[] = [
			'id'        => $id,
			'label'     => $s['label'],
			'time'      => $s['time'],
			'capacity'  => $s['capacity'],
			'remain'    => $remain,
			'is_open'   => $available_on_day,
			'is_full'   => $remain <= 0,
		];
	}

	wp_send_json_success( $result );
}

// AJAX: Create WooCommerce order directly with VietQR (without clearing shopping cart)
add_action( 'wp_ajax_bv_book_workshop', 'bv_ajax_book_workshop' );
add_action( 'wp_ajax_nopriv_bv_book_workshop', 'bv_ajax_book_workshop' );

function bv_ajax_book_workshop() {
	check_ajax_referer( 'bv_workshop_nonce', 'nonce' );

	$package_id = sanitize_text_field( wp_unslash( $_POST['package'] ?? 'ban-xoay' ) );
	$date       = sanitize_text_field( wp_unslash( $_POST['date'] ?? '' ) );
	$slot_id    = sanitize_text_field( wp_unslash( $_POST['slot'] ?? '' ) );
	$qty        = max( 1, (int) ( $_POST['qty'] ?? 1 ) );
	$name       = sanitize_text_field( wp_unslash( $_POST['name'] ?? '' ) );
	$phone      = sanitize_text_field( wp_unslash( $_POST['phone'] ?? '' ) );
	$email      = sanitize_email( wp_unslash( $_POST['email'] ?? '' ) );
	$note       = sanitize_textarea_field( wp_unslash( $_POST['note'] ?? '' ) );

	$packages = bv_get_workshop_packages();
	$slots    = bv_get_workshop_slots();

	if ( ! isset( $packages[ $package_id ] ) || ! isset( $slots[ $slot_id ] ) || empty( $date ) ) {
		wp_send_json_error( [ 'message' => 'Thông tin đặt lịch không hợp lệ.' ] );
	}
	if ( empty( $name ) || empty( $phone ) ) {
		wp_send_json_error( [ 'message' => 'Vui lòng nhập đầy đủ họ tên và số điện thoại / Zalo.' ] );
	}

	$package = $packages[ $package_id ];
	$slot    = $slots[ $slot_id ];

	// Check remaining capacity
	$booked = bv_get_booked_count( $date, $slot_id );
	$remain = max( 0, $slot['capacity'] - $booked );
	if ( $qty > $remain ) {
		wp_send_json_error( [ 'message' => sprintf( 'Ca học này chỉ còn lại %d chỗ trống.', $remain ) ] );
	}

	$product_id = bv_get_or_create_workshop_product( $package );
	$product    = wc_get_product( $product_id );
	if ( ! $product ) {
		wp_send_json_error( [ 'message' => 'Không tìm thấy gói workshop. Vui lòng thử lại.' ] );
	}

	// Create WooCommerce order directly without touching WC()->cart
	$order = wc_create_order( [
		'customer_id' => get_current_user_id(),
	] );
	if ( is_wp_error( $order ) ) {
		wp_send_json_error( [ 'message' => 'Không thể khởi tạo đơn đặt vé. Vui lòng thử lại.' ] );
	}

	$item_id = $order->add_product( $product, $qty, [
		'subtotal' => (float) $package['price'] * $qty,
		'total'    => (float) $package['price'] * $qty,
	] );

	$slot_label     = $slot['label'] . ' (' . $slot['time'] . ')';
	$date_formatted = date( 'd/m/Y', strtotime( $date ) );
	$item           = $order->get_item( $item_id );
	if ( $item ) {
		$item->add_meta_data( 'Gói workshop', $package['name'], true );
		$item->add_meta_data( 'Ngày trải nghiệm', $date_formatted, true );
		$item->add_meta_data( 'Ca học', $slot_label, true );
		$item->add_meta_data( '_ws_date_raw', $date, true );
		$item->add_meta_data( '_ws_slot_id', $slot_id, true );
		$item->add_meta_data( '_ws_attendees', $qty, true );
		$item->save();
	}

	$order->set_billing_first_name( $name );
	$order->set_billing_phone( $phone );
	if ( ! empty( $email ) && is_email( $email ) ) {
		$order->set_billing_email( $email );
	}
	if ( ! empty( $note ) ) {
		$order->set_customer_note( $note );
	}

	$order->set_payment_method( 'vietqr' );
	$order->set_payment_method_title( 'Chuyển khoản VietQR (Quét mã tự động)' );
	$order->calculate_totals();
	$order->set_status( 'pending', 'Đặt lịch Workshop trực tiếp — chờ thanh toán chuyển khoản VietQR.' );
	$order->save();

	// Reserve seats immediately and auto-release if unpaid after 10 minutes
	bv_add_booking_seat( $date, $slot_id, $qty );
	wp_schedule_single_event( time() + 600, 'bv_release_expired_workshop_order', [ $order->get_id() ] );

	// Build VietQR payload using gateway settings
	$vietqr_settings = get_option( 'woocommerce_vietqr_settings', [] );
	$bank_id         = $vietqr_settings['bank_id'] ?? 'MB';
	$account_no      = $vietqr_settings['account_no'] ?? '0363292271';
	$account_name    = $vietqr_settings['account_name'] ?? 'VU MANH HUONG';
	$prefix          = $vietqr_settings['prefix'] ?? 'HD';
	$template        = $vietqr_settings['template'] ?? 'compact2';
	$order_id        = $order->get_id();
	$total_amount    = (int) round( (float) $order->get_total() );
	$transfer_memo   = trim( $prefix . ' ' . $order_id );

	$qr_url = class_exists( 'WC_Gateway_VietQR' )
		? ( new WC_Gateway_VietQR() )->get_vietqr_url( $order )
		: sprintf(
			'https://img.vietqr.io/image/%s-%s-%s.png?amount=%d&addInfo=%s&accountName=%s',
			rawurlencode( $bank_id ),
			rawurlencode( $account_no ),
			rawurlencode( $template ),
			$total_amount,
			rawurlencode( $transfer_memo ),
			rawurlencode( $account_name )
		);

	wp_send_json_success( [
		'order_id'         => $order_id,
		'order_key'        => $order->get_order_key(),
		'qr_url'           => $qr_url,
		'bank_id'          => $bank_id,
		'account_no'       => $account_no,
		'account_name'     => $account_name,
		'amount'           => $total_amount,
		'amount_formatted' => number_format( $total_amount, 0, ',', '.' ) . ' ₫',
		'transfer_content' => $transfer_memo,
		'package_name'     => $package['name'],
		'date_formatted'   => $date_formatted,
		'slot_label'       => $slot_label,
		'qty'              => $qty,
		'status'           => $order->get_status(),
		'order_view_url'   => is_user_logged_in() ? wc_get_endpoint_url( 'view-order', $order_id, wc_get_page_permalink( 'myaccount' ) ) : '',
	] );
}

// AJAX: Poll order payment status for Workshop Step 4 (verified by order_key)
add_action( 'wp_ajax_bv_check_workshop_payment', 'bv_ajax_check_workshop_payment' );
add_action( 'wp_ajax_nopriv_bv_check_workshop_payment', 'bv_ajax_check_workshop_payment' );

function bv_ajax_check_workshop_payment() {
	check_ajax_referer( 'bv_workshop_nonce', 'nonce' );

	$order_id  = (int) ( $_POST['order_id'] ?? 0 );
	$order_key = sanitize_text_field( wp_unslash( $_POST['order_key'] ?? '' ) );
	$order     = $order_id ? wc_get_order( $order_id ) : false;

	if ( ! $order || empty( $order_key ) || ! hash_equals( $order->get_order_key(), $order_key ) ) {
		wp_send_json_error( [ 'message' => 'Không xác thực được đơn đặt vé.' ], 403 );
	}

	bv_release_single_workshop_order( $order );
	$status     = $order->get_status();
	$is_paid    = in_array( $status, [ 'processing', 'completed' ], true );
	$is_expired = in_array( $status, [ 'cancelled', 'failed' ], true );

	wp_send_json_success( [
		'order_id'   => $order_id,
		'status'     => $status,
		'is_paid'    => $is_paid,
		'is_expired' => $is_expired,
	] );
}

// Display workshop custom metadata in Cart & Checkout
add_filter( 'woocommerce_get_item_data', function ( $item_data, $cart_item ) {
	if ( isset( $cart_item['bv_workshop'] ) ) {
		$ws = $cart_item['bv_workshop'];
		$item_data[] = [ 'name' => 'Gói workshop', 'value' => $ws['package_name'] ];
		$item_data[] = [ 'name' => 'Ngày trải nghiệm', 'value' => date( 'd/m/Y', strtotime( $ws['date'] ) ) ];
		$item_data[] = [ 'name' => 'Ca học', 'value' => $ws['slot_label'] ];
	}
	return $item_data;
}, 10, 2 );

// Set dynamic price from package
add_action( 'woocommerce_before_calculate_totals', function ( $cart ) {
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}
	foreach ( $cart->get_cart() as $cart_item ) {
		if ( isset( $cart_item['bv_workshop']['price'] ) ) {
			$cart_item['data']->set_price( $cart_item['bv_workshop']['price'] );
		}
	}
}, 20, 1 );

// Transfer workshop meta to Order Items
add_action( 'woocommerce_checkout_create_order_line_item', function ( $item, $cart_item_key, $values, $order ) {
	if ( isset( $values['bv_workshop'] ) ) {
		$ws = $values['bv_workshop'];
		$item->add_meta_data( 'Gói workshop', $ws['package_name'] );
		$item->add_meta_data( 'Ngày trải nghiệm', date( 'd/m/Y', strtotime( $ws['date'] ) ) );
		$item->add_meta_data( 'Ca học', $ws['slot_label'] );
		$item->add_meta_data( '_ws_date_raw', $ws['date'] );
		$item->add_meta_data( '_ws_slot_id', $ws['slot_id'] );
		$item->add_meta_data( '_ws_attendees', $ws['attendees'] );
	}
}, 10, 4 );

// When order is placed, decrement available capacity
add_action( 'woocommerce_checkout_order_processed', function ( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return;
	}
	foreach ( $order->get_items() as $item ) {
		$date    = $item->get_meta( '_ws_date_raw' );
		$slot_id = $item->get_meta( '_ws_slot_id' );
		$qty     = $item->get_quantity();

		if ( $date && $slot_id ) {
			bv_add_booking_seat( $date, $slot_id, $qty );
		}
	}
} );

// Helper: Ensure a bookable product exists in WooCommerce
function bv_get_or_create_workshop_product( $package ) {
	$sku = 'WS-TICKET-' . strtoupper( $package['id'] );
	$product_id = wc_get_product_id_by_sku( $sku );

	if ( ! $product_id ) {
		$product = new WC_Product_Simple();
		$product->set_name( 'Vé Workshop: ' . $package['name'] );
		$product->set_sku( $sku );
		$product->set_status( 'publish' );
		$product->set_catalog_visibility( 'hidden' ); // Hidden from normal shop catalog
		$product->set_regular_price( $package['price'] );
		$product->set_price( $package['price'] );
		$product->set_virtual( true ); // Virtual product (no shipping needed)
		$product->set_description( $package['description'] );

		// Set default workshop image
		$ws_img_id = (int) get_option( 'bv_workshop_default_img', 26 );
		if ( $ws_img_id ) {
			$product->set_image_id( $ws_img_id );
		}

		$product_id = $product->save();

		// Assign to internal ticket category so it never pollutes uncategorized
		$term = get_term_by( 'slug', 've-workshop-noi-bo', 'product_cat' );
		if ( $term && $product_id ) {
			wp_set_object_terms( $product_id, [ (int) $term->term_id ], 'product_cat' );
		}
	}

	return $product_id;
}
