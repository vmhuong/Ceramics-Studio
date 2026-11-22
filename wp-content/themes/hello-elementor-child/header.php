<?php
/**
 * Header template - Phong cách Mộc mạc Nhật Bản (Wabi-Sabi / Zen)
 * Theme: Hello Elementor Child for Ceramic Studio
 *
 * @package HelloElementorChild
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$site_name = get_bloginfo( 'name' );
$home_url  = home_url( '/' );
$shop_url  = home_url( '/san-pham-gom/' );
$workshop_url = home_url( '/workshop/' );
$cart_url  = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/gio-hang-gom/' );
$account_url = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'myaccount' ) : home_url( '/tai-khoan/' );
$cart_count = ( function_exists( 'WC' ) && WC()->cart ) ? WC()->cart->get_cart_contents_count() : 0;
$logged_in = is_user_logged_in();
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

<!-- SKIP LINK FOR ACCESSIBILITY -->
<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'hello-elementor' ); ?></a>

<!-- SITE HEADER -->
<header id="site-header" class="bv-site-header wabi-sabi-header">
	<div class="bv-header-container">
		<div class="bv-header-row">
			
			<!-- BRAND LOGO & TITLE -->
			<div class="bv-header-brand">
				<a href="<?php echo esc_url( $home_url ); ?>" class="bv-brand-link" rel="home">
					<div class="bv-brand-logo-icon">
						<svg width="28" height="28" viewBox="0 0 32 32" fill="none">
							<path d="M16 3C10.5 3 6 7 6 12C6 16.5 9 19.5 12 21.5V26C12 27.1 12.9 28 14 28H18C19.1 28 20 27.1 20 26V21.5C23 19.5 26 16.5 26 12C26 7 21.5 3 16 3Z" stroke="#B3592B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							<path d="M11 12C11 12 13 14 16 14C19 14 21 12 21 12" stroke="#6B4C35" stroke-width="1.8" stroke-linecap="round"/>
							<path d="M13 28H19" stroke="#B3592B" stroke-width="2" stroke-linecap="round"/>
						</svg>
					</div>
					<div class="bv-brand-text">
						<span class="bv-brand-name">CERAMIC STUDIO</span>
						<span class="bv-brand-slogan">Gốm Mộc &amp; Workshop</span>
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

			<!-- ACTIONS: HOTLINE, CART, ACCOUNT, CTA, HAMBURGER -->
			<div class="bv-header-actions">
				<!-- Hotline Pill -->
				<a href="tel:0363292271" class="bv-action-hotline" title="Hotline tư vấn xưởng gốm: 0363 292 271">
					<span class="bv-hotline-icon" aria-hidden="true">
						<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
					</span>
					<span class="bv-hotline-num">0363 292 271</span>
				</a>

				<!-- WooCommerce Cart -->
				<a class="bv-header-cart" href="<?php echo esc_url( $cart_url ); ?>" aria-label="<?php echo esc_attr( sprintf( 'Giỏ hàng, %d sản phẩm', $cart_count ) ); ?>" title="Giỏ hàng gốm mộc">
					<span class="bv-cart-icon" aria-hidden="true">
						<svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
					</span>
					<span class="bv-cart-count" aria-hidden="true"><?php echo esc_html( $cart_count > 99 ? '99+' : $cart_count ); ?></span>
				</a>

				<!-- User Account -->
				<a class="bv-header-account <?php echo $logged_in ? 'is-logged-in' : ''; ?>" href="<?php echo esc_url( $account_url ); ?>" aria-label="<?php echo esc_attr( $logged_in ? 'Tài khoản của bạn' : 'Đăng nhập hoặc đăng ký' ); ?>" title="<?php echo esc_attr( $logged_in ? 'Tài khoản của bạn' : 'Đăng nhập / Đăng ký' ); ?>">
					<svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
				</a>

				<!-- Workshop CTA Button -->
				<a href="<?php echo esc_url( $workshop_url ); ?>" class="bv-header-cta">
					<span>Đặt Workshop</span>
				</a>

				<!-- Mobile Menu Toggle Button -->
				<button type="button" class="bv-mobile-toggle" aria-label="Mở danh mục điều hướng" aria-expanded="false" aria-controls="bv-mobile-drawer">
					<span class="bv-burger-line"></span>
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
					<span class="bv-brand-slogan">Tiệm Gốm Mộc Nhật Bản</span>
				</div>
				<button type="button" class="bv-drawer-close" aria-label="Đóng menu">
					<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
				</button>
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
						✦ Đặt Lịch Trải Nghiệm Workshop
					</a>
				</div>

				<div class="bv-drawer-info">
					<div class="bv-dinfo-item">
						<span class="bv-dinfo-label">Hotline / Zalo:</span>
						<a href="https://zalo.me/0363292271" target="_blank" rel="noopener">0363 292 271</a>
					</div>
					<div class="bv-dinfo-item">
						<span class="bv-dinfo-label">Địa chỉ xưởng:</span>
						<span>Thôn 1 Giang Cao, Bát Tràng, Hà Nội</span>
					</div>
					<div class="bv-dinfo-item">
						<span class="bv-dinfo-label">Giờ đón khách:</span>
						<span>08:30 – 21:00 (Hàng ngày)</span>
					</div>
				</div>
			</div>
		</div>
		<div class="bv-drawer-backdrop" tabindex="-1"></div>
	</div>
</header>

<div id="content" class="site-content">
