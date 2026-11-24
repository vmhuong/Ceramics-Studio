<?php
/**
 * The Template for displaying single product detail.
 * Style: Anh Ceramics Minimalist Craft (Compact Gallery + Aligned Add-to-Cart + 3 Detail Tabs)
 * Inspired by Anh Ceramics.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

while ( have_posts() ) : the_post();
	global $product;
	if ( ! is_a( $product, 'WC_Product' ) ) {
		continue;
	}

	$pid           = $product->get_id();
	$title         = $product->get_name();
	$sku           = $product->get_sku() ?: ( 'CERAMIC-' . $pid );
	$price_html    = $product->get_price_html();
	$short_desc    = $product->get_short_description();
	$desc          = get_the_content();
	$is_in_stock   = $product->is_in_stock();
	$stock_qty     = $product->get_stock_quantity() ?: 15;
	$is_on_sale    = $product->is_on_sale();
	
	// Categories
	$terms    = get_the_terms( $pid, 'product_cat' );
	$cat_name = ! empty( $terms ) && ! is_wp_error( $terms ) ? $terms[0]->name : 'Gốm Thủ Công';
	$cat_link = ! empty( $terms ) && ! is_wp_error( $terms ) ? get_term_link( $terms[0] ) : wc_get_page_permalink( 'shop' );

	// Images: Main + Gallery
	$main_img_id  = $product->get_image_id();
	$main_img_url = wp_get_attachment_image_url( $main_img_id, 'large' ) ?: home_url( '/wp-content/uploads/woocommerce-placeholder.webp' );
	$gallery_ids  = $product->get_gallery_image_ids();
	$all_img_ids  = array_filter( array_merge( [ $main_img_id ], $gallery_ids ) );
?>

<div class="bv-single-product-page">
	
	<!-- 1. BREADCRUMB (CLEAN TRAIL) -->
	<nav class="bv-product-breadcrumb" aria-label="Đường dẫn">
		<div class="bv-container">
			<div class="bv-breadcrumb-trail">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Trang chủ</a>
				<span class="bv-b-sep">/</span>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Sản phẩm</a>
				<span class="bv-b-sep">/</span>
				<a href="<?php echo esc_url( $cat_link ); ?>"><?php echo esc_html( $cat_name ); ?></a>
				<span class="bv-b-sep">/</span>
				<span class="bv-b-current" aria-current="page"><?php echo esc_html( $title ); ?></span>
			</div>
		</div>
	</nav>

	<!-- 2. MAIN 2-COLUMN PRODUCT SECTION (COMPACT & BALANCED) -->
	<div class="bv-product-main-section">
		<div class="bv-container">
			<div id="product-<?php echo esc_attr( $pid ); ?>" <?php wc_product_class( 'bv-product-columns product', $product ); ?>>
				
				<!-- COLUMN 1: COMPACT GALLERY WITH VERTICAL THUMBNAILS (LEFT) -->
				<div class="bv-product-gallery">
					
					<!-- Vertical Thumbnails Strip (Left Edge) -->
					<?php if ( count( $all_img_ids ) > 1 ) : ?>
						<div class="bv-gallery-thumbs" id="bv-gallery-thumbs">
							<?php foreach ( $all_img_ids as $idx => $img_id ) : 
								$thumb_url = wp_get_attachment_image_url( $img_id, 'thumbnail' );
								$large_url = wp_get_attachment_image_url( $img_id, 'large' );
								$is_active = ( 0 === $idx );
							?>
								<button type="button" class="bv-thumb-item <?php echo $is_active ? 'is-active' : ''; ?>" data-large-src="<?php echo esc_url( $large_url ); ?>" aria-label="<?php echo esc_attr( sprintf( 'Ảnh số %d', $idx + 1 ) ); ?>">
									<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
								</button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<!-- Main Image Stage (Compact) -->
					<div class="bv-gallery-stage">
						<div class="bv-stage-inner">
							<img src="<?php echo esc_url( $main_img_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="bv-stage-img" id="bv-main-product-img">
						</div>
					</div>

				</div>

				<!-- COLUMN 2: BUY BOX & PRODUCT ACTIONS (RIGHT) -->
				<div class="bv-product-summary">
					
					<!-- Product Title -->
					<h1 class="bv-summary-title"><?php echo esc_html( $title ); ?></h1>

					<!-- SKU -->
					<div class="bv-summary-sku">
						<span>SKU: <?php echo esc_html( $sku ); ?></span>
					</div>

					<!-- Price -->
					<div class="bv-summary-price">
						<?php echo wp_kses_post( $price_html ); ?>
						<?php if ( $is_on_sale && $product->get_regular_price() > 0 && $product->get_sale_price() > 0 ) : 
							$pct = round( ( ( (float) $product->get_regular_price() - (float) $product->get_sale_price() ) / (float) $product->get_regular_price() ) * 100 );
							if ( $pct > 0 ) : ?>
								<span class="bv-price-save-tag">-<?php echo esc_html( $pct ); ?>%</span>
							<?php endif; ?>
						<?php endif; ?>
					</div>

					<!-- ADD TO CART & VARIATIONS FORM (QUANTITY & BUTTON ON SAME LINE) -->
					<div class="bv-summary-form-wrap">
						<?php if ( $is_in_stock ) : ?>
							<div class="bv-cart-actions-container">
								
								<!-- Stock Scarcity Status -->
								<div class="bv-stock-scarcity">
									<span>Còn lại <strong><?php echo esc_html( $stock_qty ); ?></strong> sản phẩm</span>
								</div>

								<!-- WooCommerce Cart Form (Quantity & Add to Cart on same line) -->
								<?php woocommerce_template_single_add_to_cart(); ?>
							</div>
						<?php else : ?>
							<div class="bv-out-of-stock-box">
								<p>Tác phẩm hiện đã hết trong mẻ nung này. Mời bạn liên hệ xưởng để đặt làm cho mẻ nung tiếp theo.</p>
							</div>
						<?php endif; ?>

						<!-- Zalo Consultation -->
						<div class="bv-zalo-consult-wrap">
							<a href="https://zalo.me/0363292271" target="_blank" rel="noopener" class="bv-btn-zalo-consult">
								Tư vấn trực tiếp qua Zalo: 0363 292 271 (Mr. Hướng)
							</a>
						</div>
					</div>

					<!-- INLINE PRODUCT DESCRIPTION (ANH CERAMICS STYLE) -->
					<div class="bv-summary-desc-block">
						<h3 class="bv-desc-heading">Mô tả</h3>
						<div class="bv-desc-body">
							<?php if ( $short_desc ) : ?>
								<?php echo wpautop( $short_desc ); ?>
							<?php else : ?>
								<p><?php echo esc_html( $title ); ?>, chế tác thủ công mộc mạc theo phong cách gốm Việt Nam đương đại.</p>
								<p><strong>[Vi] Thông tin sản phẩm:</strong><br>
								- Công dụng: thưởng trà, cà phê, trang trí không gian hoặc quà tặng.<br>
								- Nhiệt độ nung: 1.250°C đanh chắc, bền vĩnh cửu.<br>
								- Chế tác: Chuốt tay thủ công bàn xoay độc bản.</p>
							<?php endif; ?>
						</div>
					</div>

					<!-- STUDIO POLICY & GUARANTEE (COMPACT EDITORIAL) -->
					<div class="bv-guarantee-box">
						<div class="bv-g-item">
							<span class="bv-g-num">01</span>
							<div>
								<strong>Bảo hành bể vỡ 100%</strong>
								<p>Đổi mới ngay nếu có nứt vỡ trong quá trình vận chuyển toàn quốc.</p>
							</div>
						</div>
						<div class="bv-g-item">
							<span class="bv-g-num">02</span>
							<div>
								<strong>Men khoáng tự nhiên 1.250°C</strong>
								<p>An toàn thực phẩm, sử dụng an toàn trong lò vi sóng và máy rửa bát.</p>
							</div>
						</div>
						<div class="bv-g-item">
							<span class="bv-g-num">03</span>
							<div>
								<strong>Đổi trả trong 7 ngày</strong>
								<p>Hỗ trợ đổi miễn phí nếu tác phẩm không đúng kỳ vọng thẩm mỹ.</p>
							</div>
						</div>
					</div>

				</div>

			</div>
		</div>
	</div>

	<!-- 3. DETAILED PRODUCT INFORMATION (3 MỤC DƯỚI SẢN PHẨM) -->
	<section class="bv-product-tabs-section" id="bv-product-tabs">
		<div class="bv-container">
			<div class="bv-tabs-wrapper">
				
				<!-- Tabs Navigation -->
				<div class="bv-tabs-nav" role="tablist">
					<button type="button" class="bv-tab-btn is-active" data-tab="tab-desc" role="tab" aria-selected="true">
						Chi Tiết Tác Phẩm &amp; Kích Thước
					</button>
					<button type="button" class="bv-tab-btn" data-tab="tab-usage" role="tab" aria-selected="false">
						Hướng Dẫn Sử Dụng &amp; Bảo Quản
					</button>
					<button type="button" class="bv-tab-btn" data-tab="tab-reviews" role="tab" aria-selected="false">
						Đánh Giá Từ Khách Hàng (18)
					</button>
				</div>

				<!-- Tab 1: Chi Tiết & Thông Số Kỹ Thuật -->
				<div class="bv-tab-content is-active" id="tab-desc" role="tabpanel">
					<div class="bv-tab-prose">
						<?php if ( $desc ) : ?>
							<?php echo wpautop( $desc ); ?>
						<?php elseif ( $short_desc ) : ?>
							<?php echo wpautop( $short_desc ); ?>
						<?php else : ?>
							<p>Tác phẩm gốm mộc được vuốt nặn thủ công trên bàn xoay truyền thống từ đất sét phù sa sông Hồng tinh lọc. Nung củi lớn 1.250°C tạo nên độ đanh chắc vĩnh cửu và sắc men mộc tự nhiên mang đậm triết lý Wabi-Sabi.</p>
						<?php endif; ?>

						<!-- Specifications Table -->
						<div class="bv-specs-card">
							<h4 class="bv-specs-title">Thông Số Kỹ Thuật Tác Phẩm</h4>
							<dl class="bv-specs-list">
								<div class="bv-spec-row">
									<dt>Chất liệu cốt gốm</dt>
									<dd>Đất sét tự nhiên phù sa sông Hồng tinh tuyển</dd>
								</div>
								<div class="bv-spec-row">
									<dt>Nước men chế tác</dt>
									<dd>Men tro trấu &amp; khoáng tự nhiên, hoàn toàn không chì</dd>
								</div>
								<div class="bv-spec-row">
									<dt>Nhiệt độ nung</dt>
									<dd>1.250°C (Nung nhiệt cao đanh bền, không thấm ố)</dd>
								</div>
								<div class="bv-spec-row">
									<dt>Phương pháp chế tác</dt>
									<dd>Chuốt tay thủ công bàn xoay độc bản</dd>
								</div>
								<div class="bv-spec-row">
									<dt>Xuất xứ xưởng</dt>
									<dd>Xưởng Ceramic Studio (Thôn 1 Giang Cao, Bát Tràng, Hà Nội)</dd>
								</div>
							</dl>
						</div>
					</div>
				</div>

				<!-- Tab 2: Hướng Dẫn Sử Dụng & Bảo Quản -->
				<div class="bv-tab-content" id="tab-usage" role="tabpanel">
					<div class="bv-tab-prose">
						<div class="bv-care-grid">
							<div class="bv-care-card">
								<h4>Lò vi sóng &amp; Lò nướng</h4>
								<p>Sản phẩm nung 1.250°C có khả năng chịu nhiệt cao bền bỉ, an toàn tuyệt đối khi dùng để hâm nóng đồ ăn trong lò vi sóng.</p>
							</div>
							<div class="bv-care-card">
								<h4>Vệ sinh &amp; Máy rửa bát</h4>
								<p>Sử dụng được trong máy rửa bát. Với các vết ố trà hoặc cà phê lâu ngày, chỉ cần dùng chút baking soda hoặc chanh chà nhẹ là sạch bóng.</p>
							</div>
							<div class="bv-care-card">
								<h4>Tránh sốc nhiệt đột ngột</h4>
								<p>Không nên chuyển đồ gốm đột ngột từ ngăn đông tủ lạnh vào ngay lò nướng nhiệt cao hoặc nước sôi 100°C.</p>
							</div>
						</div>
					</div>
				</div>

				<!-- Tab 3: Đánh Giá Khách Hàng -->
				<div class="bv-tab-content" id="tab-reviews" role="tabpanel">
					<div class="bv-reviews-container">
						<?php
						if ( comments_open() || pings_open() ) {
							comments_template();
						} else {
							echo '<div class="bv-no-reviews-note"><p>Mục nhận xét và đánh giá cho sản phẩm thủ công này.</p></div>';
						}
						?>
					</div>
				</div>

			</div>
		</div>
	</section>

	<!-- 4. RELATED PRODUCTS (GỢI Ý TỪ XƯỞNG - BÉ LẠI & TINH GỌN) -->
	<?php
	$related_args = [
		'post_type'      => 'product',
		'posts_per_page' => 4,
		'post__not_in'   => [ $pid ],
		'orderby'        => 'rand',
	];
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
		$related_args['tax_query'] = [
			[
				'taxonomy' => 'product_cat',
				'field'    => 'term_id',
				'terms'    => $terms[0]->term_id,
			],
		];
	}
	$related_query = new WP_Query( $related_args );
	if ( $related_query->have_posts() ) :
	?>
		<section class="bv-related-section">
			<div class="bv-container bv-container--compact">
				<div class="bv-related-header">
					<span class="bv-sub-tag">GỢI Ý TỪ XƯỞNG</span>
					<h2 class="bv-related-title">Tác Phẩm Cùng Bộ Sưu Tập</h2>
				</div>
				<div class="bv-ceramic-grid bv-related-grid">
					<?php while ( $related_query->have_posts() ) : $related_query->the_post(); 
						$r_prod = wc_get_product( get_the_ID() );
						if ( ! $r_prod || $r_prod->is_virtual() ) continue;
						$r_img = wp_get_attachment_image_url( $r_prod->get_image_id(), 'woocommerce_thumbnail' ) ?: home_url( '/wp-content/uploads/woocommerce-placeholder.webp' );
						$r_gallery = $r_prod->get_gallery_image_ids();
						$r_hover = ! empty( $r_gallery ) ? wp_get_attachment_image_url( $r_gallery[0], 'woocommerce_thumbnail' ) : '';
					?>
						<article class="bv-product-card bv-product-card--compact <?php echo $r_hover ? 'has-hover-image' : ''; ?>">
							<div class="bv-card-media">
								<a href="<?php echo esc_url( $r_prod->get_permalink() ); ?>" class="bv-card-img-link">
									<img src="<?php echo esc_url( $r_img ); ?>" alt="<?php echo esc_attr( $r_prod->get_name() ); ?>" class="bv-card-img bv-card-img--primary" loading="lazy">
									<?php if ( $r_hover ) : ?>
										<img src="<?php echo esc_url( $r_hover ); ?>" alt="<?php echo esc_attr( $r_prod->get_name() ); ?>" class="bv-card-img bv-card-img--hover" loading="lazy">
									<?php endif; ?>
								</a>
							</div>
							<div class="bv-card-body">
								<div class="bv-card-meta-line">
									<span>Gốm Mộc Thủ Công</span>
									<span class="bv-pt-star-pill">★ 5.0</span>
								</div>
								<h3 class="bv-card-title">
									<a href="<?php echo esc_url( $r_prod->get_permalink() ); ?>">
										<?php echo esc_html( $r_prod->get_name() ); ?>
									</a>
								</h3>
								<div class="bv-card-price-bar">
									<div class="bv-card-price">
										<?php echo wp_kses_post( $r_prod->get_price_html() ); ?>
									</div>
								</div>
							</div>
						</article>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

</div>

<?php endwhile; ?>
