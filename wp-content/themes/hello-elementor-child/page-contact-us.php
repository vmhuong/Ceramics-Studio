<?php
/**
 * Template Name: Contact Us (Liên Hệ Xưởng Gốm)
 * Style: Luxury Ceramic Craft Minimalism (No Icons / Pure Typography)
 * Theme: Hello Elementor Child for Ceramic Studio
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$form_submitted = false;
$form_error     = '';

if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_POST['bv_contact_submit'] ) ) {
	if ( ! isset( $_POST['bv_contact_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['bv_contact_nonce'] ), 'bv_contact_action' ) ) {
		$form_error = 'Yêu cầu không hợp lệ. Vui lòng tải lại trang và thử lại.';
	} else {
		$name    = isset( $_POST['contact_name'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_name'] ) ) : '';
		$phone   = isset( $_POST['contact_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_phone'] ) ) : '';
		$email   = isset( $_POST['contact_email'] ) ? sanitize_email( wp_unslash( $_POST['contact_email'] ) ) : '';
		$service = isset( $_POST['contact_service'] ) ? sanitize_text_field( wp_unslash( $_POST['contact_service'] ) ) : 'Tư vấn sản phẩm';
		$message = isset( $_POST['contact_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['contact_message'] ) ) : '';

		if ( empty( $name ) || empty( $phone ) ) {
			$form_error = 'Vui lòng nhập họ tên và số điện thoại / Zalo để xưởng tiện liên hệ lại.';
		} else {
			$to      = 'vmhuong05@gmail.com';
			$subject = sprintf( '[Ceramic Studio] Liên hệ mới từ %s (%s)', $name, $phone );
			$body    = sprintf(
				"Họ và tên: %s\nSố điện thoại / Zalo: %s\nEmail: %s\nChủ đề: %s\n\nNội dung nhắn gửi:\n%s\n\nThời gian: %s",
				$name,
				$phone,
				$email ?: 'Không cung cấp',
				$service,
				$message ?: 'Không có ghi chú thêm',
				current_time( 'mysql' )
			);
			$headers = [ 'Content-Type: text/plain; charset=UTF-8' ];
			@wp_mail( $to, $subject, $body, $headers );

			$form_submitted = true;
		}
	}
}

get_header();

$home_url     = home_url( '/' );
$shop_url     = home_url( '/san-pham-gom/' );
$workshop_url = home_url( '/workshop/' );
$zalo_url     = 'https://zalo.me/0363292271';
?>

<div class="bv-contact-page">

	<!-- 1. BREADCRUMB (NO ORDERED LIST NUMBERS - CLEAN TRAIL) -->
	<nav class="bv-contact-breadcrumb" aria-label="Đường dẫn">
		<div class="bv-container">
			<div class="bv-breadcrumb-trail">
				<a href="<?php echo esc_url( $home_url ); ?>">Trang chủ</a>
				<span class="bv-b-sep">/</span>
				<span class="bv-b-current" aria-current="page">Liên Hệ Xưởng Gốm</span>
			</div>
		</div>
	</nav>

	<!-- 2. HERO EDITORIAL -->
	<header class="bv-contact-hero">
		<div class="bv-container">
			<div class="bv-contact-hero-inner">
				<span class="bv-contact-eyebrow">CERAMIC STUDIO • LIÊN HỆ &amp; GHÉ THĂM</span>
				<h1 class="bv-contact-title">
					Kết Nối Cùng Xưởng Gốm Mộc
				</h1>
				<p class="bv-contact-subtitle">
					Không gian xưởng tại làng cổ Bát Tràng luôn mở cửa đón tiếp bạn. Dù bạn muốn tư vấn tác phẩm độc bản, tìm hiểu lịch workshop hay ghé thăm không gian lò nung, chúng tôi luôn sẵn sàng đồng hành.
				</p>
			</div>
		</div>
	</header>

	<!-- 3. MAIN SECTION: INFO (LEFT) & FORM (RIGHT) -->
	<section class="bv-contact-main-section">
		<div class="bv-container">
			<div class="bv-contact-grid">
				
				<!-- COLUMN 1: DIRECT CONTACT & STUDIO SCHEDULE (NO ICONS - EDITORIAL NUMBERS) -->
				<div class="bv-contact-info-col">
					<div class="bv-contact-info-card">
						<span class="bv-info-tag">THÔNG TIN TRỰC TIẾP</span>
						<h2 class="bv-info-heading">Ghé Thăm Không Gian Xưởng</h2>
						<p class="bv-info-desc">
							Bạn có thể đến trực tiếp xưởng để cảm nhận chất đất mộc, xem quy trình vuốt nặn trên bàn xoay và thưởng trà cùng nghệ nhân.
						</p>

						<div class="bv-info-items">
							
							<div class="bv-info-item">
								<span class="bv-info-num">01</span>
								<div class="bv-info-text">
									<strong>Địa chỉ xưởng gốm</strong>
									<p>Thôn 1 Giang Cao, Bát Tràng, Gia Lâm, Hà Nội</p>
									<small class="bv-info-sub">Đường ô tô vào tận cửa, có chỗ đỗ xe máy và ô tô an toàn.</small>
								</div>
							</div>

							<div class="bv-info-item">
								<span class="bv-info-num">02</span>
								<div class="bv-info-text">
									<strong>Hotline &amp; Hỗ trợ Zalo</strong>
									<p><a href="<?php echo esc_url( $zalo_url ); ?>" target="_blank" rel="noopener"><strong>0363 292 271</strong></a> (Mr. Vũ Mạnh Hướng)</p>
									<small class="bv-info-sub">Tư vấn chọn tác phẩm, đặt làm theo kích thước hoặc lịch workshop.</small>
								</div>
							</div>

							<div class="bv-info-item">
								<span class="bv-info-num">03</span>
								<div class="bv-info-text">
									<strong>Thư điện tử (Email)</strong>
									<p><a href="mailto:vmhuong05@gmail.com">vmhuong05@gmail.com</a></p>
									<small class="bv-info-sub">Phản hồi báo giá quà tặng doanh nghiệp trong 24 giờ.</small>
								</div>
							</div>

							<div class="bv-info-item">
								<span class="bv-info-num">04</span>
								<div class="bv-info-text">
									<strong>Thời gian mở cửa đón khách</strong>
									<p>08:30 – 21:00 (Tất cả các ngày trong tuần)</p>
									<small class="bv-info-sub">Bao gồm Thứ 7, Chủ Nhật và các ngày nghỉ lễ.</small>
								</div>
							</div>

						</div>

						<div class="bv-info-quick-actions">
							<a href="<?php echo esc_url( $zalo_url ); ?>" target="_blank" rel="noopener" class="bv-btn-zalo-direct">
								Nhắn Tin Zalo: 0363 292 271
							</a>
							<a href="tel:0363292271" class="bv-btn-call-direct">
								Gọi Điện Trực Tiếp
							</a>
						</div>
					</div>
				</div>

				<!-- COLUMN 2: CONTACT FORM -->
				<div class="bv-contact-form-col">
					<div class="bv-contact-form-card">
						<span class="bv-form-tag">GỬI THÔNG ĐIỆP</span>
						<h2 class="bv-form-heading">Nhắn Nhủ Đến Xưởng Gốm</h2>
						<p class="bv-form-desc">
							Để lại thông tin và nhu cầu của bạn, nghệ nhân phụ trách sẽ liên hệ lại qua điện thoại hoặc Zalo trong thời gian sớm nhất.
						</p>

						<?php if ( $form_submitted ) : ?>
							<div class="bv-form-success" role="alert">
								<h4>Gửi lời nhắn thành công!</h4>
								<p>Ceramic Studio đã nhận được thông tin từ bạn. Chúng tôi sẽ liên hệ lại qua số điện thoại / Zalo trong vòng ít phút.</p>
							</div>
						<?php else : ?>

							<?php if ( ! empty( $form_error ) ) : ?>
								<div class="bv-form-error" role="alert">
									<p><?php echo esc_html( $form_error ); ?></p>
								</div>
							<?php endif; ?>

							<form method="post" action="<?php echo esc_url( get_permalink() ); ?>" class="bv-craft-form">
								<?php wp_nonce_field( 'bv_contact_action', 'bv_contact_nonce' ); ?>

								<div class="bv-form-row bv-form-row--two">
									<div class="bv-form-field">
										<label for="contact_name" class="bv-field-label">Họ và tên <span class="bv-req">*</span></label>
										<input type="text" id="contact_name" name="contact_name" class="bv-field-input" placeholder="Ví dụ: Nguyễn Minh An" required>
									</div>

									<div class="bv-form-field">
										<label for="contact_phone" class="bv-field-label">Số điện thoại / Zalo <span class="bv-req">*</span></label>
										<input type="tel" id="contact_phone" name="contact_phone" class="bv-field-input" placeholder="Ví dụ: 0912 345 678" required>
									</div>
								</div>

								<div class="bv-form-row bv-form-row--two">
									<div class="bv-form-field">
										<label for="contact_email" class="bv-field-label">Địa chỉ Email</label>
										<input type="email" id="contact_email" name="contact_email" class="bv-field-input" placeholder="email@example.com">
									</div>

									<div class="bv-form-field">
										<label for="contact_service" class="bv-field-label">Chủ đề quan tâm</label>
										<select id="contact_service" name="contact_service" class="bv-field-select">
											<option value="Tư vấn mua tác phẩm gốm">Tư vấn mua tác phẩm gốm mộc</option>
											<option value="Đăng ký workshop trải nghiệm">Đăng ký tham gia Workshop làm gốm</option>
											<option value="Đặt quà tặng doanh nghiệp độc bản">Đặt quà tặng gốm độc bản / Sự kiện</option>
											<option value="Đặt hàng theo kích thước riêng">Đặt tạo tác theo yêu cầu riêng</option>
											<option value="Khác">Câu hỏi khác</option>
										</select>
									</div>
								</div>

								<div class="bv-form-row">
									<div class="bv-form-field">
										<label for="contact_message" class="bv-field-label">Lời nhắn gửi đến nghệ nhân</label>
										<textarea id="contact_message" name="contact_message" rows="4" class="bv-field-textarea" placeholder="Chia sẻ thêm về ý tưởng, số lượng hoặc câu hỏi của bạn..."></textarea>
									</div>
								</div>

								<div class="bv-form-row bv-form-row--submit">
									<button type="submit" name="bv_contact_submit" value="1" class="bv-btn-submit-contact">
										Gửi Lời Nhắn Đến Xưởng
									</button>
								</div>
							</form>

						<?php endif; ?>
					</div>
				</div>

			</div>
		</div>
	</section>

	<!-- 4. MAP & DIRECTIONS SECTION -->
	<section class="bv-contact-map-section">
		<div class="bv-container">
			<div class="bv-section-header">
				<span class="bv-section-eyebrow">CHỈ DẪN ĐƯỜNG ĐI</span>
				<h2 class="bv-section-title">Đường Đến Làng Gốm Bát Tràng</h2>
				<p class="bv-section-desc">
					Từ trung tâm Hà Nội, bạn di chuyển qua cầu Vĩnh Tuy hoặc cầu Thanh Trì, rẽ xuống đường đê Bát Tràng khoảng 3km là đến thôn 1 Giang Cao.
				</p>
			</div>

			<div class="bv-map-wrapper">
				<iframe 
					src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d14899.789139049836!2d105.908492!3d20.974716!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135af764c632e29%3A0xe54b9aa1ef78fef8!2zR2lhbmcgQ2FvLCBCw6F0IFRyw6BuZywgR2lhIEzDom0sIEjDoCBO4buZaQ!5e0!3m2!1svi!2svn!4v1710000000000!5m2!1svi!2svn" 
					width="100%" 
					height="440" 
					style="border:0;" 
					allowfullscreen="" 
					loading="lazy" 
					referrerpolicy="no-referrer-when-downgrade"
					title="Bản đồ chỉ dẫn đến Ceramic Studio Bát Tràng">
				</iframe>
				<div class="bv-map-footer">
					<span>Tọa độ xưởng: Thôn 1 Giang Cao, Bát Tràng, Gia Lâm, Hà Nội • Hỗ trợ đón đường: 0363 292 271</span>
				</div>
			</div>
		</div>
	</section>

	<!-- 5. WORKSHOP & SHOP INVITATION -->
	<section class="bv-contact-cta-section">
		<div class="bv-container">
			<div class="bv-contact-cta-card">
				<span class="bv-cta-eyebrow">TRẢI NGHIỆM THỰC TẾ</span>
				<h2 class="bv-cta-title">Tự Tay Chạm Đất &amp; Thổi Hồn Vào Gốm</h2>
				<p class="bv-cta-desc">
					Hãy dành một buổi chiều cuối tuần rời xa màn hình số, đến xưởng vuốt nặn chiếc tách mộc đầu tiên của riêng mình dưới sự hướng dẫn của thợ lành nghề.
				</p>
				<div class="bv-cta-actions">
					<a href="<?php echo esc_url( $workshop_url ); ?>" class="bv-btn-cta-primary">
						Đặt Chỗ Workshop Cuối Tuần
					</a>
					<a href="<?php echo esc_url( $shop_url ); ?>" class="bv-btn-cta-secondary">
						Xem Bộ Sưu Tập Tác Phẩm
					</a>
				</div>
			</div>
		</div>
	</section>

</div>

<?php
get_footer();
