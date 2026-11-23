<?php
/**
 * Template Name: Workshop (Trải Nghiệm Làm Gốm)
 * Style: Luxury Ceramic Craft Minimalism (No Icons / Pure Typography)
 * Theme: Hello Elementor Child for Ceramic Studio
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

$home_url     = home_url( '/' );
$shop_url     = home_url( '/san-pham-gom/' );
$contact_url  = home_url( '/contact-us/' );
$zalo_url     = 'https://zalo.me/0363292271';
?>

<div class="bv-workshop-page">

	<!-- 1. BREADCRUMB (NO NUMBERS - CLEAN TRAIL) -->
	<nav class="bv-ws-breadcrumb" aria-label="Đường dẫn">
		<div class="bv-container">
			<div class="bv-breadcrumb-trail">
				<a href="<?php echo esc_url( $home_url ); ?>">Trang chủ</a>
				<span class="bv-b-sep">/</span>
				<span class="bv-b-current" aria-current="page">Workshop Làm Gốm</span>
			</div>
		</div>
	</nav>

	<!-- 2. HERO EDITORIAL -->
	<header class="bv-ws-hero">
		<div class="bv-container">
			<div class="bv-ws-hero-inner">
				<span class="bv-ws-eyebrow">CERAMIC STUDIO • TRẢI NGHIỆM THỦ CÔNG</span>
				<h1 class="bv-ws-hero-title">
					Một Buổi Chiều Chạm Tay Vào Đất Sét Mộc
				</h1>
				<p class="bv-ws-hero-subtitle">
					Không gian xưởng yên bình tại làng cổ Bát Tràng đón bạn đến tự tay vuốt nặn, định tâm trên bàn xoay và cảm nhận sự an yên thuần khiết từ đất mẹ phù sa sông Hồng.
				</p>
			</div>

			<div class="bv-ws-hero-banner">
				<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/09/gom-demo-15-e3ec755cc48b7069c346.webp' ) ); ?>" alt="Không gian lớp học Workshop làm gốm tại Ceramic Studio Bát Tràng" class="bv-ws-banner-img" loading="eager">
				<div class="bv-ws-banner-caption">
					<span>Bàn xoay gỗ truyền thống &amp; không gian trải nghiệm thủ công mộc mạc</span>
				</div>
			</div>
		</div>
	</header>

	<!-- 3. WORKSHOP HIGHLIGHTS (4 EDITORIAL BENEFITS - NO ICONS) -->
	<section class="bv-ws-benefits-section">
		<div class="bv-container">
			<div class="bv-section-header">
				<span class="bv-section-eyebrow">TRẢI NGHIỆM ĐỘC BẢN</span>
				<h2 class="bv-section-title">Bạn Nhận Được Gì Sau Khóa Học?</h2>
				<p class="bv-section-desc">
					Mỗi buổi workshop kéo dài từ 2 đến 3 giờ, được thiết kế để bạn có trải nghiệm trọn vẹn và tạo ra những thành phẩm gốm mộc thực thụ.
				</p>
			</div>

			<div class="bv-benefits-grid">
				
				<div class="bv-benefit-card">
					<span class="bv-benefit-num">01</span>
					<h3 class="bv-benefit-title">Hướng Dẫn Tận Tay 1-1</h3>
					<p class="bv-benefit-desc">
						Nghệ nhân xưởng kèm cặp trực tiếp từ tư thế ngồi, cách đặt tay định tâm đất, mở miệng khối đến kỹ thuật chuốt thành gốm trên bàn xoay.
					</p>
				</div>

				<div class="bv-benefit-card">
					<span class="bv-benefit-num">02</span>
					<h3 class="bv-benefit-title">Tự Tạo 2 - 3 Tác Phẩm</h3>
					<p class="bv-benefit-desc">
						Thỏa sức tạo hình ly uống nước, chén trà, bình hoa nhỏ hoặc đĩa đựng bánh. Bạn được chọn giữ lại những tác phẩm ưng ý nhất để đem nung.
					</p>
				</div>

				<div class="bv-benefit-card">
					<span class="bv-benefit-num">03</span>
					<h3 class="bv-benefit-title">Men Tro Khoáng Lò 1.250°C</h3>
					<p class="bv-benefit-desc">
						Xưởng hỗ trợ bạn tráng men tro tự nhiên không chì, nung củi nhiệt lớn 1.250°C đảm bảo xương gốm đanh chắc và an toàn tuyệt đối cho thực phẩm.
					</p>
				</div>

				<div class="bv-benefit-card">
					<span class="bv-benefit-num">04</span>
					<h3 class="bv-benefit-title">Đóng Gói Gửi Tận Tay</h3>
					<p class="bv-benefit-desc">
						Sau khi hoàn thiện nung chín (khoảng 7 – 10 ngày), tác phẩm được xưởng đóng gói chống sốc an toàn và gửi bưu điện về tận địa chỉ nhà bạn.
					</p>
				</div>

			</div>
		</div>
	</section>

	<!-- 4. INTERACTIVE BOOKING ENGINE (CENTERED APP) -->
	<section class="bv-ws-booking-section" id="dat-lich">
		<div class="bv-container">
			<?php echo do_shortcode( '[ceramic_workshop_booking]' ); ?>
		</div>
	</section>

	<!-- 5. WORKSHOP FAQ (NO ICONS - EDITORIAL Q&A) -->
	<section class="bv-ws-faq-section">
		<div class="bv-container">
			<div class="bv-section-header">
				<span class="bv-section-eyebrow">GIẢI ĐÁP THẮC MẮC</span>
				<h2 class="bv-section-title">Câu Hỏi Thường Gặp Về Workshop</h2>
			</div>

			<div class="bv-faq-grid">
				
				<div class="bv-faq-card">
					<span class="bv-faq-num">Q1</span>
					<div class="bv-faq-body">
						<h3 class="bv-faq-q">Chưa từng làm gốm bao giờ có tham gia được không?</h3>
						<p class="bv-faq-a">
							Hoàn toàn được! Hơn 90% học viên đến xưởng là người lần đầu tiếp xúc với bàn xoay. Các nghệ nhân sẽ hướng dẫn chậm rãi từng thao tác để bất kỳ ai cũng có thể tự tay hoàn thiện sản phẩm.
						</p>
					</div>
				</div>

				<div class="bv-faq-card">
					<span class="bv-faq-num">Q2</span>
					<div class="bv-faq-body">
						<h3 class="bv-faq-q">Học phí workshop đã bao gồm những chi phí gì?</h3>
						<p class="bv-faq-a">
							Chi phí đã bao gồm trọn gói: Đất sét không giới hạn, dụng cụ tạo tác, màu men khoáng thực vật, công nung lò nhiệt độ cao 2 lần và một phần trà thảo mộc hoa cúc nhâm nhi trong giờ nghỉ.
						</p>
					</div>
				</div>

				<div class="bv-faq-card">
					<span class="bv-faq-num">Q3</span>
					<div class="bv-faq-body">
						<h3 class="bv-faq-q">Khi nào tôi có thể nhận lại tác phẩm sau nung?</h3>
						<p class="bv-faq-a">
							Đồ gốm mộc cần phơi khô tự nhiên từ 3–5 ngày để tránh nứt gió, sau đó nung sơ bộ, tráng men và nung chín ở 1.250°C trong 36 giờ. Tổng thời gian hoàn thiện khoảng 7–10 ngày. Bạn có thể ghé xưởng lấy hoặc nhờ xưởng chuyển phát nhanh về tận nhà.
						</p>
					</div>
				</div>

				<div class="bv-faq-card">
					<span class="bv-faq-num">Q4</span>
					<div class="bv-faq-body">
						<h3 class="bv-faq-q">Xưởng có nhận tổ chức teambuilding hoặc nhóm đông người?</h3>
						<p class="bv-faq-a">
							Có. Chúng tôi thường xuyên tiếp đón các đoàn thể, công ty hoặc gia đình với quy mô từ 10 đến 35 người. Vui lòng liên hệ trực tiếp qua Hotline/Zalo <strong>0363 292 271</strong> để sắp xếp khung giờ riêng.
						</p>
					</div>
				</div>

			</div>
		</div>
	</section>

	<!-- 6. STUDIO LOCATION & CONTACT NOTE -->
	<section class="bv-ws-contact-bar-section">
		<div class="bv-container">
			<div class="bv-ws-contact-bar">
				<div class="bv-wsc-text">
					<h4>Cần tư vấn thêm về lịch học hoặc ca riêng?</h4>
					<p>Liên hệ trực tiếp với người phụ trách xưởng: <strong>0363 292 271</strong> (Mr. Vũ Mạnh Hướng)</p>
				</div>
				<div class="bv-wsc-action">
					<a href="<?php echo esc_url( $zalo_url ); ?>" target="_blank" rel="noopener" class="bv-btn-wsc-zalo">
						Nhắn Tin Zalo: 0363 292 271
					</a>
				</div>
			</div>
		</div>
	</section>

</div>

<?php
get_footer();
