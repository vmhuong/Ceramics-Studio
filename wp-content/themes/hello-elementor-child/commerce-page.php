<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
get_header();
$received = is_order_received_page();
$title = is_cart() ? 'Giỏ hàng của bạn' : ( is_checkout() ? ( $received ? 'Cảm ơn bạn đã chọn gốm' : 'Thanh toán' ) : get_the_title() );
$account = is_account_page();
$guest = $account && ! is_user_logged_in();
if ( $account ) {
	$title = $guest ? ( is_wc_endpoint_url( 'lost-password' ) ? 'Tìm lại mật khẩu' : 'Chào mừng trở lại' ) : 'Tài khoản của tôi';
}
?>
<main id="content" class="site-main bv-commerce-shell<?php echo $account ? ' bv-account-page' . ( $guest ? ' bv-account-guest' : '' ) : ''; ?>">
	<header class="bv-commerce-heading">
		<a class="bv-back-link" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><i class="eicon-arrow-left" aria-hidden="true"></i> Tiếp tục chọn gốm</a>
		<?php if ( is_cart() || is_checkout() ) { bv_commerce_steps(); } ?>
		<h1><?php echo esc_html( $title ); ?></h1>
		<?php if ( $guest ) : ?><p class="bv-account-subtitle">Một chút gốm, một chút an yên.</p><?php endif; ?>
	</header>
	<?php while ( have_posts() ) : the_post(); ?>
		<div class="bv-commerce-content"><?php the_content(); ?></div>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
