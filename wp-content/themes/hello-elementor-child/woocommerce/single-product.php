<?php
/**
 * The Template for displaying single product detail.
 * Inspired by Anh Ceramics (Wabi-Sabi Craft Aesthetic).
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
	
	<!-- 1. BREADCRUMB -->
	<nav class="bv-product-breadcrumb" aria-label="Đường dẫn">
		<div class="bv-container">
			<ol class="bv-breadcrumb-list">
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Trang chủ</a></li>
				<li class="bv-b-sep">/</li>
				<li><a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Cửa hàng gốm</a></li>
				<li class="bv-b-sep">/</li>
				<li><a href="<?php echo esc_url( $cat_link ); ?>"><?php echo esc_html( $cat_name ); ?></a></li>
				<li class="bv-b-sep">/</li>
				<li aria-current="page"><?php echo esc_html( $title ); ?></li>
			</ol>
		</div>
	</nav>

	<!-- 2. MAIN 2-COLUMN PRODUCT SECTION -->
	<div class="bv-product-main-section">
		<div class="bv-container">
			<div class="bv-product-columns">
				
				<!-- COLUMN 1: PRODUCT GALLERY (LEFT - 55%) -->
				<div class="bv-product-gallery">
					<div class="bv-gallery-stage">
						<div class="bv-stage-inner" id="bv-gallery-stage">
							<img src="<?php echo esc_url( $main_img_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="bv-stage-img" id="bv-main-product-img">
							
							<div class="bv-stage-badges">
								<span class="bv-sbadge">✦ Nung củi 1.250°C</span>
								<span class="bv-sbadge">Men tro tự nhiên</span>
							</div>

							<button type="button" class="bv-stage-zoom-btn" id="bv-zoom-btn" aria-label="Xem ảnh phóng to" title="Phóng to ảnh">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
							</button>
						</div>
					</div>

					<!-- Thumbnails Strip (Anh Ceramics Style) -->
					<?php if ( count( $all_img_ids ) > 1 ) : ?>
						<div class="bv-gallery-thumbs" id="bv-gallery-thumbs">
							<?php foreach ( $all_img_ids as $idx => $img_id ) : 
								$thumb_url = wp_get_attachment_image_url( $img_id, 'thumbnail' );
								$large_url = wp_get_attachment_image_url( $img_id, 'large' );
								$is_active = ( 0 === $idx );
							?>
								<button type="button" class="bv-thumb-item <?php echo $is_active ? 'is-active' : ''; ?>" data-large-src="<?php echo esc_url( $large_url ); ?>" aria-label="<?php echo esc_attr( sprintf( 'Xem ảnh số %d', $idx + 1 ) ); ?>">
									<img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
								</button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>

				<!-- COLUMN 2: BUY BOX & PRODUCT DETAILS (RIGHT - 45%) -->
				<div class="bv-product-summary">
					
					<!-- Category Tag -->
					<div class="bv-summary-category">
						<a href="<?php echo esc_url( $cat_link ); ?>"><?php echo esc_html( $cat_name ); ?></a>
					</div>

					<!-- Product Title -->
					<h1 class="bv-summary-title"><?php echo esc_html( $title ); ?></h1>

					<!-- Rating & Orders Meta -->
					<div class="bv-summary-rating-row">
						<div class="bv-stars-group">
							<span class="bv-star">★</span>
							<span class="bv-star">★</span>
							<span class="bv-star">★</span>
							<span class="bv-star">★</span>
							<span class="bv-star">★</span>
							<strong>5.0</strong>
						</div>
						<span class="bv-meta-sep">•</span>
						<a href="#bv-reviews-tab" class="bv-rating-count-link">18 lượt đánh giá mộc</a>
						<span class="bv-meta-sep">•</span>
						<span class="bv-craft-certified">✓ Thành phẩm ra lò</span>
					</div>

					<!-- Price -->
					<div class="bv-summary-price">
						<?php echo wp_kses_post( $price_html ); ?>
					</div>

					<!-- Stock Scarcity Status -->
					<div class="bv-stock-scarcity">
						<div class="bv-scarcity-text">
							<span class="bv-pulse-dot" aria-hidden="true"></span>
							<span>Chỉ còn lại <strong>2 - 3 chiếc độc bản</strong> trong mẻ nung này</span>
						</div>
						<div class="bv-scarcity-bar" aria-hidden="true">
							<div class="bv-scarcity-fill" style="width: 25%;"></div>
						</div>
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
									<!-- Quantity Input with +/- buttons -->
									<div class="bv-quantity-box">
										<button type="button" class="bv-qty-btn bv-qty-minus" aria-label="Giảm số lượng">-</button>
										<input type="number" id="bv-quantity" class="bv-qty-input input-text qty text" step="1" min="1" max="10" name="quantity" value="1" title="Số lượng" size="4" inputmode="numeric">
										<button type="button" class="bv-qty-btn bv-qty-plus" aria-label="Tăng số lượng">+</button>
									</div>

									<!-- Add to Cart Button -->
									<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $pid ); ?>" class="bv-btn-add-to-cart single_add_to_cart_button button alt">
										<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
										<span>Thêm Vào Giỏ Hàng</span>
									</button>
								</div>

								<!-- Buy Now (Direct Checkout) Button -->
								<div class="bv-buynow-wrap">
									<button type="button" class="bv-btn-buy-now" data-product-id="<?php echo esc_attr( $pid ); ?>" data-checkout-url="<?php echo esc_url( wc_get_checkout_url() ); ?>">
										<span>⚡ Mua Ngay (Thanh toán VietQR MBBank)</span>
									</button>
								</div>

							</form>
						<?php else : ?>
							<div class="bv-out-of-stock-box">
								<p>Tác phẩm hiện đã hết trong mẻ nung này. Mời bạn chọn sản phẩm khác hoặc nhắn Zalo để đặt hàng cho mẻ nung tiếp theo.</p>
							</div>
						<?php endif; ?>

						<!-- Zalo Consultation CTA Button -->
						<div class="bv-zalo-consult-wrap">
							<a href="https://zalo.me/0363292271" target="_blank" rel="noopener" class="bv-btn-zalo-consult">
								<span class="bv-zalo-icon">💬</span>
								<span>Nhắn Zalo tư vấn chi tiết tác phẩm này (Mr. Hướng)</span>
							</a>
						</div>
					</div>

					<!-- STUDIO GUARANTEE BOX (ANH CERAMICS TRUST) -->
					<div class="bv-guarantee-box">
						<div class="bv-g-item">
							<span class="bv-g-icon">📦</span>
							<div>
								<strong>Bảo hành bể vỡ 100%</strong>
								<p>Quay video khui kiện gốm, xưởng hoàn tiền hoặc gửi tác phẩm thay thế ngay nếu có bất kỳ nứt vỡ nào.</p>
							</div>
						</div>
						<div class="bv-g-item">
							<span class="bv-g-icon">🍃</span>
							<div>
								<strong>Men khoáng tự nhiên 100%</strong>
								<p>Nung lửa lớn 1.250°C, khử hoàn toàn kim loại nặng, an toàn tuyệt đối cho thực phẩm và lò vi sóng.</p>
							</div>
						</div>
						<div class="bv-g-item">
							<span class="bv-g-icon">🔄</span>
							<div>
								<strong>Hỗ trợ đổi trả trong 7 ngày</strong>
								<p>Đổi miễn phí nếu tác phẩm không đúng hình ảnh hoặc kỳ vọng thẩm mỹ của bạn.</p>
							</div>
						</div>
						<div class="bv-g-item">
							<span class="bv-g-icon">🚚</span>
							<div>
								<strong>Giao hàng an toàn toàn quốc</strong>
								<p>Đóng gói chống sốc 3 lớp chuyên dụng bằng xốp và giấy carton mộc thân thiện môi trường.</p>
							</div>
						</div>
					</div>

				</div>

			</div>
		</div>
	</div>

	<!-- 3. DETAILED PRODUCT INFORMATION TABS / ACCORDION -->
	<section class="bv-product-tabs-section" id="bv-product-tabs">
		<div class="bv-container">
			<div class="bv-tabs-wrapper">
				
				<!-- Tabs Nav -->
				<div class="bv-tabs-nav" role="tablist">
					<button type="button" class="bv-tab-btn is-active" data-tab="tab-desc" role="tab" aria-selected="true">
						Chi Tiết Tác Phẩm & Kích Thước
					</button>
					<button type="button" class="bv-tab-btn" data-tab="tab-usage" role="tab" aria-selected="false">
						Hướng Dẫn Sử Dụng & Bảo Quản
					</button>
					<button type="button" class="bv-tab-btn" data-tab="tab-reviews" id="bv-reviews-tab" role="tab" aria-selected="false">
						Đánh Giá Khách Hàng (<?php echo esc_html( $product->get_review_count() ?: 5 ); ?>)
					</button>
				</div>

				<!-- Tab 1: Description & Specs -->
				<div class="bv-tab-content is-active" id="tab-desc" role="tabpanel">
					<div class="bv-tab-prose">
						<?php if ( $desc ) : ?>
							<?php echo wpautop( $desc ); ?>
						<?php else : ?>
							<p>Tác phẩm gốm mộc được tạo hình hoàn toàn thủ công trên bàn xoay truyền thống từ bàn tay người thợ làng gốm. Mỗi đường vân, vệt loang ngẫu hứng của men tro và sắc nung 1.250°C mang theo linh hồn độc bản, không sản phẩm nào giống hệt sản phẩm nào.</p>
						<?php endif; ?>

						<!-- Specifications Table -->
						<div class="bv-specs-card">
							<h4 class="bv-specs-title">Thông Số Kỹ Thuật Tác Phẩm</h4>
							<dl class="bv-specs-list">
								<div class="bv-spec-row">
									<dt>Chất liệu đất</dt>
									<dd>Đất sét phù sa sông Hồng tinh lọc, ủ tự nhiên</dd>
								</div>
								<div class="bv-spec-row">
									<dt>Nước men</dt>
									<dd>Men tro trấu & khoáng thiên nhiên, không chì</dd>
								</div>
								<div class="bv-spec-row">
									<dt>Nhiệt độ lò nung</dt>
									<dd>1.250°C (Nung lửa bền vĩnh cửu, độ đanh như đá)</dd>
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

				<!-- Tab 2: Usage & Care -->
				<div class="bv-tab-content" id="tab-usage" role="tabpanel">
					<div class="bv-tab-prose">
						<div class="bv-care-grid">
							<div class="bv-care-card">
								<span class="bv-care-icon">♨️</span>
								<h4>Lò vi sóng & Lò nướng</h4>
								<p>Sản phẩm đã được nung ở 1.250°C nên chịu nhiệt hoàn hảo. Dùng an toàn trong lò vi sóng để hâm nóng đồ ăn.</p>
							</div>
							<div class="bv-care-card">
								<span class="bv-care-icon">🧼</span>
								<h4>Vệ sinh & Máy rửa bát</h4>
								<p>An toàn tuyệt đối với máy rửa bát. Với vết ố trà hoặc cà phê lâu ngày, bạn chỉ cần dùng baking soda hoặc chanh chà nhẹ là sạch bóng.</p>
							</div>
							<div class="bv-care-card">
								<span class="bv-care-icon">❄️</span>
								<h4>Tránh sốc nhiệt đột ngột</h4>
								<p>Không chuyển gốm trực tiếp từ ngăn đông tủ lạnh sang lò nướng hoặc đổ ngay nước sôi sùng sục khi gốm đang lạnh băng.</p>
							</div>
						</div>
					</div>
				</div>

				<!-- Tab 3: Reviews with Photos -->
				<div class="bv-tab-content" id="tab-reviews" role="tabpanel">
					<div class="bv-reviews-container">
						<?php
						// Output WooCommerce comments / reviews template
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

	<!-- 4. RELATED PRODUCTS (4 CARDS - ANH CERAMICS) -->
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
								<div class="bv-card-badges">
									<span class="bv-badge bv-badge--craft">✦ Độc bản</span>
								</div>
							</div>
							<div class="bv-card-body">
								<div class="bv-card-stars">
									<span class="bv-star">★</span><span class="bv-star">★</span><span class="bv-star">★</span><span class="bv-star">★</span><span class="bv-star">★</span>
									<span class="bv-rating-note">(5.0)</span>
								</div>
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

<!-- LIGHTBOX MODAL FOR PRODUCT IMAGE ZOOM -->
<div id="bv-image-modal" class="bv-image-modal" aria-hidden="true" tabindex="-1">
	<div class="bv-modal-overlay"></div>
	<div class="bv-modal-content">
		<button type="button" class="bv-modal-close" id="bv-modal-close" aria-label="Đóng ảnh lớn">&times;</button>
		<img src="" alt="" id="bv-modal-img" class="bv-modal-img">
	</div>
</div>

<?php endwhile; ?>
