<?php
/**
 * WooCommerce wrapper template for the pottery shop.
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>

<main id="content" class="site-main bv-woocommerce bv-commerce-shell">
	<?php
	$shop_document = class_exists( '\Elementor\Plugin' ) ? \Elementor\Plugin::$instance->documents->get( wc_get_page_id( 'shop' ) ) : null;
	if ( is_shop() && $shop_document && $shop_document->is_built_with_elementor() ) {
		?>
		<header class="bv-shop-heading">
			<nav class="bv-shop-breadcrumb" aria-label="Đường dẫn">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Trang chủ</a>
				<span aria-hidden="true">/</span>
				<span aria-current="page">Sản phẩm</span>
			</nav>
			<h1>Sản phẩm</h1>
		</header>
		<?php
		wc_print_notices();
		echo \Elementor\Plugin::$instance->frontend->get_builder_content( wc_get_page_id( 'shop' ), true );
	} else {
		woocommerce_breadcrumb();
		echo '<div class="woocommerce">';
		woocommerce_content();
		echo '</div>';
	}
	?>
</main>

<?php
get_footer();
