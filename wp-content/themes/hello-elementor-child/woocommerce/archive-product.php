<?php
/**
 * The Template for displaying product archives (Shop / Collections).
 * Style: Luxury Ceramic Minimalism (No Icons / Pure Typography & Craft)
 * Inspired by Anh Ceramics.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shop_url = wc_get_page_permalink( 'shop' );
$current_term = is_product_category() ? get_queried_object() : null;
$total_products = wp_count_posts( 'product' )->publish;

// Fetch all product categories with products
$categories = get_terms( [
	'taxonomy'   => 'product_cat',
	'hide_empty' => true,
	'exclude'    => get_option( 'default_product_cat' ),
] );

// Current sorting
$orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'menu_order';
?>

<div class="bv-shop-archive-page">
	
	<!-- 1. BREADCRUMB (NO NUMBERS - CLEAN TRAIL) -->
	<nav class="bv-archive-breadcrumb" aria-label="Đường dẫn">
		<div class="bv-container">
			<div class="bv-breadcrumb-trail">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Trang chủ</a>
				<span class="bv-b-sep">/</span>
				<?php if ( $current_term ) : ?>
					<a href="<?php echo esc_url( $shop_url ); ?>">Cửa hàng gốm</a>
					<span class="bv-b-sep">/</span>
					<span class="bv-b-current" aria-current="page"><?php echo esc_html( $current_term->name ); ?></span>
				<?php else : ?>
					<span class="bv-b-current" aria-current="page">Tất cả tác phẩm gốm</span>
				<?php endif; ?>
			</div>
		</div>
	</nav>

	<!-- 2. COLLECTION HERO BANNER -->
	<header class="bv-collection-hero">
		<div class="bv-container">
			<div class="bv-hero-text-wrap">
				<span class="bv-collection-eyebrow">CERAMIC STUDIO • BỘ SƯU TẬP</span>
				<h1 class="bv-collection-title">
					<?php
					if ( $current_term ) {
						echo esc_html( $current_term->name );
					} else {
						echo 'Bộ Sưu Tập Gốm Mộc';
					}
					?>
				</h1>
				<p class="bv-collection-desc">
					<?php
					if ( $current_term && $current_term->description ) {
						echo esc_html( $current_term->description );
					} else {
						echo 'Mỗi tác phẩm là độc bản được vuốt nặn thủ công, nung củi lớn 1.250°C và mang dấu ấn mộc mạc Wabi-Sabi tự nhiên.';
					}
					?>
				</p>
			</div>

			<!-- 3. CATEGORY PILLS BAR (ANH CERAMICS STYLE) -->
			<div class="bv-category-pills-bar">
				<div class="bv-pills-scroll">
					<a href="<?php echo esc_url( $shop_url ); ?>" class="bv-pill <?php echo ! $current_term ? 'is-active' : ''; ?>">
						<span>Tất cả tác phẩm</span>
						<small class="bv-pill-count">(<?php echo esc_html( $total_products ); ?>)</small>
					</a>
					<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
						<?php foreach ( $categories as $cat ) : 
							$is_active = ( $current_term && $current_term->term_id === $cat->term_id );
						?>
							<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="bv-pill <?php echo $is_active ? 'is-active' : ''; ?>">
								<span><?php echo esc_html( $cat->name ); ?></span>
								<small class="bv-pill-count">(<?php echo esc_html( $cat->count ); ?>)</small>
							</a>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</header>

	<!-- 4. TOOLBAR: COUNT & SORT -->
	<div class="bv-shop-toolbar">
		<div class="bv-container">
			<div class="bv-toolbar-inner">
				<div class="bv-toolbar-count">
					<?php
					global $wp_query;
					$found = $wp_query->found_posts;
					printf( 'Hiển thị <strong>%d</strong> tác phẩm gốm', esc_html( $found ) );
					?>
				</div>
				<div class="bv-toolbar-sort">
					<form class="bv-sort-form" method="get">
						<label for="bv-orderby" class="bv-sort-label">Sắp xếp:</label>
						<select name="orderby" id="bv-orderby" class="bv-sort-select" onchange="this.form.submit()">
							<option value="menu_order" <?php selected( $orderby, 'menu_order' ); ?>>Mặc định xưởng gốm</option>
							<option value="date" <?php selected( $orderby, 'date' ); ?>>Mới ra lò</option>
							<option value="price" <?php selected( $orderby, 'price' ); ?>>Giá: Thấp đến cao</option>
							<option value="price-desc" <?php selected( $orderby, 'price-desc' ); ?>>Giá: Cao đến thấp</option>
							<option value="popularity" <?php selected( $orderby, 'popularity' ); ?>>Bán chạy nhất</option>
						</select>
						<?php
						foreach ( $_GET as $key => $val ) {
							if ( 'orderby' !== $key && 'submit' !== $key ) {
								echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '">';
							}
						}
						?>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- 5. CERAMIC PRODUCT GRID (4 COLUMNS - ANH CERAMICS) -->
	<div class="bv-shop-content">
		<div class="bv-container">
			<?php if ( have_posts() ) : ?>
				<div class="bv-ceramic-grid">
					<?php while ( have_posts() ) : the_post(); 
						global $product;
						if ( ! is_a( $product, 'WC_Product' ) ) {
							continue;
						}
						
						$pid          = $product->get_id();
						$title        = $product->get_name();
						$permalink    = $product->get_permalink();
						$price_html   = $product->get_price_html();
						$is_in_stock  = $product->is_in_stock();
						
						// Images: Primary + Hover Flip
						$image_id     = $product->get_image_id();
						$primary_img  = wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) ?: home_url( '/wp-content/uploads/woocommerce-placeholder.webp' );
						$gallery_ids  = $product->get_gallery_image_ids();
						$hover_img    = ! empty( $gallery_ids ) ? wp_get_attachment_image_url( $gallery_ids[0], 'woocommerce_thumbnail' ) : '';
						$has_hover    = ! empty( $hover_img );
					?>
						<article class="bv-product-card <?php echo $has_hover ? 'has-hover-image' : ''; ?>" data-product-id="<?php echo esc_attr( $pid ); ?>">
							
							<!-- Product Image Box with Hover Flip -->
							<div class="bv-card-media">
								<a href="<?php echo esc_url( $permalink ); ?>" class="bv-card-img-link" tabindex="-1">
									<img src="<?php echo esc_url( $primary_img ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="bv-card-img bv-card-img--primary" loading="lazy">
									<?php if ( $has_hover ) : ?>
										<img src="<?php echo esc_url( $hover_img ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="bv-card-img bv-card-img--hover" loading="lazy">
									<?php endif; ?>
								</a>

								<!-- Quick Actions overlay on hover -->
								<div class="bv-card-actions">
									<?php if ( $is_in_stock && $product->is_type( 'simple' ) ) : ?>
										<a href="<?php echo esc_url( $product->add_to_cart_url() ); ?>" data-product_id="<?php echo esc_attr( $pid ); ?>" class="bv-btn-quick-cart ajax_add_to_cart add_to_cart_button">
											Thêm vào giỏ
										</a>
									<?php else : ?>
										<a href="<?php echo esc_url( $permalink ); ?>" class="bv-btn-quick-cart bv-btn-quick-cart--view">
											Xem chi tiết
										</a>
									<?php endif; ?>
								</div>
							</div>

							<!-- Product Info Body -->
							<div class="bv-card-body">
								<div class="bv-card-meta-line">Gốm Mộc Thủ Công</div>

								<!-- Title -->
								<h3 class="bv-card-title">
									<a href="<?php echo esc_url( $permalink ); ?>">
										<?php echo esc_html( $title ); ?>
									</a>
								</h3>

								<!-- Price -->
								<div class="bv-card-price">
									<?php echo wp_kses_post( $price_html ); ?>
								</div>
							</div>

						</article>
					<?php endwhile; ?>
				</div>

				<!-- 6. PAGINATION -->
				<div class="bv-shop-pagination">
					<?php
					the_posts_pagination( [
						'mid_size'  => 2,
						'prev_text' => '← Trước',
						'next_text' => 'Sau →',
					] );
					?>
				</div>

			<?php else : ?>
				<div class="bv-no-products">
					<h3>Chưa có sản phẩm nào trong mẻ nung này</h3>
					<p>Các nghệ nhân đang tiếp tục hoàn thiện các tác phẩm gốm mộc mới. Mời bạn quay lại sau hoặc khám phá toàn bộ bộ sưu tập.</p>
					<a href="<?php echo esc_url( $shop_url ); ?>" class="bv-btn-primary">Xem tất cả tác phẩm</a>
				</div>
			<?php endif; ?>
		</div>
	</div>

</div>
