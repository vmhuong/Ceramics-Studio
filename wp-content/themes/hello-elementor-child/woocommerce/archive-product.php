<?php
/**
 * The Template for displaying product archives (Shop / Collections).
 * Style: Anh Ceramics 2-Column Minimalist Layout (Filter Sidebar + 4-Column Grid)
 * Inspired by Anh Ceramics.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$shop_url     = wc_get_page_permalink( 'shop' );
$current_term = is_product_category() ? get_queried_object() : null;

// Determine current active filter states
$is_on_sale  = ! empty( $_GET['on_sale'] );
$is_featured = ! empty( $_GET['featured'] );
$cur_min     = isset( $_GET['min_price'] ) ? (float) $_GET['min_price'] : 0;
$cur_max     = isset( $_GET['max_price'] ) ? (float) $_GET['max_price'] : 0;
$orderby     = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : 'date';

// Determine page heading
$page_title = 'Tất cả sản phẩm';
if ( $is_on_sale ) {
	$page_title = 'Sản phẩm khuyến mãi';
} elseif ( $is_featured ) {
	$page_title = 'Sản phẩm nổi bật';
} elseif ( $current_term ) {
	$page_title = $current_term->name;
}

// Fetch all product categories with products
$categories = get_terms( [
	'taxonomy'   => 'product_cat',
	'hide_empty' => true,
	'exclude'    => get_option( 'default_product_cat' ),
] );

// Color swatches (Anh Ceramics Palette)
$glaze_colors = [
	[ 'name' => 'Mộc be cát',     'hex' => '#E8DFC8' ],
	[ 'name' => 'Men tro nhạt',    'hex' => '#D8C3A5' ],
	[ 'name' => 'Đất nung mộc',    'hex' => '#B88258' ],
	[ 'name' => 'Đất nung đậm',    'hex' => '#9B5B33' ],
	[ 'name' => 'Nâu sẫm nung',    'hex' => '#4A342B' ],
	[ 'name' => 'Xám men tro',     'hex' => '#3D4543' ],
	[ 'name' => 'Xanh ngọc sương', 'hex' => '#8CA39D' ],
	[ 'name' => 'Xanh sương nhạt', 'hex' => '#A8BDB8' ],
	[ 'name' => 'Nâu hạt dẻ',      'hex' => '#936037' ],
	[ 'name' => 'Đỏ gạch nung',    'hex' => '#8C3825' ],
	[ 'name' => 'Cam san hô',      'hex' => '#C4572E' ],
	[ 'name' => 'Hồng phấn gốm',   'hex' => '#CDB3A3' ],
	[ 'name' => 'Men rêu xanh',    'hex' => '#7B8C70' ],
	[ 'name' => 'Men rêu đậm',     'hex' => '#586851' ],
	[ 'name' => 'Men cát vàng',    'hex' => '#C99E4D' ],
	[ 'name' => 'Cam gốm chín',    'hex' => '#BE6929' ],
	[ 'name' => 'Xanh biển sâu',   'hex' => '#314E59' ],
	[ 'name' => 'Men nâu đen',     'hex' => '#332E2C' ],
	[ 'name' => 'Xanh ngọc biển',  'hex' => '#508985' ],
	[ 'name' => 'Men mộc ngà',     'hex' => '#9D9F8E' ],
];
?>

<div class="bv-shop-archive-page">
	
	<!-- 1. BREADCRUMB (CLEAN TRAIL) -->
	<nav class="bv-archive-breadcrumb" aria-label="Đường dẫn">
		<div class="bv-container">
			<div class="bv-breadcrumb-trail">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Trang chủ</a>
				<span class="bv-b-sep">/</span>
				<a href="<?php echo esc_url( $shop_url ); ?>">Sản phẩm</a>
				<span class="bv-b-sep">/</span>
				<span class="bv-b-current" aria-current="page"><?php echo esc_html( $page_title ); ?></span>
			</div>
		</div>
	</nav>

	<!-- 2. MAIN 2-COLUMN CATALOG CONTAINER -->
	<div class="bv-container">
		<div class="bv-shop-layout">
			
			<!-- ===============================================================
			     LEFT SIDEBAR FILTER (ANH CERAMICS SIGNATURE)
			     =============================================================== -->
			<aside class="bv-shop-sidebar" aria-label="Bộ lọc sản phẩm">
				
				<!-- Group 1: Collection Navigation Links -->
				<div class="bv-filter-group bv-filter-collections">
					<ul class="bv-filter-nav-list">
						<li class="bv-filter-nav-item <?php echo $is_on_sale ? 'is-active' : ''; ?>">
							<a href="<?php echo esc_url( add_query_arg( 'on_sale', '1', $shop_url ) ); ?>" class="bv-filter-nav-link">
								<span class="bv-filter-text-wrap">
									<strong class="bv-filter-title">Sản phẩm khuyến mãi</strong>
									<span class="bv-filter-sub">Discounted product</span>
								</span>
								<span class="bv-filter-icon">−</span>
							</a>
						</li>
						<li class="bv-filter-nav-item <?php echo $is_featured ? 'is-active' : ''; ?>">
							<a href="<?php echo esc_url( add_query_arg( 'featured', '1', $shop_url ) ); ?>" class="bv-filter-nav-link">
								<span class="bv-filter-text-wrap">
									<strong class="bv-filter-title">Sản phẩm nổi bật</strong>
								</span>
							</a>
						</li>
						<li class="bv-filter-nav-item <?php echo ( ! $is_on_sale && ! $is_featured && ! $current_term ) ? 'is-active' : ''; ?>">
							<a href="<?php echo esc_url( $shop_url ); ?>" class="bv-filter-nav-link">
								<span class="bv-filter-text-wrap">
									<strong class="bv-filter-title">Tất cả sản phẩm</strong>
								</span>
							</a>
						</li>
						<?php if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) : ?>
							<?php foreach ( $categories as $cat ) : 
								$is_cat_active = ( $current_term && $current_term->term_id === $cat->term_id );
							?>
								<li class="bv-filter-nav-item bv-filter-cat-item <?php echo $is_cat_active ? 'is-active' : ''; ?>">
									<a href="<?php echo esc_url( get_term_link( $cat ) ); ?>" class="bv-filter-nav-link">
										<span class="bv-filter-text-wrap">
											<span class="bv-filter-cat-name"><?php echo esc_html( $cat->name ); ?></span>
										</span>
									</a>
								</li>
							<?php endforeach; ?>
						<?php endif; ?>
					</ul>
				</div>

				<!-- Group 2: Price Filter Checkboxes -->
				<div class="bv-filter-group bv-filter-price">
					<div class="bv-filter-heading">
						<span class="bv-filter-heading-text">GIÁ SẢN PHẨM</span>
						<span class="bv-filter-heading-icon">−</span>
					</div>
					<ul class="bv-price-filter-list">
						<?php
						$price_ranges = [
							[ 'label' => 'Dưới 300,000₫',             'min' => 0,       'max' => 300000 ],
							[ 'label' => '300,000₫ - 500,000₫',       'min' => 300000,  'max' => 500000 ],
							[ 'label' => '500,000₫ - 1,000,000₫',     'min' => 500000,  'max' => 1000000 ],
							[ 'label' => '1,000,000₫ - 2,000,000₫',   'min' => 1000000, 'max' => 2000000 ],
							[ 'label' => 'Trên 2,000,000₫',           'min' => 2000000, 'max' => 0 ],
						];

						foreach ( $price_ranges as $range ) :
							$checked = false;
							if ( $range['min'] === 0 && $range['max'] > 0 ) {
								$checked = ( $cur_max == $range['max'] && $cur_min == 0 );
							} elseif ( $range['max'] === 0 && $range['min'] > 0 ) {
								$checked = ( $cur_min == $range['min'] && $cur_max == 0 );
							} else {
								$checked = ( $cur_min == $range['min'] && $cur_max == $range['max'] );
							}

							// Target URL
							$target_url = $shop_url;
							if ( $current_term ) {
								$target_url = get_term_link( $current_term );
							}
							if ( $is_on_sale ) {
								$target_url = add_query_arg( 'on_sale', '1', $target_url );
							}
							if ( $is_featured ) {
								$target_url = add_query_arg( 'featured', '1', $target_url );
							}

							if ( $checked ) {
								// Uncheck link
								$link_url = remove_query_arg( [ 'min_price', 'max_price' ], $target_url );
							} else {
								$params = [];
								if ( $range['min'] > 0 ) $params['min_price'] = $range['min'];
								if ( $range['max'] > 0 ) $params['max_price'] = $range['max'];
								$link_url = add_query_arg( $params, $target_url );
							}
						?>
							<li class="bv-price-filter-item">
								<a href="<?php echo esc_url( $link_url ); ?>" class="bv-checkbox-label <?php echo $checked ? 'is-checked' : ''; ?>">
									<span class="bv-custom-checkbox <?php echo $checked ? 'is-checked' : ''; ?>"></span>
									<span class="bv-checkbox-text"><?php echo esc_html( $range['label'] ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>

				<!-- Group 3: Color Swatches Grid -->
				<div class="bv-filter-group bv-filter-colors">
					<div class="bv-filter-heading">
						<span class="bv-filter-heading-text">MÀU SẮC</span>
						<span class="bv-filter-heading-icon">−</span>
					</div>
					<div class="bv-color-palette-grid">
						<?php foreach ( $glaze_colors as $color ) : ?>
							<span class="bv-palette-swatch" style="background-color: <?php echo esc_attr( $color['hex'] ); ?>;" title="<?php echo esc_attr( $color['name'] ); ?>" aria-label="<?php echo esc_attr( $color['name'] ); ?>"></span>
						<?php endforeach; ?>
					</div>
				</div>

			</aside>

			<!-- ===============================================================
			     RIGHT MAIN CATALOG CONTENT (HEADER + 4-COLUMN GRID)
			     =============================================================== -->
			<main class="bv-shop-main" id="bv-shop-main">
				
				<!-- Catalog Header: Title + Minimal Sort Dropdown -->
				<div class="bv-shop-main-header">
					<h1 class="bv-shop-page-title"><?php echo esc_html( $page_title ); ?></h1>
					
					<div class="bv-shop-sort-wrap">
						<form method="get" class="bv-sort-minimal-form">
							<select name="orderby" id="bv-minimal-sort-select" class="bv-minimal-sort-select" onchange="this.form.submit()">
								<option value="date" <?php selected( $orderby, 'date' ); ?>>Mới nhất ˅</option>
								<option value="menu_order" <?php selected( $orderby, 'menu_order' ); ?>>Mặc định ˅</option>
								<option value="price" <?php selected( $orderby, 'price' ); ?>>Giá: Thấp đến cao ˅</option>
								<option value="price-desc" <?php selected( $orderby, 'price-desc' ); ?>>Giá: Cao đến thấp ˅</option>
								<option value="popularity" <?php selected( $orderby, 'popularity' ); ?>>Bán chạy nhất ˅</option>
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

				<!-- Product Grid -->
				<?php if ( have_posts() ) : ?>
					<div class="bv-ceramic-grid bv-shop-grid">
						<?php while ( have_posts() ) : the_post(); 
							global $product;
							if ( ! is_a( $product, 'WC_Product' ) || $product->is_virtual() || 0 === strpos( $product->get_sku(), 'WS-TICKET' ) || false !== stripos( $product->get_name(), 'vé' ) ) {
								continue;
							}
							
							$pid          = $product->get_id();
							$title        = $product->get_name();
							$permalink    = $product->get_permalink();
							$price_html   = $product->get_price_html();
							$is_in_stock  = $product->is_in_stock();
							$is_on_sale_p = $product->is_on_sale();
							$discount_pct = 0;
							if ( $is_on_sale_p ) {
								$reg = (float) $product->get_regular_price();
								$sal = (float) $product->get_sale_price();
								if ( $reg > 0 && $sal > 0 && $reg > $sal ) {
									$discount_pct = round( ( ( $reg - $sal ) / $reg ) * 100 );
								}
							}
							
							// Images: Primary + Hover Flip
							$image_id     = $product->get_image_id();
							$primary_img  = wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' ) ?: home_url( '/wp-content/uploads/woocommerce-placeholder.webp' );
							$gallery_ids  = $product->get_gallery_image_ids();
							$hover_img    = ! empty( $gallery_ids ) ? wp_get_attachment_image_url( $gallery_ids[0], 'woocommerce_thumbnail' ) : '';
							$has_hover    = ! empty( $hover_img );
						?>
							<article class="bv-product-card <?php echo $has_hover ? 'has-hover-image' : ''; ?> <?php echo $is_on_sale_p ? 'is-on-sale' : ''; ?>" data-product-id="<?php echo esc_attr( $pid ); ?>">
								
								<!-- Product Image Box with Hover Flip & Quick Actions -->
								<div class="bv-card-media">
									<a href="<?php echo esc_url( $permalink ); ?>" class="bv-card-img-link" tabindex="-1">
										<img src="<?php echo esc_url( $primary_img ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="bv-card-img bv-card-img--primary" loading="lazy">
										<?php if ( $has_hover ) : ?>
											<img src="<?php echo esc_url( $hover_img ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="bv-card-img bv-card-img--hover" loading="lazy">
										<?php endif; ?>
									</a>

									<?php if ( $discount_pct > 0 ) : ?>
										<span class="bv-sale-badge">-<?php echo esc_html( $discount_pct ); ?>%</span>
									<?php endif; ?>

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

									<h3 class="bv-card-title">
										<a href="<?php echo esc_url( $permalink ); ?>">
											<?php echo esc_html( $title ); ?>
										</a>
									</h3>

									<!-- Price & Rating Inline -->
									<div class="bv-card-price-bar">
										<div class="bv-card-price <?php echo $is_on_sale_p ? 'has-discount' : ''; ?>">
											<?php echo wp_kses_post( $price_html ); ?>
											<?php if ( $discount_pct > 0 ) : ?>
												<span class="bv-price-save-tag">-<?php echo esc_html( $discount_pct ); ?>%</span>
											<?php endif; ?>
										</div>
										<span class="bv-pt-star-pill">★ 5.0</span>
									</div>
								</div>

							</article>
						<?php endwhile; ?>
					</div>

					<!-- Pagination -->
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
						<h3>Chưa tìm thấy sản phẩm phù hợp</h3>
						<p>Hiện không có tác phẩm gốm nào trong khoảng lọc đã chọn. Mời bạn điều chỉnh lại bộ lọc hoặc xem toàn bộ tác phẩm.</p>
						<a href="<?php echo esc_url( $shop_url ); ?>" class="bv-btn-primary">Xem tất cả sản phẩm</a>
					</div>
				<?php endif; ?>

			</main>

		</div>
	</div>

</div>
