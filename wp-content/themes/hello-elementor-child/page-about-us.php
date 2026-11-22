<?php
/**
 * Template Name: About Us (Về Xưởng Gốm)
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
$workshop_url = home_url( '/workshop/' );
$contact_url  = home_url( '/contact-us/' );
?>

<div class="bv-about-page">

	<!-- 1. BREADCRUMB (CLEAN EDITORIAL TRAIL - NO NUMBERS) -->
	<nav class="bv-about-breadcrumb" aria-label="Đường dẫn">
		<div class="bv-container">
			<div class="bv-breadcrumb-trail">
				<a href="<?php echo esc_url( $home_url ); ?>">Trang chủ</a>
				<span class="bv-b-sep">/</span>
				<span class="bv-b-current" aria-current="page">Về Xưởng Gốm</span>
			</div>
		</div>
	</nav>

	<!-- 2. HERO EDITORIAL BANNER -->
	<section class="bv-about-hero">
		<div class="bv-container">
			<div class="bv-about-hero-header">
				<span class="bv-about-eyebrow">CERAMIC STUDIO • VỀ CHÚNG TÔI</span>
				<h1 class="bv-about-title">
					Vẻ Đẹp Của Sự Bất Toàn Tự Nhiên &amp; Ngọn Lửa Lò 1.250°C
				</h1>
				<p class="bv-about-subtitle">
					Tại làng cổ Bát Tràng, chúng tôi gìn giữ tinh thần làm gốm vuốt tay thủ công mộc mạc — nơi mỗi tác phẩm là một kết tinh độc bản giữa đất mẹ phù sa, nước nguồn tinh lọc và ngọn lửa lò bền bỉ.
				</p>
			</div>

			<div class="bv-about-hero-banner">
				<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/09/gom-demo-15-e3ec755cc48b7069c346.webp' ) ); ?>" alt="Không gian xưởng gốm thủ công Ceramic Studio tại Bát Tràng" class="bv-hero-img" loading="eager">
				<div class="bv-hero-caption">
					<span>Không gian xưởng gốm mộc — Thôn 1 Giang Cao, Bát Tràng, Gia Lâm, Hà Nội</span>
				</div>
			</div>
		</div>
	</section>

	<!-- 3. BRAND PHILOSOPHY & FOUNDER STORY -->
	<section class="bv-about-story-section">
		<div class="bv-container">
			<div class="bv-story-grid">
				
				<!-- Story Visual -->
				<div class="bv-story-media">
					<div class="bv-story-img-wrap">
						<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/09/gom-demo-3-19c71de367daef049112.webp' ) ); ?>" alt="Nghệ nhân tạo tác gốm vuốt tay trên bàn xoay" class="bv-story-img" loading="lazy">
					</div>
					<div class="bv-story-subcard">
						<span class="bv-subcard-label">TRIẾT LÝ WABI-SABI</span>
						<p>Chấp nhận vẻ đẹp bất toàn, mộc mạc và trường tồn cùng thời gian.</p>
					</div>
				</div>

				<!-- Story Narrative -->
				<div class="bv-story-content">
					<span class="bv-story-tag">KHỞI NGUYÊN &amp; TÂM HUYẾT</span>
					<h2 class="bv-story-heading">
						Gìn Giữ Hồn Gốm Việt Trong Từng Nhịp Xoay Bàn Gỗ
					</h2>
					<div class="bv-story-prose">
						<p>
							Giữa nhịp sống hiện đại với những sản phẩm công nghiệp sản xuất hàng loạt bằng khuôn đúc vô hồn, <strong>Ceramic Studio</strong> ra đời như một nốt trầm an yên. Chúng tôi chọn quay về với những giá trị nguyên bản nhất của nghề gốm: đôi bàn tay trần lấm lem đất sét, vòng quay đều đặn của bàn xoay gỗ và ngọn lửa lò củi truyền thống.
						</p>
						<p>
							Được sáng lập bởi nghệ nhân <strong>Vũ Mạnh Hướng</strong> cùng các bạn trẻ say mê mỹ thuật dân gian tại làng gốm Bát Tràng, mỗi sản phẩm tại xưởng không chỉ đơn thuần là vật dụng thường nhật — mà là một thực thể sống động mang năng lượng mộc của đất trời, mang lại cảm giác tĩnh tại cho không gian sống của bạn.
						</p>
					</div>

					<blockquote class="bv-story-quote">
						<p>
							“Đồ gốm mộc không cần phô trương vẻ bóng bẩy nhân tạo. Khi bạn nâng trên tay một chiếc ly men loang tự nhiên, sức nặng của đất và sự ấm áp của ngọn lửa lò nung sẽ nhắc bạn chậm lại để cảm nhận trọn vẹn hiện tại.”
						</p>
						<cite>— Vũ Mạnh Hướng (Người sáng lập Ceramic Studio)</cite>
					</blockquote>
				</div>

			</div>
		</div>
	</section>

	<!-- 4. CRAFT PROCESS (5 EDITORIAL STEPS - NO ICONS) -->
	<section class="bv-process-section">
		<div class="bv-container">
			<div class="bv-section-header">
				<span class="bv-section-eyebrow">QUY TRÌNH TẠO TÁC</span>
				<h2 class="bv-section-title">Năm Công Đoạn Kỳ Công Thành Hình Tác Phẩm</h2>
				<p class="bv-section-desc">
					Không có khuôn dập vội vã. Mỗi mẻ gốm mộc tại xưởng trải qua ít nhất 14 ngày lao động miệt mài và cẩn trọng.
				</p>
			</div>

			<div class="bv-process-grid">
				
				<!-- Step 01 -->
				<article class="bv-process-card">
					<div class="bv-process-num">01</div>
					<div class="bv-process-media">
						<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/09/gom-demo-6-1d6b5c1ef4a6aa2bae5c.webp' ) ); ?>" alt="Tuyển chọn đất sét phù sa sông Hồng" loading="lazy">
					</div>
					<div class="bv-process-info">
						<h3 class="bv-process-title">Tuyển Chọn &amp; Ủ Đất</h3>
						<p>
							Sử dụng đất sét phù sa sông Hồng được lắng đọng tinh lọc qua nhiều lớp bể, ủ ngấu tự nhiên trong nhiều tháng để khử bọt khí và đạt độ dẻo mịn tuyệt đối.
						</p>
					</div>
				</article>

				<!-- Step 02 -->
				<article class="bv-process-card">
					<div class="bv-process-num">02</div>
					<div class="bv-process-media">
						<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/09/gom-demo-1-8225e71abedc20d35ece.webp' ) ); ?>" alt="Chuốt gốm bàn xoay thủ công" loading="lazy">
					</div>
					<div class="bv-process-info">
						<h3 class="bv-process-title">Chuốt Gốm Bàn Xoay</h3>
						<p>
							Từng dáng ấm, miệng chén và đường cong của thân bình được nghệ nhân tạo hình thủ công 100% bằng bàn tay trên bàn xoay, giữ nguyên từng vệt vân mộc sống động.
						</p>
					</div>
				</article>

				<!-- Step 03 -->
				<article class="bv-process-card">
					<div class="bv-process-num">03</div>
					<div class="bv-process-media">
						<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/09/gom-demo-14-fe71c673139dfcff9e29.webp' ) ); ?>" alt="Tiện mộc và phơi chậm tự nhiên" loading="lazy">
					</div>
					<div class="bv-process-info">
						<h3 class="bv-process-title">Tiện Mộc &amp; Phơi Chậm</h3>
						<p>
							Gốm mộc sau khi tạo hình được gọt tỉa đáy, gắn quai cẩn trọng rồi phơi chậm trong bóng râm từ 3 đến 5 ngày để đất co ngót tự nhiên, tránh hiện tượng rạn nứt.
						</p>
					</div>
				</article>

				<!-- Step 04 -->
				<article class="bv-process-card">
					<div class="bv-process-num">04</div>
					<div class="bv-process-media">
						<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/09/gom-demo-4-470be51447a4999caf62.webp' ) ); ?>" alt="Phối men tro khoáng thiên nhiên" loading="lazy">
					</div>
					<div class="bv-process-info">
						<h3 class="bv-process-title">Phối Men Tro Tự Nhiên</h3>
						<p>
							Nước men được chế tác hoàn toàn từ tro trấu, tro củi tự nhiên kết hợp khoáng thạch bản địa. Cam kết không chứa chì hay oxit kim loại công nghiệp độc hại.
						</p>
					</div>
				</article>

				<!-- Step 05 -->
				<article class="bv-process-card">
					<div class="bv-process-num">05</div>
					<div class="bv-process-media">
						<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/09/gom-demo-10-ec0d8f387f86a7328ab1.webp' ) ); ?>" alt="Lò nung củi nhiệt lớn 1.250 độ C" loading="lazy">
					</div>
					<div class="bv-process-info">
						<h3 class="bv-process-title">Lò Nung Củi 1.250°C</h3>
						<p>
							Lửa lò nung liên tục hơn 30 giờ ở nhiệt độ 1.250°C. Ngọn lửa tôi luyện xương gốm đanh chắc như đá và tạo nên những mảng men hỏa biến ngẫu hứng độc nhất vô nhị.
						</p>
					</div>
				</article>

			</div>
		</div>
	</section>

	<!-- 5. THREE CORE VALUES (EDITORIAL PILLARS - NO ICONS) -->
	<section class="bv-values-section">
		<div class="bv-container">
			<div class="bv-section-header">
				<span class="bv-section-eyebrow">GIÁ TRỊ CỐT LÕI</span>
				<h2 class="bv-section-title">Ba Cam Kết Từ Người Làm Gốm</h2>
			</div>

			<div class="bv-values-grid">
				
				<div class="bv-value-card">
					<div class="bv-value-num">I</div>
					<h3 class="bv-value-title">Tính Độc Bản Tuyệt Đối</h3>
					<p class="bv-value-desc">
						Không có hai tác phẩm nào giống nhau tuyệt đối. Mỗi độ co ngót của đất và vệt loang ngẫu hứng của tro nung biến mỗi chiếc ly, bình gốm thành tác phẩm duy nhất trên thế gian.
					</p>
				</div>

				<div class="bv-value-card">
					<div class="bv-value-num">II</div>
					<h3 class="bv-value-title">Men Tro Thiên Nhiên An Toàn</h3>
					<p class="bv-value-desc">
						Nung ở nhiệt độ cao 1.250°C giúp loại bỏ hoàn toàn tạp chất. Men khoáng thực vật an toàn tuyệt đối cho đồ uống nóng, dùng được trong lò vi sóng và máy rửa bát gia đình.
					</p>
				</div>

				<div class="bv-value-card">
					<div class="bv-value-num">III</div>
					<h3 class="bv-value-title">Không Gian Kết Nối &amp; Chữa Lành</h3>
					<p class="bv-value-desc">
						Ceramic Studio mở cửa đón bạn đến chạm tay vào đất qua các buổi workshop cuối tuần. Đây là nơi bạn tìm lại sự cân bằng, xua tan căng thẳng sau những bận rộn đời thường.
					</p>
				</div>

			</div>
		</div>
	</section>

	<!-- 6. CALL TO ACTION & STUDIO INVITATION -->
	<section class="bv-about-cta-section">
		<div class="bv-container">
			<div class="bv-about-cta-box">
				<span class="bv-about-cta-eyebrow">CHÀO ĐÓN BẠN GHÉ THĂM</span>
				<h2 class="bv-about-cta-title">
					Trải Nghiệm Nghệ Thuật Gốm Mộc Cùng Ceramic Studio
				</h2>
				<p class="bv-about-cta-desc">
					Dù bạn đang tìm kiếm một bộ tách gốm men tro tinh tế cho góc trà nhà mình hay muốn tự tay nặn chiếc ly đầu tiên trên bàn xoay, chúng tôi luôn sẵn sàng đón tiếp.
				</p>
				<div class="bv-about-cta-actions">
					<a href="<?php echo esc_url( $shop_url ); ?>" class="bv-btn-about-primary">
						Xem Bộ Sưu Tập Gốm
					</a>
					<a href="<?php echo esc_url( $workshop_url ); ?>" class="bv-btn-about-secondary">
						Đăng Ký Workshop Cuối Tuần
					</a>
				</div>
				<div class="bv-about-cta-meta">
					<span>Địa chỉ: Thôn 1 Giang Cao, Bát Tràng, Hà Nội</span>
					<span class="bv-c-sep">•</span>
					<span>Hotline: <a href="tel:0363292271">0363 292 271</a> (Mr. Hướng)</span>
				</div>
			</div>
		</div>
	</section>

</div>

<?php
get_footer();
