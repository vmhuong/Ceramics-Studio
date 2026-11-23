<?php
/**
 * VietQR Payment Gateway for WooCommerce - Ceramic Studio
 *
 * @package HelloElementorChild
 */

defined( 'ABSPATH' ) || exit;

add_filter( 'woocommerce_payment_gateways', 'bv_add_vietqr_gateway' );

function bv_add_vietqr_gateway( $gateways ) {
	$gateways[] = 'WC_Gateway_VietQR';
	return $gateways;
}

if ( class_exists( 'WC_Payment_Gateway' ) ) {
	bv_define_vietqr_class();
} else {
	add_action( 'init', 'bv_define_vietqr_class' );
}

function bv_define_vietqr_class() {
	if ( class_exists( 'WC_Gateway_VietQR' ) || ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	class WC_Gateway_VietQR extends WC_Payment_Gateway {

		public function __construct() {
			$this->id                 = 'vietqr';
			$this->has_fields         = false;
			$this->method_title       = __( 'Chuyển khoản VietQR', 'hello-elementor-child' );
			$this->method_description = __( 'Thanh toán quét mã QR qua ứng dụng ngân hàng tự động điền số tài khoản, số tiền và nội dung.', 'hello-elementor-child' );

			$this->init_form_fields();
			$this->init_settings();

			$this->title        = $this->get_option( 'title', 'Chuyển khoản VietQR (Quét mã tự động)' );
			$this->description  = $this->get_option( 'description', 'Quét mã QR bằng bất kỳ ứng dụng ngân hàng nào để thanh toán nhanh chóng và chính xác.' );
			$this->bank_id      = $this->get_option( 'bank_id', 'MB' );
			$this->account_no   = $this->get_option( 'account_no', '0363292271' );
			$this->account_name = $this->get_option( 'account_name', 'VU MANH HUONG' );
			$this->template     = $this->get_option( 'template', 'compact2' );
			$this->prefix       = $this->get_option( 'prefix', 'HD' );

			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, [ $this, 'process_admin_options' ] );
			add_action( 'woocommerce_thankyou_' . $this->id, [ $this, 'thankyou_page' ] );
			add_action( 'woocommerce_email_before_order_table', [ $this, 'email_instructions' ], 10, 3 );
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
		}

		public function init_form_fields() {
			$this->form_fields = [
				'enabled'      => [
					'title'   => 'Bật/Tắt',
					'type'    => 'checkbox',
					'label'   => 'Kích hoạt cổng chuyển khoản VietQR',
					'default' => 'yes',
				],
				'title'        => [
					'title'       => 'Tiêu đề hiển thị',
					'type'        => 'text',
					'description' => 'Tên phương thức thanh toán khách nhìn thấy lúc thanh toán.',
					'default'     => 'Chuyển khoản VietQR (Quét mã tự động)',
					'desc_tip'    => true,
				],
				'description'  => [
					'title'       => 'Mô tả',
					'type'        => 'textarea',
					'description' => 'Hướng dẫn hiển thị khi khách chọn phương thức này.',
					'default'     => 'Quét mã QR bằng ứng dụng ngân hàng bất kỳ để thanh toán tức thì.',
				],
				'bank_id'      => [
					'title'       => 'Ngân hàng thụ hưởng',
					'type'        => 'select',
					'description' => 'Chọn ngân hàng mở tài khoản của cửa hàng.',
					'default'     => 'MB',
					'options'     => [
						'MB'       => 'MBBank - Ngân hàng Quân Đội',
						'VCB'      => 'Vietcombank - Ngoại Thương Việt Nam',
						'TCB'      => 'Techcombank - Kỹ Thương Việt Nam',
						'ACB'      => 'ACB - Á Châu',
						'VPB'      => 'VPBank - Việt Nam Thịnh Vượng',
						'TPB'      => 'TPBank - Tiên Phong',
						'BIDV'     => 'BIDV - Đầu tư và Phát triển VN',
						'CTG'      => 'VietinBank - Công Thương Việt Nam',
						'VIB'      => 'VIB - Quốc Tế',
						'STB'      => 'Sacombank - Sài Gòn Thương Tín',
						'HDB'      => 'HDBank - Phát Triển TP.HCM',
						'OCB'      => 'OCB - Phương Đông',
					],
				],
				'account_no'   => [
					'title'       => 'Số tài khoản',
					'type'        => 'text',
					'description' => 'Số tài khoản ngân hàng thụ hưởng.',
					'default'     => '0363292271',
				],
				'account_name' => [
					'title'       => 'Tên chủ tài khoản',
					'type'        => 'text',
					'description' => 'Tên chủ tài khoản (viết hoa không dấu hoặc có dấu).',
					'default'     => 'VU MANH HUONG',
				],
				'prefix'       => [
					'title'       => 'Tiền tố mã đơn',
					'type'        => 'text',
					'description' => 'Tiền tố ghi trong nội dung chuyển khoản (Ví dụ: HD).',
					'default'     => 'HD',
				],
				'template'     => [
					'title'   => 'Mẫu mã VietQR',
					'type'    => 'select',
					'default' => 'compact2',
					'options' => [
						'compact2' => 'Đầy đủ (Logo + Khung thông tin)',
						'compact'  => 'Nhỏ gọn (Logo + QR)',
						'qr_only'  => 'Chỉ mã QR',
					],
				],
			];
		}

		public function enqueue_assets() {
			if ( is_checkout() || is_order_received_page() || is_account_page() ) {
				wp_enqueue_style( 'bv-vietqr', get_stylesheet_directory_uri() . '/assets/vietqr.css', [ 'bv-commerce' ], filemtime( __DIR__ . '/../assets/vietqr.css' ) );
				wp_enqueue_script( 'bv-vietqr', get_stylesheet_directory_uri() . '/assets/vietqr.js', [ 'jquery' ], filemtime( __DIR__ . '/../assets/vietqr.js' ), true );
			}
		}

		public function process_payment( $order_id ) {
			$order = wc_get_order( $order_id );

			// Mark as on-hold (waiting for payment)
			$order->update_status( 'on-hold', __( 'Chờ khách hàng chuyển khoản VietQR.', 'hello-elementor-child' ) );

			// Reduce stock levels
			wc_reduce_stock_levels( $order_id );

			// Clear cart
			WC()->cart->empty_cart();

			return [
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order ),
			];
		}

		public function get_vietqr_url( $order ) {
			$total    = (int) $order->get_total();
			$order_id = $order->get_id();
			$content  = trim( $this->prefix . ' ' . $order_id );
			$bank     = rawurlencode( $this->bank_id );
			$acc_no   = rawurlencode( $this->account_no );
			$acc_name = rawurlencode( $this->account_name );
			$add_info = rawurlencode( $content );

			return sprintf(
				'https://img.vietqr.io/image/%s-%s-%s.png?amount=%d&addInfo=%s&accountName=%s',
				$bank,
				$acc_no,
				$this->template,
				$total,
				$add_info,
				$acc_name
			);
		}

		public function thankyou_page( $order_id ) {
			$order = wc_get_order( $order_id );
			if ( ! $order ) {
				return;
			}

			$qr_url           = $this->get_vietqr_url( $order );
			$transfer_content = trim( $this->prefix . ' ' . $order_id );
			$total            = number_format( $order->get_total(), 0, ',', '.' ) . ' ₫';
			?>
			<div class="bv-vietqr-card" id="bv-vietqr-section">
				<div class="bv-vietqr-header">
					<span class="bv-vietqr-badge">Đặt hàng thành công</span>
					<h3>Quét mã VietQR để thanh toán</h3>
					<p class="bv-vietqr-sub">Mở ứng dụng ngân hàng bất kỳ để quét mã QR và hoàn tất thanh toán đơn hàng gốm của bạn.</p>
				</div>
				<div class="bv-vietqr-body">
					<div class="bv-vietqr-image-wrapper">
						<img src="<?php echo esc_url( $qr_url ); ?>" alt="Mã thanh toán VietQR đơn hàng <?php echo esc_attr( $order_id ); ?>" class="bv-vietqr-image" width="280" height="280">
						<span class="bv-vietqr-tip">Tự động điền đúng số tiền & nội dung</span>
					</div>
					<div class="bv-vietqr-details">
						<div class="bv-detail-row">
							<span class="bv-detail-label">Ngân hàng:</span>
							<strong class="bv-detail-value"><?php echo esc_html( $this->bank_id ); ?></strong>
						</div>
						<div class="bv-detail-row">
							<span class="bv-detail-label">Số tài khoản:</span>
							<strong class="bv-detail-value" id="bv-copy-acc"><?php echo esc_html( $this->account_no ); ?></strong>
							<button type="button" class="bv-btn-copy" data-target="#bv-copy-acc" title="Sao chép số tài khoản">Sao chép</button>
						</div>
						<div class="bv-detail-row">
							<span class="bv-detail-label">Chủ tài khoản:</span>
							<strong class="bv-detail-value"><?php echo esc_html( $this->account_name ); ?></strong>
						</div>
						<div class="bv-detail-row">
							<span class="bv-detail-label">Số tiền:</span>
							<strong class="bv-detail-value bv-amount" id="bv-copy-amount"><?php echo esc_html( $total ); ?></strong>
							<button type="button" class="bv-btn-copy" data-copy="<?php echo esc_attr( (int) $order->get_total() ); ?>" title="Sao chép số tiền">Sao chép</button>
						</div>
						<div class="bv-detail-row bv-highlight-row">
							<span class="bv-detail-label">Nội dung CK:</span>
							<strong class="bv-detail-value" id="bv-copy-memo"><?php echo esc_html( $transfer_content ); ?></strong>
							<button type="button" class="bv-btn-copy" data-target="#bv-copy-memo" title="Sao chép nội dung chuyển khoản">Sao chép</button>
						</div>
						<div class="bv-vietqr-notice">
							<p><strong>Lưu ý:</strong> Vui lòng giữ nguyên nội dung chuyển khoản <code><?php echo esc_html( $transfer_content ); ?></code> để đơn hàng được xưởng gốm xử lý và đóng gói nhanh nhất.</p>
						</div>
					</div>
				</div>
			</div>
			<?php
		}

		public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
			if ( $order->get_payment_method() !== $this->id || $sent_to_admin || $order->has_status( 'completed' ) ) {
				return;
			}

			$qr_url           = $this->get_vietqr_url( $order );
			$transfer_content = trim( $this->prefix . ' ' . $order->get_id() );
			$total            = wc_price( $order->get_total() );

			if ( $plain_text ) {
				echo "\n--- THÔNG TIN CHUYỂN KHOẢN VIETQR ---\n";
				echo "Ngân hàng: " . $this->bank_id . "\n";
				echo "Số tài khoản: " . $this->account_no . "\n";
				echo "Chủ tài khoản: " . $this->account_name . "\n";
				echo "Số tiền: " . wp_strip_all_tags( $total ) . "\n";
				echo "Nội dung chuyển khoản: " . $transfer_content . "\n";
			} else {
				?>
				<div style="margin: 24px 0; padding: 20px; border: 1px solid #e0d7ce; border-radius: 8px; background-color: #faf7f2; text-align: center;">
					<h3 style="color: #6b4c35; margin-top: 0;">Hướng dẫn thanh toán chuyển khoản VietQR</h3>
					<p style="font-size: 14px; color: #555;">Quét mã QR dưới đây hoặc chuyển khoản theo thông tin đính kèm:</p>
					<div style="margin: 16px auto;">
						<img src="<?php echo esc_url( $qr_url ); ?>" alt="VietQR" style="max-width: 240px; height: auto; border: 4px solid #fff; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);" />
					</div>
					<table style="width: 100%; max-width: 400px; margin: 0 auto; text-align: left; font-size: 14px; border-collapse: collapse;">
						<tr><td style="padding: 6px 0; color: #777;">Ngân hàng:</td><td><strong><?php echo esc_html( $this->bank_id ); ?></strong></td></tr>
						<tr><td style="padding: 6px 0; color: #777;">Số tài khoản:</td><td><strong><?php echo esc_html( $this->account_no ); ?></strong></td></tr>
						<tr><td style="padding: 6px 0; color: #777;">Chủ tài khoản:</td><td><strong><?php echo esc_html( $this->account_name ); ?></strong></td></tr>
						<tr><td style="padding: 6px 0; color: #777;">Số tiền:</td><td><strong style="color: #b3592b;"><?php echo wp_kses_post( $total ); ?></strong></td></tr>
						<tr><td style="padding: 6px 0; color: #777;">Nội dung CK:</td><td><strong style="background: #eedfd3; padding: 2px 6px; border-radius: 4px;"><?php echo esc_html( $transfer_content ); ?></strong></td></tr>
					</table>
				</div>
				<?php
			}
		}
	}
}
