<?php
/** Account dashboard, WooCommerce template version 4.4.0. */
defined( 'ABSPATH' ) || exit;
$recent = wc_get_orders( [ 'customer_id' => get_current_user_id(), 'limit' => 3, 'orderby' => 'date', 'order' => 'DESC' ] );
?>
<div class="bv-account-welcome">
    <span class="bv-account-eyebrow">GÓC NHỎ CỦA BẠN</span>
    <h2>Xin chào, <?php echo esc_html( $current_user->first_name ?: $current_user->display_name ); ?>.</h2>
</div>
<div class="bv-account-section-title"><h3>Đơn hàng gần đây</h3><a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>">Tất cả đơn hàng</a></div>
<?php if ( $recent ) : ?>
<div class="bv-recent-orders">
<?php foreach ( $recent as $order ) : ?>
    <a class="bv-recent-order" href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
        <strong>#<?php echo esc_html( $order->get_order_number() ); ?></strong>
        <span><?php echo esc_html( $order->get_date_created() ? wc_format_datetime( $order->get_date_created() ) : '' ); ?></span>
        <span><?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?></span>
        <b><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></b>
    </a>
<?php endforeach; ?>
</div>
<?php else : ?>
<div class="bv-account-empty">
    <?php echo wp_get_attachment_image( 370, 'medium', false, [ 'class' => 'bv-account-pottery', 'alt' => 'Bộ sưu tập bình gốm thủ công' ] ); ?>
    <h3>Chưa có đơn hàng nào</h3>
    <a class="button" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">Khám phá bộ sưu tập</a>
</div>
<?php endif; ?>
<div class="bv-account-quicklinks">
    <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-address' ) ); ?>"><span>Địa chỉ của tôi</span></a>
    <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>"><span>Thông tin &amp; mật khẩu</span></a>
</div>
<?php
do_action( 'woocommerce_account_dashboard' );
do_action( 'woocommerce_before_my_account' );
do_action( 'woocommerce_after_my_account' );
