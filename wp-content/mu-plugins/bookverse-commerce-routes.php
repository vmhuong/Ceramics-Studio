<?php
/**
 * Stable storefront routes for the ceramics shop.
 *
 * @package Bookverse
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function bookverse_get_page_url_by_slug( $slug ) {
	$page = get_page_by_path( $slug, OBJECT, 'page' );

	return $page ? get_permalink( $page ) : '';
}

add_filter(
	'woocommerce_get_cart_url',
	function ( $url ) {
		return bookverse_get_page_url_by_slug( 'gio-hang-gom' ) ?: $url;
	}
);

add_filter(
	'woocommerce_get_checkout_url',
	function ( $url ) {
		return bookverse_get_page_url_by_slug( 'thanh-toan-gom' ) ?: $url;
	}
);

add_filter(
	'template_include',
	function ( $template ) {
		$is_product_screen = (
			is_post_type_archive( 'product' )
			|| is_singular( 'product' )
			|| is_tax( [ 'product_cat', 'product_tag' ] )
		);

		if ( $is_product_screen ) {
			$woocommerce_template = get_stylesheet_directory() . '/woocommerce.php';

			if ( file_exists( $woocommerce_template ) ) {
				return $woocommerce_template;
			}
		}

		return $template;
	},
	9999
);

add_shortcode(
	'bookverse_products',
	function () {
		if ( ! function_exists( 'wc_get_products' ) ) {
			return '';
		}

		$products = wc_get_products(
			[
				'status'  => 'publish',
				'limit'   => 12,
				'orderby' => 'date',
				'order'   => 'DESC',
			]
		);

		if ( empty( $products ) ) {
			return '<p>Chưa có sản phẩm.</p>';
		}

		ob_start();
		?>
		<div class="bv-product-grid">
			<?php foreach ( $products as $product ) : ?>
				<article class="bv-product-card">
					<div class="bv-product-card__image">
						<?php echo $product->get_image( 'woocommerce_thumbnail' ); ?>
					</div>
					<div class="bv-product-card__body">
						<h2><?php echo esc_html( $product->get_name() ); ?></h2>
						<p><?php echo wp_kses_post( $product->get_short_description() ); ?></p>
						<div class="bv-product-card__meta">
							<strong><?php echo wp_kses_post( $product->get_price_html() ); ?></strong>
							<a class="button" href="<?php echo esc_url( $product->add_to_cart_url() ); ?>">
								<?php echo esc_html( $product->add_to_cart_text() ); ?>
							</a>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
		<?php

		return ob_get_clean();
	}
);
