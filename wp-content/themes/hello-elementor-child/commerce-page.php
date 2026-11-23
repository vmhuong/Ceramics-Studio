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
	<?php if ( $guest ) : ?>
		<!-- GUEST ACCOUNT HERO & STORY SPLIT -->
		<div class="bv-account-guest-wrapper">
			<section class="bv-account-story-card">
				<a class="bv-back-link" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">← Quay lại Cửa hàng gốm</a>
				<div class="bv-story-header">
					<span class="bv-story-eyebrow">XƯỞNG GỐM MỘC & WORKSHOP</span>
					<h1 class="bv-story-title">Không Gian Gốm Thủ Công</h1>
					<p class="bv-story-quote">"Chạm tay vào đất mẹ – Tìm lại sự an yên và mộc mạc trong từng nếp gốm vuốt nặn thủ công."</p>
				</div>
				<div class="bv-story-perks">
					<div class="bv-perk-item">
						<span class="bv-perk-number">01</span>
						<div class="bv-perk-content">
							<strong>Quản lý & Đổi lịch Workshop</strong>
							<p>Chủ động theo dõi lịch học, chọn ca làm gốm và nhận tài liệu chuẩn bị trước buổi trải nghiệm.</p>
						</div>
					</div>
					<div class="bv-perk-item">
						<span class="bv-perk-number">02</span>
						<div class="bv-perk-content">
							<strong>Theo dõi tiến độ mẻ nung gốm</strong>
							<p>Cập nhật trạng thái sấy khô, tráng men mộc và ngày nhận những chiếc bình/ly bạn tự tay tạo tác.</p>
						</div>
					</div>
					<div class="bv-perk-item">
						<span class="bv-perk-number">03</span>
						<div class="bv-perk-content">
							<strong>Đặc quyền thành viên xưởng</strong>
							<p>Lưu địa chỉ nhận gốm nhanh chóng, lịch sử đơn hàng và nhận thông báo sớm về các mẻ gốm độc bản.</p>
						</div>
					</div>
				</div>
				<div class="bv-story-badge-footer">
					<span>Hà Nội • Men tro tự nhiên • Nung củi nhiệt cao 1.250°C</span>
				</div>
			</section>

			<section class="bv-account-form-card">
				<div class="bv-commerce-content">
					<?php while ( have_posts() ) : the_post(); the_content(); endwhile; ?>
				</div>
			</section>
		</div>
	<?php else : ?>
		<header class="bv-commerce-heading">
			<a class="bv-back-link" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">← Tiếp tục chọn gốm</a>
			<?php if ( is_cart() || is_checkout() ) { bv_commerce_steps(); } ?>
			<h1><?php echo esc_html( $title ); ?></h1>
		</header>
		<?php while ( have_posts() ) : the_post(); ?>
			<div class="bv-commerce-content"><?php the_content(); ?></div>
		<?php endwhile; ?>
	<?php endif; ?>
</main>
<?php get_footer(); ?>
