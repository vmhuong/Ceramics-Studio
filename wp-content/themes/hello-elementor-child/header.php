<?php
/**
 * Header template - Phong cách Tối giản Cao cấp (Luxury Craft Minimalism)
 * Theme: Hello Elementor Child for Ceramic Studio
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$site_name    = get_bloginfo( 'name' );
$home_url     = home_url( '/' );
$shop_url     = home_url( '/san-pham-gom/' );
$workshop_url = home_url( '/workshop/' );
$cart_url     = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/gio-hang-gom/' );
$account_url  = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/tai-khoan/' );
$cart_count   = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
$logged_in    = is_user_logged_in();
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'bv-wabi-sabi-body' ); ?>>
<?php wp_body_open(); ?>

<!-- SKIP LINK -->
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'hello-elementor' ); ?></a>

<!-- SITE HEADER -->
<header id="site-header" class="bv-site-header wabi-sabi-header">
	<div class="bv-header-container">
		<div class="bv-header-row">
			
			<!-- BRAND LOGO & TITLE (PURE LUXURY TYPOGRAPHY - NO ICONS) -->
			<div class="bv-header-brand">
				<a href="<?php echo esc_url( $home_url ); ?>" class="bv-brand-link" rel="home">
					<div class="bv-brand-text">
						<span class="bv-brand-name">CERAMIC STUDIO</span>
						<span class="bv-brand-slogan">Xưởng Gốm Mộc &amp; Workshop</span>
					</div>
				</a>
			</div>

			<!-- PRIMARY NAVIGATION (DESKTOP) -->
			<nav class="bv-header-nav" aria-label="Menu chính">
				<?php
				wp_nav_menu( [
					'theme_location' => 'menu-1',
					'menu_class'     => 'bv-nav-menu',
					'container'      => false,
					'fallback_cb'    => false,
					'depth'          => 2,
				] );
				?>
			</nav>

			<!-- ACTIONS: TEXT HOTLINE, CART COUNT, ACCOUNT, CTA (NO ICONS) -->
			<div class="bv-header-actions">
				<!-- Hotline Text -->
				<a href="tel:0363292271" class="bv-action-hotline" title="Hotline xưởng: 0363 292 271">
					Hotline: 0363 292 271
				</a>

				<!-- WooCommerce Cart Text -->
				<a class="bv-header-cart" href="<?php echo esc_url( $cart_url ); ?>" title="Xem giỏ hàng">
					Giỏ hàng (<span class="bv-cart-count"><?php echo esc_html( $cart_count ); ?></span>)
				</a>

				<!-- User Account Text -->
				<a class="bv-header-account" href="<?php echo esc_url( $account_url ); ?>" title="Tài khoản">
					<?php echo $logged_in ? 'Tài khoản' : 'Đăng nhập'; ?>
				</a>

				<!-- Workshop CTA Button -->
				<a href="<?php echo esc_url( $workshop_url ); ?>" class="bv-header-cta">
					Đặt Workshop
				</a>

				<!-- Mobile Menu Toggle Button -->
				<button type="button" class="bv-mobile-toggle" aria-label="Mở menu" aria-expanded="false" aria-controls="bv-mobile-drawer">
					<span class="bv-burger-line"></span>
					<span class="bv-burger-line"></span>
				</button>
			</div>

		</div>
	</div>

	<!-- MOBILE NAVIGATION DRAWER -->
	<div id="bv-mobile-drawer" class="bv-mobile-drawer" aria-hidden="true">
		<div class="bv-drawer-panel">
			<div class="bv-drawer-top">
				<div class="bv-drawer-brand">
					<span class="bv-brand-name">CERAMIC STUDIO</span>
				</div>
				<button type="button" class="bv-drawer-close" aria-label="Đóng menu">Đóng [✕]</button>
			</div>

			<div class="bv-drawer-body">
				<nav class="bv-drawer-nav" aria-label="Menu di động">
					<?php
					wp_nav_menu( [
						'theme_location' => 'menu-1',
						'menu_class'     => 'bv-drawer-menu',
						'container'      => false,
						'fallback_cb'    => false,
					] );
					?>
				</nav>

				<div class="bv-drawer-cta-wrap">
					<a href="<?php echo esc_url( $workshop_url ); ?>" class="bv-drawer-cta-btn">
						Đặt Ca Trải Nghiệm Workshop
					</a>
				</div>

				<div class="bv-drawer-info">
					<div class="bv-dinfo-item">
						<span class="bv-dinfo-label">Hotline / Zalo</span>
						<a href="https://zalo.me/0363292271" target="_blank" rel="noopener">0363 292 271</a>
					</div>
					<div class="bv-dinfo-item">
						<span class="bv-dinfo-label">Địa chỉ xưởng</span>
						<span>Thôn 1 Giang Cao, Bát Tràng, Gia Lâm, Hà Nội</span>
					</div>
					<div class="bv-dinfo-item">
						<span class="bv-dinfo-label">Giờ đón khách</span>
						<span>08:30 – 21:00 (Hàng ngày)</span>
					</div>
				</div>
			</div>
		</div>
		<div class="bv-drawer-backdrop" tabindex="-1"></div>
	</div>
</header>

<div id="content" class="site-content">
