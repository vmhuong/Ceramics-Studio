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

		<!-- STEPPER PROGRESS BAR (THEO LUỒNG THỨ TỰ NHƯ TRANG THANH TOÁN) -->
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
					<span>Xác nhận</span>
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

				<div class="bv-ws-wizard-actions">
					<button type="button" class="bv-ws-btn-prev" data-goto="2">
						← Quay lại chọn lịch
					</button>
					<button type="submit" id="bv-ws-submit" class="bv-btn-book">
						Tiến Hành Đặt Lịch &amp; Thanh Toán
					</button>
				</div>
				<div class="bv-ws-msg-box">
					<span id="bv-ws-message" class="bv-ws-message" role="status"></span>
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

// AJAX: Book workshop and add to cart
add_action( 'wp_ajax_bv_book_workshop', 'bv_ajax_book_workshop' );
add_action( 'wp_ajax_nopriv_bv_book_workshop', 'bv_ajax_book_workshop' );

function bv_ajax_book_workshop() {
	check_ajax_referer( 'bv_workshop_nonce', 'nonce' );

	$package_id = sanitize_text_field( wp_unslash( $_POST['package'] ?? 'ban-xoay' ) );
	$date       = sanitize_text_field( wp_unslash( $_POST['date'] ?? '' ) );
	$slot_id    = sanitize_text_field( wp_unslash( $_POST['slot'] ?? '' ) );
	$qty        = max( 1, (int) ( $_POST['qty'] ?? 1 ) );

	$packages = bv_get_workshop_packages();
	$slots    = bv_get_workshop_slots();

	if ( ! isset( $packages[ $package_id ] ) || ! isset( $slots[ $slot_id ] ) || empty( $date ) ) {
		wp_send_json_error( [ 'message' => 'Thông tin đặt lịch không hợp lệ.' ] );
	}

	$package = $packages[ $package_id ];
	$slot    = $slots[ $slot_id ];

	// Check remaining capacity
	$booked = bv_get_booked_count( $date, $slot_id );
	$remain = max( 0, $slot['capacity'] - $booked );
	if ( $qty > $remain ) {
		wp_send_json_error( [ 'message' => sprintf( 'Ca học này chỉ còn lại %d chỗ trống.', $remain ) ] );
	}

	// Find or create a shadow product for workshop booking
	$product_id = bv_get_or_create_workshop_product( $package );

	// Custom cart item data
	$cart_item_data = [
		'bv_workshop' => [
			'package_name' => $package['name'],
			'package_id'   => $package_id,
			'date'         => $date,
			'slot_label'   => $slot['label'] . ' (' . $slot['time'] . ')',
			'slot_id'      => $slot_id,
			'price'        => $package['price'],
			'attendees'    => $qty,
		],
	];

	// Add to cart
	WC()->cart->empty_cart(); // Clear previous items so user can checkout workshop cleanly
	$cart_item_key = WC()->cart->add_to_cart( $product_id, $qty, 0, [], $cart_item_data );

	if ( $cart_item_key ) {
		wp_send_json_success( [
			'redirect' => wc_get_checkout_url(),
		] );
	} else {
		wp_send_json_error( [ 'message' => 'Không thể thêm vé workshop vào giỏ hàng. Vui lòng thử lại.' ] );
	}
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
		$product_id = $product->save();
	}

	return $product_id;
}
