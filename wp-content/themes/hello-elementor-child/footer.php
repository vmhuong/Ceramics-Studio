<?php
/**
 * Footer template - Phong cách Mộc mạc Nhật Bản (Wabi-Sabi / Zen)
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
			
			<!-- COLUMN 1: BRAND STORY & COMMITMENT -->
			<div class="bv-footer-col bv-footer-col--brand">
				<div class="bv-footer-brand">
					<div class="bv-flogo-icon" aria-hidden="true">
						<svg width="26" height="26" viewBox="0 0 32 32" fill="none">
							<path d="M16 3C10.5 3 6 7 6 12C6 16.5 9 19.5 12 21.5V26C12 27.1 12.9 28 14 28H18C19.1 28 20 27.1 20 26V21.5C23 19.5 26 16.5 26 12C26 7 21.5 3 16 3Z" stroke="#D27342" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							<path d="M11 12C11 12 13 14 16 14C19 14 21 12 21 12" stroke="#A69C94" stroke-width="1.8" stroke-linecap="round"/>
						</svg>
					</div>
					<div class="bv-flogo-text">
						<span class="bv-fbrand-title">CERAMIC STUDIO</span>
						<span class="bv-fbrand-sub">Xưởng Gốm Mộc & Tinh Thần Wabi-Sabi</span>
					</div>
				</div>

				<p class="bv-footer-story-text">
					Nơi đất mẹ phù sa hòa cùng ngọn lửa lò 1.250°C. Mỗi tác phẩm mang vẻ đẹp của sự bất toàn tự nhiên, mộc mạc và chân thật — mang lại cảm giác an yên cho tổ ấm của bạn.
				</p>

				<div class="bv-footer-craft-badges">
					<span class="bv-fc-badge">✦ Men khoáng thiên nhiên</span>
					<span class="bv-fc-badge">✦ Nung lửa lớn 1.250°C</span>
					<span class="bv-fc-badge">✦ Vuốt tay độc bản</span>
				</div>
			</div>

			<!-- COLUMN 2: CERAMIC COLLECTIONS -->
			<div class="bv-footer-col">
				<h4 class="bv-footer-col-title">Bộ Sưu Tập Gốm</h4>
				<ul class="bv-footer-menu">
					<li><a href="<?php echo esc_url( $shop_url ); ?>">Bình Hoa Men Tro Dáng Mộc</a></li>
					<li><a href="<?php echo esc_url( $shop_url ); ?>">Bộ Tách Trà & Ly Cafe Espresso</a></li>
					<li><a href="<?php echo esc_url( $shop_url ); ?>">Bộ Bát Đĩa Mộc Bữa Cơm Nhà</a></li>
					<li><a href="<?php echo esc_url( $shop_url ); ?>">Chén Matcha & Đồ Dụng Trà Đạo</a></li>
					<li><a href="<?php echo esc_url( $shop_url ); ?>">Set Quà Tặng Gốm Thủ Công</a></li>
				</ul>
			</div>

			<!-- COLUMN 3: WORKSHOP & EXPERIENCES -->
			<div class="bv-footer-col">
				<h4 class="bv-footer-col-title">Trải Nghiệm Workshop</h4>
				<ul class="bv-footer-menu">
					<li><a href="<?php echo esc_url( $workshop_url ); ?>">Lịch Workshop Cuối Tuần</a></li>
					<li><a href="<?php echo esc_url( $workshop_url ); ?>">Khóa Xoay Gốm Bàn Xoay Tự Do</a></li>
					<li><a href="<?php echo esc_url( $workshop_url ); ?>">Ca Gia Đình & Cặp Đôi (2-4 người)</a></li>
					<li><a href="<?php echo esc_url( $workshop_url ); ?>">Teambuilding & Nhóm Doanh Nghiệp</a></li>
					<li><a href="<?php echo esc_url( $contact_url ); ?>">Quy Trình Nhận Tác Phẩm Sau Nung</a></li>
				</ul>
			</div>

			<!-- COLUMN 4: VISIT STUDIO & CONTACT -->
			<div class="bv-footer-col bv-footer-col--contact">
				<h4 class="bv-footer-col-title">Ghé Thăm Xưởng Gốm</h4>
				<div class="bv-footer-contact-details">
					<div class="bv-fc-item">
						<span class="bv-fc-icon" aria-hidden="true">📍</span>
						<span>Thôn 1 Giang Cao, Bát Tràng, Gia Lâm, Hà Nội</span>
					</div>
					<div class="bv-fc-item">
						<span class="bv-fc-icon" aria-hidden="true">📞</span>
						<span>Hotline / Zalo: <a href="https://zalo.me/0363292271" target="_blank" rel="noopener"><strong>0363 292 271</strong></a> (Mr. Hướng)</span>
					</div>
					<div class="bv-fc-item">
						<span class="bv-fc-icon" aria-hidden="true">✉️</span>
						<span>Email: <a href="mailto:vmhuong05@gmail.com">vmhuong05@gmail.com</a></span>
					</div>
					<div class="bv-fc-item">
						<span class="bv-fc-icon" aria-hidden="true">⏰</span>
						<span>Mở cửa: <strong>08:30 – 21:00</strong> (Tất cả các ngày)</span>
					</div>
				</div>
				<div class="bv-footer-cta-box">
					<a href="https://zalo.me/0363292271" target="_blank" rel="noopener" class="bv-fcta-zalo">
						<span>💬 Nhắn Zalo tư vấn gốm & workshop</span>
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
					<span>🛡️ Bảo hành bể vỡ 100% khi vận chuyển</span>
					<span class="bv-dot-sep">•</span>
					<span>🔄 Đổi trả trong 7 ngày</span>
					<span class="bv-dot-sep">•</span>
					<span>🌱 Đóng gói mộc thân thiện môi trường</span>
				</div>
			</div>
		</div>

	</div>

	<!-- BACK TO TOP FLOATING BUTTON -->
	<button type="button" id="bv-back-to-top" class="bv-back-to-top" aria-label="Cuộn lên đầu trang" title="Về đầu trang">
		<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 15l-6-6-6 6"/></svg>
	</button>
</footer>

<?php wp_footer(); ?>
</body>
</html>
