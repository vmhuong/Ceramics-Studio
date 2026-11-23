<?php
/**
 * The Template for displaying single product detail.
 * Style: Anh Ceramics Minimalist Craft (Vertical Thumbnail Gallery + Clean Buy Box & Inline Mô tả)
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

	<!-- 2. MAIN 2-COLUMN PRODUCT SECTION -->
	<div class="bv-product-main-section">
		<div class="bv-container">
			<div class="bv-product-columns">
				
				<!-- COLUMN 1: PRODUCT GALLERY WITH VERTICAL THUMBNAILS (LEFT - ANH CERAMICS) -->
				<div class="bv-product-gallery">
					
					<!-- Vertical Thumbnails Column (Left Edge) -->
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

					<!-- Main Image Stage (Right of Thumbnails) -->
					<div class="bv-gallery-stage">
						<div class="bv-stage-inner">
							<img src="<?php echo esc_url( $main_img_url ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="bv-stage-img" id="bv-main-product-img">
						</div>
					</div>

				</div>

				<!-- COLUMN 2: BUY BOX & PRODUCT DETAILS (RIGHT - ANH CERAMICS) -->
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

					<!-- ADD TO CART & VARIATIONS FORM (WOOCOMMERCE INTEGRATED) -->
					<div class="bv-summary-form-wrap">
						<?php if ( $is_in_stock ) : ?>
							<div class="bv-cart-actions-container">
								
								<!-- Stock Scarcity Status -->
								<div class="bv-stock-scarcity">
									<span>Còn lại <strong><?php echo esc_html( $stock_qty ); ?></strong> sản phẩm</span>
								</div>

								<?php woocommerce_template_single_add_to_cart(); ?>
								
								<!-- Buy Now (Direct Checkout) -->
								<div class="bv-buynow-wrap">
									<button type="button" class="bv-btn-buy-now" data-product-id="<?php echo esc_attr( $pid ); ?>" data-checkout-url="<?php echo esc_url( wc_get_checkout_url() ); ?>">
										Mua Ngay (Thanh toán VietQR)
									</button>
								</div>
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

					<!-- INLINE "MÔ TẢ" SECTION (ANH CERAMICS SIGNATURE) -->
					<div class="bv-summary-desc-block">
						<h3 class="bv-desc-heading">Mô tả</h3>
						<div class="bv-desc-body">
							<?php if ( $desc ) : ?>
								<?php echo wpautop( $desc ); ?>
							<?php elseif ( $short_desc ) : ?>
								<?php echo wpautop( $short_desc ); ?>
							<?php else : ?>
								<p>Tác phẩm gốm mộc độc bản từ đất sét phù sa sông Hồng tinh lọc, vuốt nặn thủ công trên bàn xoay truyền thống và nung củi lớn 1.250°C.</p>
								<p><strong>[Vi] Thông tin sản phẩm:</strong><br>
								• Công dụng: Sử dụng thưởng trà, cà phê, trang trí không gian bàn ăn ấm cúng.<br>
								• Chất liệu: Đất sét tự nhiên &amp; men khoáng tro trấu không chì.<br>
								• Nung nhiệt: 1.250°C bền vĩnh cửu, an toàn lò vi sóng &amp; máy rửa bát.</p>
							<?php endif; ?>
						</div>
					</div>

					<!-- STUDIO POLICY & GUARANTEE -->
					<div class="bv-guarantee-box">
						<div class="bv-g-item">
							<span class="bv-g-num">01</span>
							<div>
								<strong>Bảo hành bể vỡ 100%</strong>
								<p>Đổi mới ngay nếu nứt vỡ trong quá trình vận chuyển toàn quốc.</p>
							</div>
						</div>
						<div class="bv-g-item">
							<span class="bv-g-num">02</span>
							<div>
								<strong>Men khoáng an toàn 1.250°C</strong>
								<p>An toàn tuyệt đối cho thực phẩm, lò vi sóng và máy rửa bát.</p>
							</div>
						</div>
						<div class="bv-g-item">
							<span class="bv-g-num">03</span>
							<div>
								<strong>Đổi trả trong 7 ngày</strong>
								<p>Hỗ trợ đổi miễn phí nếu sản phẩm không đúng kỳ vọng thẩm mỹ.</p>
							</div>
						</div>
					</div>

				</div>

			</div>
		</div>
	</div>

	<!-- 3. RELATED PRODUCTS -->
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
				<div class="bv-ceramic-grid bv-shop-grid">
					<?php while ( $related_query->have_posts() ) : $related_query->the_post(); 
						$r_prod = wc_get_product( get_the_ID() );
						if ( ! $r_prod || $r_prod->is_virtual() ) continue;
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
								<div class="bv-card-price-bar">
									<div class="bv-card-price">
										<?php echo wp_kses_post( $r_prod->get_price_html() ); ?>
									</div>
									<span class="bv-pt-star-pill">★ 5.0</span>
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
