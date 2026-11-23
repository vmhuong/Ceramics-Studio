<?php
/**
 * The Template for displaying single product detail.
 * Style: Luxury Ceramic Minimalism (No Icons / Pure Typography & Craft)
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
	$price_html    = $product->get_price_html();
	$short_desc    = $product->get_short_description();
	$desc          = get_the_content();
	$is_in_stock   = $product->is_in_stock();
	$stock_qty     = $product->get_stock_quantity();
	$is_on_sale    = $product->is_on_sale();
	
	// Categories
	$terms = get_the_terms( $pid, 'product_cat' );
	$cat_name = ! empty( $terms ) && ! is_wp_error( $terms ) ? $terms[0]->name : 'Gốm Thủ Công';
	$cat_link = ! empty( $terms ) && ! is_wp_error( $terms ) ? get_term_link( $terms[0] ) : wc_get_page_permalink( 'shop' );

	// Images: Main + Gallery
	$main_img_id  = $product->get_image_id();
	$main_img_url = wp_get_attachment_image_url( $main_img_id, 'large' ) ?: home_url( '/wp-content/uploads/woocommerce-placeholder.webp' );
	$gallery_ids  = $product->get_gallery_image_ids();
	$all_img_ids  = array_filter( array_merge( [ $main_img_id ], $gallery_ids ) );
?>

<div class="bv-single-product-page">
	
	<!-- 1. BREADCRUMB (NO ORDERED LIST NUMBERS - CLEAN TRAIL) -->
	<nav class="bv-product-breadcrumb" aria-label="Đường dẫn">
		<div class="bv-container">
			<div class="bv-breadcrumb-trail">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Trang chủ</a>
				<span class="bv-b-sep">/</span>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Cửa hàng gốm</a>
				<span class="bv-b-sep">/</span>
				<a href="<?php echo esc_url( $cat_link ); ?>"><?php echo esc_html( $cat_name ); ?></a>
				<span class="bv-b-sep">/</span>
				<span class="bv-b-current" aria-current="page"><?php echo esc_html( $title ); ?></span>
			</div>
		</div>
	</nav>

	<!-- 2. MAIN 2-COLUMN PRODUCT SECTION -->
	<div class="bv-product-main-section">
		<div class="bv-container">
			<div class="bv-product-columns">
				
				<!-- COLUMN 1: PRODUCT GALLERY (LEFT) -->
				<div class="bv-product-gallery">
					<div class="bv-gallery-stage">
						<div class="bv-stage-inner">
							<img src="<?php echo esc_url( $main_img_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="bv-stage-img" id="bv-main-product-img">
						</div>
					</div>

					<!-- Thumbnails Strip -->
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
				</div>

				<!-- COLUMN 2: BUY BOX & PRODUCT DETAILS (RIGHT) -->
				<div class="bv-product-summary">
					
					<!-- Category Tag -->
					<div class="bv-summary-category">
						<a href="<?php echo esc_url( $cat_link ); ?>"><?php echo esc_html( $cat_name ); ?></a>
					</div>

					<!-- Product Title -->
					<h1 class="bv-summary-title"><?php echo esc_html( $title ); ?></h1>

					<!-- Rating & Verification Meta (No cartoon stars) -->
					<div class="bv-summary-rating-row">
						<span class="bv-meta-rating"><span class="bv-star">★</span> 5.0</span>
						<span class="bv-meta-sep">•</span>
						<a href="#bv-product-tabs" class="bv-rating-count-link">18 nhận xét từ người dùng</a>
						<span class="bv-meta-sep">•</span>
						<span class="bv-craft-certified">Thành phẩm xưởng gốm</span>
					</div>

					<!-- Price -->
					<div class="bv-summary-price">
						<?php echo wp_kses_post( $price_html ); ?>
						<?php if ( $is_on_sale && $product->get_regular_price() > 0 && $product->get_sale_price() > 0 ) : 
							$pct = round( ( ( (float) $product->get_regular_price() - (float) $product->get_sale_price() ) / (float) $product->get_regular_price() ) * 100 );
							if ( $pct > 0 ) : ?>
								<span class="bv-price-save-tag">Tiết kiệm <?php echo esc_html( $pct ); ?>%</span>
							<?php endif; ?>
						<?php endif; ?>
					</div>

					<!-- Scarcity / Stock Status -->
					<div class="bv-stock-scarcity">
						<span>Tình trạng: <strong>Còn 2 sản phẩm</strong> trong mẻ nung này</span>
					</div>

					<!-- Short Description -->
					<?php if ( $short_desc ) : ?>
						<div class="bv-summary-excerpt">
							<?php echo wpautop( $short_desc ); ?>
						</div>
					<?php endif; ?>

					<!-- ADD TO CART & BUY NOW ACTIONS -->
					<div class="bv-summary-form-wrap">
						<?php if ( $is_in_stock ) : ?>
							<form class="cart bv-addcart-form" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype="multipart/form-data">
								
								<div class="bv-qty-and-cta">
									<!-- Quantity Input [-] 1 [+] -->
									<div class="bv-quantity-box">
										<button type="button" class="bv-qty-btn bv-qty-minus" aria-label="Giảm">-</button>
										<input type="number" id="bv-quantity" class="bv-qty-input" step="1" min="1" max="10" name="quantity" value="1" title="Số lượng">
										<button type="button" class="bv-qty-btn bv-qty-plus" aria-label="Tăng">+</button>
									</div>

									<!-- Add to Cart Button -->
									<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $pid ); ?>" class="bv-btn-add-to-cart">
										Thêm Vào Giỏ Hàng
									</button>
								</div>

								<!-- Buy Now (Direct Checkout) Button -->
								<div class="bv-buynow-wrap">
									<button type="button" class="bv-btn-buy-now" data-product-id="<?php echo esc_attr( $pid ); ?>" data-checkout-url="<?php echo esc_url( wc_get_checkout_url() ); ?>">
										Mua Ngay (Thanh toán VietQR)
									</button>
								</div>

							</form>
						<?php else : ?>
							<div class="bv-out-of-stock-box">
								<p>Tác phẩm hiện đã hết trong mẻ nung này. Mời bạn liên hệ xưởng để đặt làm cho mẻ nung tiếp theo.</p>
							</div>
						<?php endif; ?>

						<!-- Zalo Consultation Text Button -->
						<div class="bv-zalo-consult-wrap">
							<a href="https://zalo.me/0363292271" target="_blank" rel="noopener" class="bv-btn-zalo-consult">
								Tư vấn trực tiếp qua Zalo: 0363 292 271 (Mr. Hướng)
							</a>
						</div>
					</div>

					<!-- STUDIO POLICY & GUARANTEE (NO EMOJIS - EDITORIAL MINIMALISM) -->
					<div class="bv-guarantee-box">
						<div class="bv-g-item">
							<span class="bv-g-num">01</span>
							<div>
								<strong>Bảo hành bể vỡ 100%</strong>
								<p>Đổi mới hoặc hoàn tiền ngay nếu có bất kỳ nứt mẻ nào trong quá trình vận chuyển.</p>
							</div>
						</div>
						<div class="bv-g-item">
							<span class="bv-g-num">02</span>
							<div>
								<strong>Men khoáng tự nhiên 100%</strong>
								<p>Nung nhiệt 1.250°C, an toàn cho thực phẩm, lò vi sóng và máy rửa bát.</p>
							</div>
						</div>
						<div class="bv-g-item">
							<span class="bv-g-num">03</span>
							<div>
								<strong>Hỗ trợ đổi trả trong 7 ngày</strong>
								<p>Đổi miễn phí nếu sản phẩm không đúng hình ảnh hoặc kỳ vọng thẩm mỹ.</p>
							</div>
						</div>
						<div class="bv-g-item">
							<span class="bv-g-num">04</span>
							<div>
								<strong>Đóng gói xốp 3 lớp an toàn</strong>
								<p>Đóng gói chống sốc chuyên dụng cho đồ gốm thủ công, giao hàng toàn quốc.</p>
							</div>
						</div>
					</div>

				</div>

			</div>
		</div>
	</div>

	<!-- 3. DETAILED PRODUCT INFORMATION TABS -->
	<section class="bv-product-tabs-section" id="bv-product-tabs">
		<div class="bv-container">
			<div class="bv-tabs-wrapper">
				
				<!-- Tabs Nav -->
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

				<!-- Tab 1: Description & Specs -->
				<div class="bv-tab-content is-active" id="tab-desc" role="tabpanel">
					<div class="bv-tab-prose">
						<?php if ( $desc ) : ?>
							<?php echo wpautop( $desc ); ?>
						<?php else : ?>
							<p>Tác phẩm gốm mộc được tạo hình thủ công trên bàn xoay truyền thống từ bàn tay người thợ lành nghề. Mỗi đường vân, vệt loang ngẫu hứng của men tro và sắc nung 1.250°C mang theo dấu ấn độc bản riêng biệt.</p>
						<?php endif; ?>

						<!-- Specifications Table -->
						<div class="bv-specs-card">
							<h4 class="bv-specs-title">Thông Số Kỹ Thuật Tác Phẩm</h4>
							<dl class="bv-specs-list">
								<div class="bv-spec-row">
									<dt>Chất liệu đất</dt>
									<dd>Đất sét phù sa sông Hồng tinh lọc</dd>
								</div>
								<div class="bv-spec-row">
									<dt>Nước men</dt>
									<dd>Men tro trấu &amp; khoáng thiên nhiên, không chì</dd>
								</div>
								<div class="bv-spec-row">
									<dt>Nhiệt độ nung</dt>
									<dd>1.250°C (Độ đanh chắc, bền vĩnh cửu)</dd>
								</div>
								<div class="bv-spec-row">
									<dt>Phương pháp chế tác</dt>
									<dd>Chuốt tay thủ công bàn xoay độc bản</dd>
								</div>
								<div class="bv-spec-row">
									<dt>Xuất xứ</dt>
									<dd>Xưởng Ceramic Studio (Thôn 1 Giang Cao, Bát Tràng, Hà Nội)</dd>
								</div>
							</dl>
						</div>
					</div>
				</div>

				<!-- Tab 2: Usage & Care (No Emojis) -->
				<div class="bv-tab-content" id="tab-usage" role="tabpanel">
					<div class="bv-tab-prose">
						<div class="bv-care-grid">
							<div class="bv-care-card">
								<h4>Lò vi sóng &amp; Lò nướng</h4>
								<p>Sản phẩm nung 1.250°C chịu nhiệt rất tốt, sử dụng an toàn trong lò vi sóng để hâm nóng đồ ăn.</p>
							</div>
							<div class="bv-care-card">
								<h4>Vệ sinh &amp; Máy rửa bát</h4>
								<p>An toàn với máy rửa bát. Với vết ố trà hoặc cà phê, dùng baking soda hoặc chanh chà nhẹ là sạch bóng.</p>
							</div>
							<div class="bv-care-card">
								<h4>Tránh sốc nhiệt</h4>
								<p>Không chuyển gốm đột ngột từ ngăn đông tủ lạnh sang nước sôi hoặc lò nướng nhiệt cao.</p>
							</div>
						</div>
					</div>
				</div>

				<!-- Tab 3: Reviews -->
				<div class="bv-tab-content" id="tab-reviews" role="tabpanel">
					<div class="bv-reviews-container">
						<?php
						if ( comments_open() || pings_open() ) {
							comments_template();
						} else {
							echo '<div class="bv-no-reviews-note"><p>Đánh giá đã đóng cho sản phẩm này.</p></div>';
						}
						?>
					</div>
				</div>

			</div>
		</div>
	</section>

	<!-- 4. RELATED PRODUCTS -->
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
			<div class="bv-container">
				<div class="bv-related-header">
					<span class="bv-sub-tag">GỢI Ý TỪ XƯỞNG</span>
					<h2 class="bv-related-title">Tác Phẩm Cùng Bộ Sưu Tập</h2>
				</div>
				<div class="bv-ceramic-grid">
					<?php while ( $related_query->have_posts() ) : $related_query->the_post(); 
						$r_prod = wc_get_product( get_the_ID() );
						if ( ! $r_prod ) continue;
						$r_img = wp_get_attachment_image_url( $r_prod->get_image_id(), 'woocommerce_thumbnail' ) ?: home_url( '/wp-content/uploads/woocommerce-placeholder.webp' );
						$r_gallery = $r_prod->get_gallery_image_ids();
						$r_hover = ! empty( $r_gallery ) ? wp_get_attachment_image_url( $r_gallery[0], 'woocommerce_thumbnail' ) : '';
					?>
						<article class="bv-product-card <?php echo $r_hover ? 'has-hover-image' : ''; ?>">
							<div class="bv-card-media">
								<a href="<?php echo esc_url( $r_prod->get_permalink() ); ?>" class="bv-card-img-link">
									<img src="<?php echo esc_url( $r_img ); ?>" alt="<?php echo esc_attr( $r_prod->get_name() ); ?>" class="bv-card-img bv-card-img--primary" loading="lazy">
									<?php if ( $r_hover ) : ?>
										<img src="<?php echo esc_url( $r_hover ); ?>" alt="<?php echo esc_attr( $r_prod->get_name() ); ?>" class="bv-card-img bv-card-img--hover" loading="lazy">
									<?php endif; ?>
								</a>
							</div>
							<div class="bv-card-body">
								<div class="bv-card-meta-line">Gốm Mộc Thủ Công</div>
								<h3 class="bv-card-title">
									<a href="<?php echo esc_url( $r_prod->get_permalink() ); ?>">
										<?php echo esc_html( $r_prod->get_name() ); ?>
									</a>
								</h3>
								<div class="bv-card-price">
									<?php echo wp_kses_post( $r_prod->get_price_html() ); ?>
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
