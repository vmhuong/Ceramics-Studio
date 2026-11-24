<?php
/**
 * Footer template - Phong cách Tối giản Cao cấp (Luxury Craft Minimalism)
 * Theme: Hello Elementor Child for Ceramic Studio
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$home_url     = home_url( '/' );
$shop_url     = home_url( '/san-pham-gom/' );
$workshop_url = home_url( '/workshop/' );
$about_url    = home_url( '/about-us/' );
$contact_url  = home_url( '/contact-us/' );
?>

</div><!-- #content -->

<footer id="site-footer" class="bv-site-footer wabi-sabi-footer">
	<div class="bv-footer-wrapper">
		
		<!-- MAIN 4-COLUMN FOOTER CONTENT -->
		<div class="bv-footer-grid">
			
			<!-- COLUMN 1: BRAND STORY & PHILOSOPHY -->
			<div class="bv-footer-col bv-footer-col--brand">
				<div class="bv-footer-brand">
					<div class="bv-flogo-text">
						<span class="bv-fbrand-title">CERAMIC STUDIO</span>
						<span class="bv-fbrand-sub">Xưởng Gốm Mộc &amp; Nghệ Thuật Lò Nung</span>
					</div>
				</div>

				<p class="bv-footer-story-text">
					Nơi đất sét phù sa hòa cùng ngọn lửa lò 1.250°C. Mỗi tác phẩm mang vẻ đẹp của sự bất toàn tự nhiên, mộc mạc và chân thật — mang lại cảm giác an yên cho tổ ấm của bạn.
				</p>

				<div class="bv-footer-craft-notes">
					<span>Men khoáng tự nhiên</span>
					<span class="bv-f-sep">•</span>
					<span>Nung lửa 1.250°C</span>
					<span class="bv-f-sep">•</span>
					<span>Vuốt tay độc bản</span>
				</div>
			</div>

			<!-- COLUMN 2: CERAMIC COLLECTIONS -->
			<div class="bv-footer-col">
				<h4 class="bv-footer-col-title">Bộ Sưu Tập Gốm</h4>
				<ul class="bv-footer-menu">
					<li><a href="<?php echo esc_url( $shop_url ); ?>">Bình hoa men tro dáng mộc</a></li>
					<li><a href="<?php echo esc_url( $shop_url ); ?>">Bộ tách trà &amp; ly cafe espresso</a></li>
					<li><a href="<?php echo esc_url( $shop_url ); ?>">Bộ bát đĩa mộc bữa cơm nhà</a></li>
					<li><a href="<?php echo esc_url( $shop_url ); ?>">Chén matcha &amp; đồ dùng trà đạo</a></li>
					<li><a href="<?php echo esc_url( $shop_url ); ?>">Set quà tặng gốm thủ công</a></li>
				</ul>
			</div>

			<!-- COLUMN 3: WORKSHOP & EXPERIENCES -->
			<div class="bv-footer-col">
				<h4 class="bv-footer-col-title">Trải Nghiệm Workshop</h4>
				<ul class="bv-footer-menu">
					<li><a href="<?php echo esc_url( $workshop_url ); ?>">Lịch workshop cuối tuần</a></li>
					<li><a href="<?php echo esc_url( $workshop_url ); ?>">Khóa xoay gốm bàn xoay tự do</a></li>
					<li><a href="<?php echo esc_url( $workshop_url ); ?>">Ca trải nghiệm đôi &amp; gia đình</a></li>
					<li><a href="<?php echo esc_url( $workshop_url ); ?>">Teambuilding &amp; đoàn thể</a></li>
					<li><a href="<?php echo esc_url( home_url( '/quy-dinh-workshop/' ) ); ?>">Quy định đặt &amp; đổi lịch</a></li>
				</ul>
			</div>

			<!-- COLUMN 4: VISIT STUDIO & CONTACT -->
			<div class="bv-footer-col bv-footer-col--contact">
				<h4 class="bv-footer-col-title">Ghé Thăm Xưởng Gốm</h4>
				<div class="bv-footer-contact-details">
					<div class="bv-fc-item">
						<span class="bv-fc-label">Địa chỉ:</span>
						<span>Thôn 1 Giang Cao, Bát Tràng, Gia Lâm, Hà Nội</span>
					</div>
					<div class="bv-fc-item">
						<span class="bv-fc-label">Hotline / Zalo:</span>
						<span><a href="https://zalo.me/0363292271" target="_blank" rel="noopener"><strong>0363 292 271</strong></a> (Mr. Hướng)</span>
					</div>
					<div class="bv-fc-item">
						<span class="bv-fc-label">Email:</span>
						<span><a href="mailto:vmhuong05@gmail.com">vmhuong05@gmail.com</a></span>
					</div>
					<div class="bv-fc-item">
						<span class="bv-fc-label">Giờ mở cửa:</span>
						<span>08:30 – 21:00 (Tất cả các ngày trong tuần)</span>
					</div>
				</div>
				<div class="bv-footer-cta-box">
					<a href="https://zalo.me/0363292271" target="_blank" rel="noopener" class="bv-fcta-zalo">
						Tư vấn trực tiếp qua Zalo
					</a>
				</div>
			</div>

		</div>

		<!-- BOTTOM COPYRIGHT & POLICY BAR -->
		<div class="bv-footer-bottom">
			<div class="bv-footer-bottom-flex">
				<p class="bv-footer-copy">
					&copy; <?php echo date( 'Y' ); ?> <strong>Ceramic Studio</strong> — Vũ Mạnh Hướng (PM28.11). Giữ trọn tinh hoa gốm thủ công Việt.
				</p>
				<div class="bv-footer-trust-notes">
					<a href="<?php echo esc_url( home_url( '/chinh-sach-doi-tra/' ) ); ?>">Bảo hành bể vỡ 100%</a>
					<span class="bv-dot-sep">•</span>
					<a href="<?php echo esc_url( home_url( '/chinh-sach-doi-tra/' ) ); ?>">Đổi trả trong 7 ngày</a>
					<span class="bv-dot-sep">•</span>
					<a href="<?php echo esc_url( home_url( '/chinh-sach-bao-mat/' ) ); ?>">Chính sách bảo mật</a>
					<span class="bv-dot-sep">•</span>
					<a href="<?php echo esc_url( home_url( '/quy-dinh-workshop/' ) ); ?>">Quy định workshop</a>
				</div>
			</div>
		</div>

	</div>

	<!-- BACK TO TOP BUTTON (MINIMALIST TEXT/ARROW) -->
	<button type="button" id="bv-back-to-top" class="bv-back-to-top" aria-label="Cuộn lên đầu trang" title="Về đầu trang">
		↑
	</button>
</footer>

<?php wp_footer(); ?>
</body>
</html>
