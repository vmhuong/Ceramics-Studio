<?php
/**
 * Admin Workshop Schedule & Attendance Manager
 * Ceramic Studio - Minimalist & Luxury Craft Design
 *
 * @package HelloElementorChild
 */

defined( 'ABSPATH' ) || exit;

// Register Admin Menu Page
add_action( 'admin_menu', function () {
	add_menu_page(
		'Lịch Workshop',
		'Lịch Workshop',
		'manage_woocommerce',
		'bv-workshop-schedule',
		'bv_render_admin_workshop_schedule',
		'dashicons-calendar-alt',
		56
	);
} );

/**
 * Fetch workshop bookings data for a specific date or date range
 *
 * @param string $target_date Format Y-m-d
 * @return array
 */
function bv_get_workshop_bookings_for_date( $target_date ) {
	$slots = function_exists( 'bv_get_workshop_slots' ) ? bv_get_workshop_slots() : [
		'morning'   => [ 'id' => 'morning',   'label' => 'Ca Sáng',   'time' => '09:00 - 11:30', 'capacity' => 8 ],
		'afternoon' => [ 'id' => 'afternoon', 'label' => 'Ca Chiều', 'time' => '14:00 - 16:30', 'capacity' => 8 ],
		'evening'   => [ 'id' => 'evening',   'label' => 'Ca Tối',   'time' => '18:00 - 20:30', 'capacity' => 8 ],
	];

	$schedule = [];
	foreach ( $slots as $sid => $sinfo ) {
		$schedule[ $sid ] = [
			'info'     => $sinfo,
			'bookings' => [],
			'total_seats' => 0,
		];
	}

	if ( ! function_exists( 'wc_get_orders' ) ) {
		return $schedule;
	}

	// Query orders from WooCommerce (support both HPOS and traditional post storage)
	$orders = wc_get_orders( [
		'limit'  => 200,
		'status' => [ 'wc-processing', 'wc-completed', 'wc-on-hold', 'wc-pending' ],
		'orderby' => 'date',
		'order'   => 'DESC',
	] );

	foreach ( $orders as $order ) {
		foreach ( $order->get_items() as $item ) {
			$raw_date = $item->get_meta( '_ws_date_raw' );
			if ( ! $raw_date ) {
				$vn_date = $item->get_meta( 'Ngày trải nghiệm' );
				if ( $vn_date ) {
					$d_obj = DateTime::createFromFormat( 'd/m/Y', $vn_date );
					if ( $d_obj ) {
						$raw_date = $d_obj->format( 'Y-m-d' );
					}
				}
			}

			if ( $raw_date === $target_date ) {
				$slot_id = $item->get_meta( '_ws_slot_id' );
				if ( ! $slot_id || ! isset( $schedule[ $slot_id ] ) ) {
					$slot_label = (string) $item->get_meta( 'Ca học' );
					if ( false !== stripos( $slot_label, 'Sáng' ) ) {
						$slot_id = 'morning';
					} elseif ( false !== stripos( $slot_label, 'Chiều' ) ) {
						$slot_id = 'afternoon';
					} elseif ( false !== stripos( $slot_label, 'Tối' ) ) {
						$slot_id = 'evening';
					} else {
						$slot_id = 'morning';
					}
				}

				$qty = (int) $item->get_quantity();
				$customer_name = trim( $order->get_formatted_billing_full_name() );
				if ( empty( $customer_name ) ) {
					$customer_name = trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() );
				}
				if ( empty( $customer_name ) ) {
					$customer_name = 'Khách đặt vé #' . $order->get_id();
				}

				$phone = $order->get_billing_phone();
				$clean_phone = preg_replace( '/[^0-9]/', '', (string) $phone );

				$schedule[ $slot_id ]['bookings'][] = [
					'order_id'       => $order->get_id(),
					'order_url'      => $order->get_edit_order_url(),
					'customer_name'  => $customer_name,
					'customer_phone' => $phone,
					'clean_phone'    => $clean_phone,
					'customer_email' => $order->get_billing_email(),
					'customer_note'  => $order->get_customer_note(),
					'package_name'   => $item->get_meta( 'Gói workshop' ) ?: $item->get_name(),
					'qty'            => $qty,
					'item_total'     => $item->get_total(),
					'order_status'   => $order->get_status(),
					'status_name'    => wc_get_order_status_name( $order->get_status() ),
					'payment_method' => $order->get_payment_method_title(),
					'time_created'   => $order->get_date_created() ? $order->get_date_created()->date( 'H:i d/m' ) : '',
				];

				$schedule[ $slot_id ]['total_seats'] += $qty;
			}
		}
	}

	return $schedule;
}

/**
 * Render the main Admin Workshop Schedule Dashboard
 */
function bv_render_admin_workshop_schedule() {
	if ( ! current_user_can( 'manage_woocommerce' ) ) {
		wp_die( 'Bạn không có quyền truy cập trang này.' );
	}

	$today       = current_time( 'Y-m-d' );
	$target_date = isset( $_GET['ws_date'] ) ? sanitize_text_field( wp_unslash( $_GET['ws_date'] ) ) : $today;
	if ( ! preg_match( '/^\d{4}-\d{2}-\d{2}$/', $target_date ) ) {
		$target_date = $today;
	}

	$tomorrow = date( 'Y-m-d', strtotime( $today . ' +1 day' ) );
	$day_after = date( 'Y-m-d', strtotime( $today . ' +2 days' ) );

	$schedule_data = bv_get_workshop_bookings_for_date( $target_date );

	// Calculate overall KPIs for the day
	$day_total_seats = 0;
	$day_total_orders = 0;
	$day_max_capacity = 24; // 8 * 3
	foreach ( $schedule_data as $slot ) {
		$day_total_seats += $slot['total_seats'];
		$day_total_orders += count( $slot['bookings'] );
	}
	$occupancy_rate = $day_max_capacity > 0 ? round( ( $day_total_seats / $day_max_capacity ) * 100 ) : 0;
	$formatted_vn_date = date( 'd/m/Y', strtotime( $target_date ) );
	$day_of_week = date( 'l', strtotime( $target_date ) );
	$vn_days = [
		'Monday'    => 'Thứ Hai',
		'Tuesday'   => 'Thứ Ba',
		'Wednesday' => 'Thứ Tư',
		'Thursday'  => 'Thứ Năm',
		'Friday'    => 'Thứ Sáu',
		'Saturday'  => 'Thứ Bảy',
		'Sunday'    => 'Chủ Nhật',
	];
	$vn_day_name = isset( $vn_days[ $day_of_week ] ) ? $vn_days[ $day_of_week ] : $day_of_week;
	?>

	<div class="wrap bv-admin-ws-wrap">
		
		<!-- HEADER BAR -->
		<div class="bv-ws-header">
			<div class="bv-ws-header-title">
				<h1>📅 Quản Lý Lịch Workshop Làm Gốm</h1>
				<p class="bv-ws-subtitle">Theo dõi danh sách khách đặt chỗ, số lượng vé và điều phối thợ hướng dẫn theo từng ca học.</p>
			</div>
			<div class="bv-ws-header-actions">
				<button type="button" class="button button-primary bv-btn-print" onclick="window.print();">
					🖨️ In Phiếu Điểm Danh Ngày <?php echo esc_html( $formatted_vn_date ); ?>
				</button>
			</div>
		</div>

		<!-- DATE SELECTOR TOOLBAR -->
		<div class="bv-ws-toolbar">
			<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" class="bv-date-form">
				<input type="hidden" name="page" value="bv-workshop-schedule">
				
				<div class="bv-quick-dates">
					<a href="<?php echo esc_url( add_query_arg( [ 'page' => 'bv-workshop-schedule', 'ws_date' => $today ], admin_url( 'admin.php' ) ) ); ?>" class="button <?php echo ( $target_date === $today ) ? 'button-primary is-active' : ''; ?>">
						Hôm nay (<?php echo date( 'd/m', strtotime( $today ) ); ?>)
					</a>
					<a href="<?php echo esc_url( add_query_arg( [ 'page' => 'bv-workshop-schedule', 'ws_date' => $tomorrow ], admin_url( 'admin.php' ) ) ); ?>" class="button <?php echo ( $target_date === $tomorrow ) ? 'button-primary is-active' : ''; ?>">
						Ngày mai (<?php echo date( 'd/m', strtotime( $tomorrow ) ); ?>)
					</a>
					<a href="<?php echo esc_url( add_query_arg( [ 'page' => 'bv-workshop-schedule', 'ws_date' => $day_after ], admin_url( 'admin.php' ) ) ); ?>" class="button <?php echo ( $target_date === $day_after ) ? 'button-primary is-active' : ''; ?>">
						Ngày kia (<?php echo date( 'd/m', strtotime( $day_after ) ); ?>)
					</a>
				</div>

				<div class="bv-picker-wrap">
					<label for="ws_date_input"><strong>Chọn ngày khác:</strong></label>
					<input type="date" id="ws_date_input" name="ws_date" value="<?php echo esc_attr( $target_date ); ?>" onchange="this.form.submit();">
					<button type="submit" class="button">Xem lịch</button>
				</div>
			</form>
		</div>

		<!-- KPI STATS CARDS -->
		<div class="bv-ws-kpi-grid">
			<div class="bv-kpi-card">
				<span class="bv-kpi-label">Ngày đang xem</span>
				<div class="bv-kpi-val"><?php echo esc_html( $vn_day_name . ', ' . $formatted_vn_date ); ?></div>
			</div>
			<div class="bv-kpi-card">
				<span class="bv-kpi-label">Tổng số vé đã đặt</span>
				<div class="bv-kpi-val bv-kpi-highlight"><?php echo esc_html( $day_total_seats ); ?> <span class="bv-kpi-unit">/ <?php echo esc_html( $day_max_capacity ); ?> chỗ</span></div>
			</div>
			<div class="bv-kpi-card">
				<span class="bv-kpi-label">Số đơn đặt vé</span>
				<div class="bv-kpi-val"><?php echo esc_html( $day_total_orders ); ?> <span class="bv-kpi-unit">đơn hàng</span></div>
			</div>
			<div class="bv-kpi-card">
				<span class="bv-kpi-label">Tỷ lệ kín chỗ</span>
				<div class="bv-kpi-val"><?php echo esc_html( $occupancy_rate ); ?>%</div>
				<div class="bv-progress-bar"><div class="bv-progress-fill" style="width: <?php echo min( 100, $occupancy_rate ); ?>%;"></div></div>
			</div>
		</div>

		<!-- PRINT-ONLY HEADER -->
		<div class="bv-print-header">
			<h2>CERAMIC STUDIO - DANH SÁCH KHÁCH THAM GIA WORKSHOP LÀM GỐM</h2>
			<p><strong>Ngày trải nghiệm:</strong> <?php echo esc_html( $vn_day_name . ' - Ngày ' . $formatted_vn_date ); ?> | In lúc: <?php echo current_time( 'H:i d/m/Y' ); ?></p>
		</div>

		<!-- SLOTS BREAKDOWN SECTION -->
		<div class="bv-slots-container">
			<?php foreach ( $schedule_data as $slot_id => $slot_data ) : 
				$info     = $slot_data['info'];
				$bookings = $slot_data['bookings'];
				$seats    = $slot_data['total_seats'];
				$capacity = $info['capacity'];
				$empty    = max( 0, $capacity - $seats );
				$rate     = $capacity > 0 ? round( ( $seats / $capacity ) * 100 ) : 0;
			?>
				<div class="bv-slot-card <?php echo ( $seats >= $capacity ) ? 'is-full' : ( $seats > 0 ? 'has-bookings' : 'is-empty' ); ?>">
					
					<!-- SLOT HEADER -->
					<div class="bv-slot-header">
						<div class="bv-slot-main-info">
							<h2 class="bv-slot-title">
								<?php echo esc_html( $info['label'] ); ?> 
								<span class="bv-slot-time">(<?php echo esc_html( $info['time'] ); ?>)</span>
							</h2>
						</div>

						<div class="bv-slot-capacity-badge">
							<?php if ( $seats === 0 ) : ?>
								<span class="bv-badge bv-badge-empty">Chưa có khách đặt (Trống <?php echo esc_html( $capacity ); ?> chỗ)</span>
							<?php elseif ( $seats >= $capacity ) : ?>
								<span class="bv-badge bv-badge-full">Đã kín chỗ (<?php echo esc_html( $seats ); ?>/<?php echo esc_html( $capacity ); ?>)</span>
							<?php else : ?>
								<span class="bv-badge bv-badge-partial">Đã nhận <?php echo esc_html( $seats ); ?>/<?php echo esc_html( $capacity ); ?> chỗ (Còn trống <?php echo esc_html( $empty ); ?> chỗ)</span>
							<?php endif; ?>
						</div>
					</div>

					<!-- ATTENDEES TABLE -->
					<?php if ( ! empty( $bookings ) ) : ?>
						<table class="wp-list-table widefat fixed striped bv-table-attendees">
							<thead>
								<tr>
									<th style="width: 50px;">STT</th>
									<th style="width: 90px;">Đơn hàng</th>
									<th style="width: 170px;">Khách hàng</th>
									<th style="width: 150px;">Điện thoại / Zalo</th>
									<th>Gói trải nghiệm</th>
									<th style="width: 80px; text-align: center;">Số vé</th>
									<th style="width: 130px;">Trạng thái</th>
									<th>Ghi chú của khách</th>
									<th class="bv-print-hide" style="width: 110px; text-align: center;">Thao tác</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$stt = 1;
								foreach ( $bookings as $b ) : 
									$status_class = ( 'completed' === $b['order_status'] || 'processing' === $b['order_status'] ) ? 'status-paid' : 'status-pending';
								?>
									<tr>
										<td><strong><?php echo esc_html( $stt++ ); ?></strong></td>
										<td>
											<a href="<?php echo esc_url( $b['order_url'] ); ?>" target="_blank" class="bv-order-link" title="Xem chi tiết đơn hàng">
												#<?php echo esc_html( $b['order_id'] ); ?>
											</a>
										</td>
										<td>
											<strong class="bv-cust-name"><?php echo esc_html( $b['customer_name'] ); ?></strong>
											<?php if ( ! empty( $b['customer_email'] ) ) : ?>
												<div class="bv-cust-email"><?php echo esc_html( $b['customer_email'] ); ?></div>
											<?php endif; ?>
										</td>
										<td>
											<?php if ( ! empty( $b['customer_phone'] ) ) : ?>
												<a href="tel:<?php echo esc_attr( $b['customer_phone'] ); ?>" class="bv-phone-link">
													📞 <?php echo esc_html( $b['customer_phone'] ); ?>
												</a>
												<?php if ( ! empty( $b['clean_phone'] ) ) : ?>
													<a href="https://zalo.me/<?php echo esc_attr( $b['clean_phone'] ); ?>" target="_blank" class="bv-zalo-pill bv-print-hide" title="Nhắn Zalo cho khách">
														Zalo
													</a>
												<?php endif; ?>
											<?php else : ?>
												<span class="bv-muted">Không có SĐT</span>
											<?php endif; ?>
										</td>
										<td>
											<span class="bv-package-tag"><?php echo esc_html( $b['package_name'] ); ?></span>
										</td>
										<td style="text-align: center;">
											<span class="bv-seat-qty"><?php echo esc_html( $b['qty'] ); ?> vé</span>
										</td>
										<td>
											<span class="bv-status-pill <?php echo esc_attr( $status_class ); ?>">
												<?php echo esc_html( $b['status_name'] ); ?>
											</span>
										</td>
										<td>
											<?php if ( ! empty( $b['customer_note'] ) ) : ?>
												<span class="bv-note-text">"<?php echo esc_html( $b['customer_note'] ); ?>"</span>
											<?php else : ?>
												<span class="bv-muted">—</span>
											<?php endif; ?>
										</td>
										<td class="bv-print-hide" style="text-align: center;">
											<a href="<?php echo esc_url( $b['order_url'] ); ?>" target="_blank" class="button button-small">
												Xem đơn
											</a>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					<?php else : ?>
						<div class="bv-empty-slot-msg">
							<p>Hiện chưa có khách nào đặt chỗ cho ca học này.</p>
						</div>
					<?php endif; ?>

				</div>
			<?php endforeach; ?>
		</div>

	</div>

	<!-- DASHBOARD INLINE STYLES (CLEAN CERAMIC CRAFT AESTHETIC) -->
	<style>
		.bv-admin-ws-wrap {
			max-width: 1200px;
			margin: 20px 20px 40px 0;
			font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
		}
		.bv-ws-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			background: #fff;
			padding: 24px 28px;
			border-radius: 8px;
			border: 1px solid #e2e4e7;
			margin-bottom: 20px;
			box-shadow: 0 1px 3px rgba(0,0,0,0.04);
		}
		.bv-ws-header h1 {
			margin: 0 0 6px 0;
			font-size: 24px;
			font-weight: 700;
			color: #1e1e1e;
		}
		.bv-ws-subtitle {
			margin: 0;
			color: #646970;
			font-size: 14px;
		}
		.bv-btn-print {
			background: #7c5836 !important;
			border-color: #7c5836 !important;
			font-size: 14px !important;
			height: 38px !important;
			line-height: 36px !important;
			padding: 0 18px !important;
			font-weight: 600 !important;
		}
		.bv-btn-print:hover {
			background: #644426 !important;
			border-color: #644426 !important;
		}
		.bv-ws-toolbar {
			background: #fff;
			padding: 16px 24px;
			border-radius: 8px;
			border: 1px solid #e2e4e7;
			margin-bottom: 20px;
		}
		.bv-date-form {
			display: flex;
			justify-content: space-between;
			align-items: center;
			flex-wrap: wrap;
			gap: 16px;
		}
		.bv-quick-dates .button {
			height: 36px;
			line-height: 34px;
			padding: 0 16px;
			margin-right: 6px;
			font-weight: 500;
		}
		.bv-quick-dates .button.is-active {
			background: #2271b1;
			border-color: #2271b1;
			color: #fff;
		}
		.bv-picker-wrap {
			display: flex;
			align-items: center;
			gap: 10px;
		}
		.bv-picker-wrap input[type="date"] {
			height: 36px;
			padding: 4px 10px;
			border-radius: 4px;
		}
		.bv-ws-kpi-grid {
			display: grid;
			grid-template-columns: repeat(4, 1fr);
			gap: 16px;
			margin-bottom: 24px;
		}
		.bv-kpi-card {
			background: #fff;
			padding: 20px;
			border-radius: 8px;
			border: 1px solid #e2e4e7;
			box-shadow: 0 1px 3px rgba(0,0,0,0.04);
		}
		.bv-kpi-label {
			display: block;
			font-size: 13px;
			font-weight: 600;
			color: #646970;
			text-transform: uppercase;
			letter-spacing: 0.5px;
			margin-bottom: 8px;
		}
		.bv-kpi-val {
			font-size: 22px;
			font-weight: 700;
			color: #1e1e1e;
		}
		.bv-kpi-highlight {
			color: #7c5836;
		}
		.bv-kpi-unit {
			font-size: 14px;
			font-weight: normal;
			color: #8c8f94;
		}
		.bv-progress-bar {
			width: 100%;
			height: 6px;
			background: #f0f0f1;
			border-radius: 3px;
			margin-top: 10px;
			overflow: hidden;
		}
		.bv-progress-fill {
			height: 100%;
			background: #7c5836;
			border-radius: 3px;
		}
		.bv-slot-card {
			background: #fff;
			border-radius: 8px;
			border: 1px solid #e2e4e7;
			margin-bottom: 24px;
			box-shadow: 0 1px 3px rgba(0,0,0,0.04);
			overflow: hidden;
		}
		.bv-slot-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding: 18px 24px;
			background: #fdfcfb;
			border-bottom: 1px solid #e2e4e7;
		}
		.bv-slot-title {
			margin: 0;
			font-size: 18px;
			font-weight: 700;
			color: #2c3338;
		}
		.bv-slot-time {
			font-size: 14px;
			font-weight: normal;
			color: #646970;
			margin-left: 6px;
		}
		.bv-badge {
			display: inline-block;
			padding: 5px 12px;
			border-radius: 20px;
			font-size: 12.5px;
			font-weight: 600;
		}
		.bv-badge-empty {
			background: #f0f0f1;
			color: #646970;
		}
		.bv-badge-partial {
			background: #e7f5ea;
			color: #1b6329;
		}
		.bv-badge-full {
			background: #fbeae5;
			color: #9d2b11;
		}
		.bv-table-attendees {
			border: none;
			box-shadow: none;
			margin: 0;
		}
		.bv-table-attendees th {
			background: #f6f7f7;
			font-weight: 600;
			padding: 12px 14px;
		}
		.bv-table-attendees td {
			padding: 14px;
			vertical-align: middle;
		}
		.bv-order-link {
			font-weight: 700;
			color: #2271b1;
			text-decoration: none;
		}
		.bv-cust-name {
			display: block;
			font-size: 14px;
			color: #1e1e1e;
		}
		.bv-cust-email {
			font-size: 12px;
			color: #646970;
		}
		.bv-phone-link {
			font-size: 13.5px;
			font-weight: 600;
			color: #1e1e1e;
			text-decoration: none;
		}
		.bv-zalo-pill {
			display: inline-block;
			background: #0068ff;
			color: #fff;
			font-size: 11px;
			font-weight: 700;
			padding: 2px 7px;
			border-radius: 10px;
			text-decoration: none;
			margin-left: 6px;
		}
		.bv-zalo-pill:hover {
			background: #0050c7;
			color: #fff;
		}
		.bv-package-tag {
			display: inline-block;
			background: #f4ece4;
			color: #6e4822;
			padding: 4px 10px;
			border-radius: 4px;
			font-size: 13px;
			font-weight: 600;
		}
		.bv-seat-qty {
			font-weight: 700;
			font-size: 14px;
			color: #1e1e1e;
		}
		.bv-status-pill {
			display: inline-block;
			padding: 3px 8px;
			border-radius: 12px;
			font-size: 11.5px;
			font-weight: 600;
		}
		.status-paid {
			background: #c6e1c6;
			color: #1c521c;
		}
		.status-pending {
			background: #f8dda4;
			color: #7a5000;
		}
		.bv-note-text {
			font-style: italic;
			color: #50575e;
			font-size: 12.5px;
		}
		.bv-muted {
			color: #a7aaad;
		}
		.bv-empty-slot-msg {
			padding: 32px;
			text-align: center;
			color: #8c8f94;
			font-style: italic;
		}
		.bv-print-header {
			display: none;
		}

		/* PRINT SHEET STYLES */
		@media print {
			#adminmenumain, #wpadminbar, #wpfooter, .bv-ws-header-actions, .bv-ws-toolbar, .bv-print-hide {
				display: none !important;
			}
			#wpcontent, #wpbody-content {
				margin: 0 !important;
				padding: 0 !important;
			}
			.bv-admin-ws-wrap {
				max-width: 100% !important;
				margin: 0 !important;
			}
			.bv-print-header {
				display: block !important;
				margin-bottom: 20px;
				border-bottom: 2px solid #333;
				padding-bottom: 10px;
			}
			.bv-slot-card {
				box-shadow: none !important;
				border: 1px solid #999 !important;
				page-break-inside: avoid;
				margin-bottom: 20px !important;
			}
		}
	</style>
	<?php
}
