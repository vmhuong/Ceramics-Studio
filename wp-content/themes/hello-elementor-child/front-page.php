<?php
/**
 * Wabi-Sabi Homepage Template - Ceramic Studio
 * Style: Luxury Ceramic Craft Minimalism (No Icons / Pure Typography)
 * Theme: Hello Elementor Child for Ceramic Studio
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();

// Fetch featured pottery products (exclude virtual and workshop tickets)
$pottery_products = wc_get_products( [
	'limit'      => 12,
	'status'     => 'publish',
	'visibility' => 'catalog',
	'virtual'    => false,
	'orderby'    => 'date',
	'order'      => 'DESC',
] );

// Strict filter: keep only actual pottery products
$pottery_products = array_values( array_filter( $pottery_products, function ( $p ) {
	return ! $p->is_virtual() && 0 !== strpos( $p->get_sku(), 'WS-TICKET' ) && false === stripos( $p->get_name(), 'vé' );
} ) );

// Limit to 8 items for the grid
$pottery_products = array_slice( $pottery_products, 0, 8 );

$shop_url     = wc_get_page_permalink( 'shop' );
$workshop_url = home_url( '/workshop/' );
?>

<main id="content" class="site-main bv-wabi-sabi-home">

	<!-- 1. HERO SECTION -->
	<section class="bv-hero-section">
		<div class="bv-hero-container">
			<div class="bv-hero-content">
				<span class="bv-hero-tag">CERAMIC STUDIO • MỘC MẠC &amp; TĨNH TẠI</span>
				<h1 class="bv-hero-title">Nơi Đất Mẹ Gặp Lửa, Tạo Nên Những Tác Phẩm Có Hồn</h1>
				<p class="bv-hero-desc">Mỗi món gốm tại xưởng đều được nặn tay, chuốt men khoáng tự nhiên và nung củi truyền thống, mang theo hơi thở bình yên cho không gian sống của bạn.</p>
				<div class="bv-hero-actions">
					<a href="<?php echo esc_url( $shop_url ); ?>" class="bv-btn-primary">
						Khám Phá Gốm Mộc
					</a>
					<a href="<?php echo esc_url( $workshop_url ); ?>" class="bv-btn-secondary">
						Đặt Lịch Workshop
					</a>
				</div>
				<div class="bv-hero-highlights">
					<div class="bv-hl-item">
						<strong>100%</strong>
						<span>Làm tay độc bản</span>
					</div>
					<div class="bv-hl-divider" aria-hidden="true"></div>
					<div class="bv-hl-item">
						<strong>1.250°C</strong>
						<span>Nung lửa bền vĩnh cửu</span>
					</div>
					<div class="bv-hl-divider" aria-hidden="true"></div>
					<div class="bv-hl-item">
						<strong>Men mộc</strong>
						<span>Không hóa chất độc hại</span>
					</div>
				</div>
			</div>
			<div class="bv-hero-visual">
				<div class="bv-hero-image-card">
					<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/09/gom-demo-15-e3ec755cc48b7069c346.webp' ) ); ?>" alt="Đồ gốm mộc thủ công Ceramic Studio" class="bv-hero-img" loading="eager" fetchpriority="high">
					<div class="bv-hero-floating-card">
						<div>
							<strong>Men Nâu Rustic</strong>
							<small>Bộ sưu tập mùa thu</small>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- 2. CURATED COLLECTIONS -->
	<section class="bv-collections-section">
		<div class="bv-container">
			<div class="bv-section-header">
				<span class="bv-sub-tag">DANH MỤC TIÊU BIỂU</span>
				<h2 class="bv-section-title">Bốn Dòng Gốm Đặc Trưng</h2>
				<p class="bv-section-desc">Mỗi bộ sưu tập mang một ngôn ngữ tạo hình và kỹ thuật men khoáng riêng biệt.</p>
			</div>
			<div class="bv-collections-grid">
				<a href="<?php echo esc_url( $shop_url ); ?>" class="bv-col-card">
					<div class="bv-col-thumb">
						<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/09/gom-demo-20-8e65a9da8b2256ebbe41.webp' ) ); ?>" alt="Bình hoa gốm mộc" loading="lazy">
					</div>
					<div class="bv-col-info">
						<h3>Bình hoa &amp; Bình gốm dáng mộc</h3>
						<p>Dáng tròn, miệng hoa, dáng rượu men pastel thanh nhã.</p>
						<span class="bv-col-link">Khám phá bộ sưu tập</span>
					</div>
				</a>
				<a href="<?php echo esc_url( $shop_url ); ?>" class="bv-col-card">
					<div class="bv-col-thumb">
						<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/09/gom-demo-13-32f4983a4ea37d864f73.webp' ) ); ?>" alt="Tách ly cafe men tro" loading="lazy">
					</div>
					<div class="bv-col-info">
						<h3>Tách ly &amp; Mug Cafe thủ công</h3>
						<p>Men tro, men loang có quai, giữ nhiệt ấm áp cho buổi sáng.</p>
						<span class="bv-col-link">Khám phá bộ sưu tập</span>
					</div>
				</a>
				<a href="<?php echo esc_url( $shop_url ); ?>" class="bv-col-card">
					<div class="bv-col-thumb">
						<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/09/gom-demo-19-a77d35793ddead77097f.webp' ) ); ?>" alt="Bộ chén đĩa cơm nhà" loading="lazy">
					</div>
					<div class="bv-col-info">
						<h3>Bộ bàn ăn &amp; Chén đĩa cơm nhà</h3>
						<p>Bát vành bèo, chén chấm lam, set 4 người ấm cúng gia đình.</p>
						<span class="bv-col-link">Khám phá bộ sưu tập</span>
					</div>
				</a>
				<a href="<?php echo esc_url( $shop_url ); ?>" class="bv-col-card">
					<div class="bv-col-thumb">
						<img src="<?php echo esc_url( home_url( '/wp-content/uploads/2026/09/gom-demo-14-fe71c673139dfcff9e29.webp' ) ); ?>" alt="Tranh gạch gốm nghệ thuật" loading="lazy">
					</div>
					<div class="bv-col-info">
						<h3>Tranh gạch gốm &amp; Set quà tặng</h3>
						<p>Chủ đề Chim Lạc, Mai Lan Cúc Trúc, set quà trà chiều mộc.</p>
						<span class="bv-col-link">Khám phá bộ sưu tập</span>
					</div>
				</a>
			</div>
		</div>
	</section>

	<!-- 3. FEATURED LIVE PRODUCTS -->
	<section class="bv-products-section">
		<div class="bv-container">
			<div class="bv-section-header">
				<span class="bv-sub-tag">TÁC PHẨM TUYỂN CHỌN</span>
				<h2 class="bv-section-title">Gốm Mới Ra Lò</h2>
				<p class="bv-section-desc">Mỗi tác phẩm là độc bản, số lượng giới hạn theo từng mẻ nung.</p>
			</div>
			<div class="bv-products-grid">
				<?php if ( ! empty( $pottery_products ) ) : ?>
					<?php foreach ( $pottery_products as $product ) : 
						$img_url      = wp_get_attachment_image_url( $product->get_image_id(), 'medium' ) ?: home_url( '/wp-content/uploads/woocommerce-placeholder.webp' );
						$is_on_sale   = $product->is_on_sale();
						$price_html   = $product->get_price_html();
						$discount_pct = 0;
						if ( $is_on_sale ) {
							$reg = (float) $product->get_regular_price();
							$sal = (float) $product->get_sale_price();
							if ( $reg > 0 && $sal > 0 && $reg > $sal ) {
								$discount_pct = round( ( ( $reg - $sal ) / $reg ) * 100 );
							}
						}
					?>
						<article class="bv-product-tile <?php echo $is_on_sale ? 'is-on-sale' : ''; ?>">
							<div class="bv-pt-image-wrap">
								<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="bv-pt-img-link" tabindex="-1">
									<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $product->get_name() ); ?>" loading="lazy">
								</a>
								<?php if ( $discount_pct > 0 ) : ?>
									<span class="bv-card-sale-badge">-<?php echo esc_html( $discount_pct ); ?>%</span>
								<?php endif; ?>
								<div class="bv-card-actions-bar">
									<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" class="bv-btn-overlay-cart">
										Thêm vào giỏ
									</a>
								</div>
							</div>
							<div class="bv-pt-body">
								<div class="bv-card-meta-line">Gốm Mộc Thủ Công</div>
								<h3 class="bv-pt-title">
									<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
										<?php echo esc_html( $product->get_name() ); ?>
									</a>
								</h3>
								<div class="bv-card-bottom-row">
									<div class="bv-card-price-col">
										<div class="bv-card-price-line">
											<?php if ( $is_on_sale && $product->get_regular_price() ) : ?>
												<del class="bv-price-del"><?php echo wc_price( $product->get_regular_price() ); ?></del>
												<ins class="bv-price-ins"><?php echo wc_price( $product->get_sale_price() ); ?></ins>
											<?php else : ?>
												<span class="bv-price-main"><?php echo wp_kses_post( $price_html ); ?></span>
											<?php endif; ?>
										</div>
										<?php if ( $discount_pct > 0 ) : ?>
											<span class="bv-price-save-tag">-<?php echo esc_html( $discount_pct ); ?>%</span>
										<?php endif; ?>
									</div>
									<div class="bv-card-rating-badge">
										<span class="bv-star">★</span> 5.0
									</div>
								</div>
							</div>
						</article>
					<?php endforeach; ?>
				<?php endif; ?>
			</div>
			<div class="bv-products-cta">
				<a href="<?php echo esc_url( $shop_url ); ?>" class="bv-btn-outline">
					Xem Tất Cả Tác Phẩm Gốm
				</a>
			</div>
		</div>
	</section>

	<!-- 4. CRAFTSMANSHIP & WABI-SABI PHILOSOPHY -->
	<section class="bv-craft-section">
		<div class="bv-container">
			<div class="bv-craft-layout">
				<div class="bv-craft-intro">
					<span class="bv-sub-tag">TRIẾT LÝ WABI-SABI</span>
					<h2>Tìm Về Vẻ Đẹp Bình Yên Trong Từng Thớ Đất</h2>
					<p>Trong văn hóa gốm mộc, chúng tôi không tìm kiếm sự hoàn hảo công nghiệp đồng loạt. Một vết rạn tự nhiên của men, một dấu vân tay của người chuốt đất hay vệt loang ngẫu hứng của ngọn lửa đều là linh hồn riêng biệt của từng tác phẩm.</p>
					<div class="bv-quote-box">
						<p>"Vẻ đẹp đích thực của đồ gốm không nằm ở sự bóng bẩy, mà ở hơi ấm nó mang lại cho ngôi nhà của bạn."</p>
						<small>— Nghệ nhân Ceramic Studio</small>
					</div>
				</div>
				<div class="bv-steps-column">
					<div class="bv-step-item">
						<span class="bv-step-index">01</span>
						<div class="bv-step-detail">
							<h4>Đất sét phù sa tinh lọc</h4>
							<p>Đất sét được đãi sạch tạp chất, ủ tự nhiên nhiều tháng để đạt độ dẻo mịn và tính đàn hồi cao nhất.</p>
						</div>
					</div>
					<div class="bv-step-item">
						<span class="bv-step-index">02</span>
						<div class="bv-step-detail">
							<h4>Chuốt dáng trên bàn xoay</h4>
							<p>Bàn tay nghệ nhân kết hợp nhịp nhàng giữa lực đẩy và cảm xúc để định tâm và kéo từng thớ đất thành dáng.</p>
						</div>
					</div>
					<div class="bv-step-item">
						<span class="bv-step-index">03</span>
						<div class="bv-step-detail">
							<h4>Phủ men khoáng tự nhiên</h4>
							<p>Nước men điều chế hoàn toàn từ tro trấu, đá vôi và khoáng tự nhiên, không chì, an toàn tuyệt đối cho đồ ăn.</p>
						</div>
					</div>
					<div class="bv-step-item">
						<span class="bv-step-index">04</span>
						<div class="bv-step-detail">
							<h4>Nung lửa lớn 1.250°C</h4>
							<p>24 giờ trong lò nung tạo nên độ đanh chắc như đá, giữ nhiệt hoàn hảo và bền bỉ qua năm tháng.</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- 5. WORKSHOP INVITATION -->
	<section class="bv-workshop-hero-section">
		<div class="bv-container">
			<div class="bv-ws-box">
				<div class="bv-ws-box-content">
					<span class="bv-ws-badge">TRẢI NGHIỆM CUỐI TUẦN</span>
					<h2>Rời Xa Màn Hình, Tự Tay Tạc Chiếc Ly Gốm Đầu Tiên</h2>
					<p>Dành 2.5 giờ cuối tuần tại xưởng gốm: học kỹ thuật chuốt bàn xoay, tự do nặn tạo hình và vẽ men màu. Tác phẩm sau khi nung sẽ là kỷ niệm vô giá bạn mang về nhà.</p>
					<ul class="bv-ws-features">
						<li>Có nghệ nhân kèm 1-1 cho người mới bắt đầu</li>
						<li>Bao trọn đất sét, dụng cụ, màu vẽ men và nung 2 lần</li>
						<li>Thưởng thức trà thảo mộc trong không gian âm nhạc mộc</li>
					</ul>
					<div class="bv-ws-box-cta">
						<a href="<?php echo esc_url( $workshop_url ); ?>" class="bv-btn-primary bv-btn-lg">
							Xem Lịch Ca Học &amp; Đặt Chỗ Ngay
						</a>
						<span class="bv-ws-seat-note">Giới hạn 8 học viên / ca để đảm bảo chất lượng</span>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- 6. REAL CUSTOMER REVIEWS -->
	<section class="bv-testimonials-section">
		<div class="bv-container">
			<div class="bv-section-header">
				<span class="bv-sub-tag">NGƯỜI YÊU GỐM CHIA SẺ</span>
				<h2 class="bv-section-title">Khách Hàng Nói Về Tiệm Gốm</h2>
			</div>
			<div class="bv-testimonials-grid">
				<div class="bv-review-card">
					<div class="bv-rc-rating"><span class="bv-star">★</span> 5.0 · Đánh giá trải nghiệm</div>
					<p class="bv-rc-content">"Bình hoa gốm mộc đẹp xuất sắc ngoài mong đợi! Nước men lì sờ rất thích, cắm hoa sen hay cúc đều tôn dáng hoa. Xưởng đóng gói siêu kỹ, quấn 3 lớp bóng khí và chèn xốp đầy hộp, nhận hàng nguyên vẹn không tì vết!"</p>
					<div class="bv-rc-author">
						<strong>Thanh Thảo</strong>
						<span class="bv-rc-badge">Đã mua Bình hoa dáng tròn</span>
					</div>
				</div>
				<div class="bv-review-card">
					<div class="bv-rc-rating"><span class="bv-star">★</span> 5.0 · Đánh giá trải nghiệm</div>
					<p class="bv-rc-content">"Men tro tự nhiên màu rất có chiều sâu, uống cà phê sáng cầm cực kỳ đầm tay và giữ ấm tốt. Chiếc ly thủ công mộc mạc đúng gu mình tìm bấy lâu nay. Sẽ ủng hộ thêm set chén dĩa nữa."</p>
					<div class="bv-rc-author">
						<strong>Hoàng Nam</strong>
						<span class="bv-rc-badge">Đã mua Ly gốm men tro</span>
					</div>
				</div>
				<div class="bv-review-card">
					<div class="bv-rc-rating"><span class="bv-star">★</span> 5.0 · Đánh giá trải nghiệm</div>
					<p class="bv-rc-content">"Hộp quà đóng gói chỉn chu, có dây thừng mộc và thiệp xinh xắn. Mình mua tặng tân gia đồng nghiệp ai cũng khen nức nở vì vừa tinh tế vừa khác biệt với đồ gốm công nghiệp. 10/10 điểm!"</p>
					<div class="bv-rc-author">
						<strong>Bảo Trâm</strong>
						<span class="bv-rc-badge">Đã mua Set quà trà chiều</span>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- 7. CRAFT COMMITMENTS (TRUST BADGES) -->
	<section class="bv-promises-section">
		<div class="bv-container">
			<div class="bv-promises-grid">
				<div class="bv-promise-item">
					<span class="bv-pi-num">01</span>
					<h4>Đóng gói chống sốc 3 lớp</h4>
					<p>Bảo hành bể vỡ 100%: đổi mới ngay lập tức nếu có bất kỳ nứt mẻ nào trong quá trình vận chuyển.</p>
				</div>
				<div class="bv-promise-item">
					<span class="bv-pi-num">02</span>
					<h4>Men khoáng an toàn 100%</h4>
					<p>Tuyệt đối không sử dụng men chì, an toàn cho đồ ăn uống nóng, lò vi sóng và máy rửa chén.</p>
				</div>
				<div class="bv-promise-item">
					<span class="bv-pi-num">03</span>
					<h4>Hộp quà mộc thân thiện</h4>
					<p>Bao bì mộc mạc từ giấy carton tái chế, dây cói tự nhiên và thiệp dặn dò viết tay ấm áp.</p>
				</div>
			</div>
		</div>
	</section>

</main>

<?php get_footer(); ?>
