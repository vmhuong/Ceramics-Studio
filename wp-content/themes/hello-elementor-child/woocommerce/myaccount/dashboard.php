<?php
/**
 * Ceramic Studio Luxury Account Dashboard
 * WooCommerce template version 4.4.0
 *
 * @package HelloElementorChild
 */

defined( 'ABSPATH' ) || exit;

$user          = wp_get_current_user();
$display_name  = $user->first_name ?: $user->display_name;
$customer_id   = get_current_user_id();
$all_orders    = wc_get_orders( [ 'customer_id' => $customer_id, 'limit' => -1 ] );
$order_count   = count( $all_orders );
$recent_orders = wc_get_orders( [ 'customer_id' => $customer_id, 'limit' => 3, 'orderby' => 'date', 'order' => 'DESC' ] );
$shop_url      = wc_get_page_permalink( 'shop' );
$workshop_url  = home_url( '/workshop/' );
?>

<div class="bv-dashboard-wrap">

    <!-- 1. WELCOME HERO BANNER -->
    <div class="bv-dashboard-hero">
        <span class="bv-dash-eyebrow">XƯỞNG GỐM MỘC • CERAMIC STUDIO</span>
        <h2 class="bv-dash-title">Xin chào, <?php echo esc_html( $display_name ); ?>!</h2>
        <p class="bv-dash-intro">
            Chào mừng bạn đến với góc riêng của mình tại xưởng. Nơi bạn có thể theo dõi tiến độ các tác phẩm gốm đã đặt, quản lý lịch học workshop và cập nhật thông tin cá nhân.
        </p>
    </div>

    <!-- 2. QUICK ACTION SHORTCUTS (3 CRAFT CARDS) -->
    <div class="bv-dash-shortcuts">
        <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="bv-shortcut-card">
            <div class="bv-sc-top">
                <span class="bv-sc-count"><?php echo esc_html( $order_count ); ?></span>
                <span class="bv-sc-label">Đơn hàng</span>
            </div>
            <h4>Đơn hàng của bạn</h4>
            <p>Xem trạng thái đóng gói, nung gốm và lộ trình vận chuyển.</p>
            <span class="bv-sc-link">Xem danh sách →</span>
        </a>

        <a href="<?php echo esc_url( $workshop_url ); ?>" class="bv-shortcut-card">
            <div class="bv-sc-top">
                <span class="bv-sc-count">WS</span>
                <span class="bv-sc-label">Trải nghiệm</span>
            </div>
            <h4>Lịch học Workshop</h4>
            <p>Tự tay vuốt nặn, chuốt men mộc và nung tác phẩm của riêng bạn.</p>
            <span class="bv-sc-link">Đặt lịch học →</span>
        </a>

        <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-address' ) ); ?>" class="bv-shortcut-card">
            <div class="bv-sc-top">
                <span class="bv-sc-count">01</span>
                <span class="bv-sc-label">Sổ địa chỉ</span>
            </div>
            <h4>Địa chỉ nhận gốm</h4>
            <p>Quản lý địa chỉ giao hàng mặc định để thanh toán nhanh chóng.</p>
            <span class="bv-sc-link">Cập nhật →</span>
        </a>
    </div>

    <!-- 3. RECENT ORDERS SECTION -->
    <div class="bv-dash-section">
        <div class="bv-dash-section-header">
            <h3>Đơn hàng gần đây</h3>
            <?php if ( ! empty( $recent_orders ) ) : ?>
                <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="bv-dash-more-link">
                    Xem tất cả đơn hàng →
                </a>
            <?php endif; ?>
        </div>

        <?php if ( ! empty( $recent_orders ) ) : ?>
            <div class="bv-dash-orders-table-wrap">
                <table class="bv-dash-orders-table">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Ngày đặt</th>
                            <th>Trạng thái</th>
                            <th>Tổng cộng</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ( $recent_orders as $order ) : 
                            $item_count = $order->get_item_count();
                        ?>
                            <tr>
                                <td>
                                    <strong>#<?php echo esc_html( $order->get_order_number() ); ?></strong>
                                </td>
                                <td>
                                    <span><?php echo esc_html( $order->get_date_created() ? wc_format_datetime( $order->get_date_created() ) : '—' ); ?></span>
                                </td>
                                <td>
                                    <span class="bv-order-status bv-status--<?php echo esc_attr( $order->get_status() ); ?>">
                                        <?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
                                    </span>
                                </td>
                                <td>
                                    <strong class="bv-order-total-price"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></strong>
                                    <small>(<?php echo esc_html( $item_count ); ?> món)</small>
                                </td>
                                <td>
                                    <a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="bv-btn-order-view">
                                        Chi tiết
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <div class="bv-dash-empty-state">
                <div class="bv-empty-symbol" aria-hidden="true">
                    <span class="bv-empty-circle"></span>
                </div>
                <h4>Bạn chưa có đơn hàng nào tại xưởng</h4>
                <p>Mỗi món gốm mộc tại xưởng đều mang linh hồn riêng biệt, đong đầy sự bình yên cho không gian sống của bạn.</p>
                <a href="<?php echo esc_url( $shop_url ); ?>" class="bv-btn-dash-cta">
                    Khám phá các tác phẩm gốm
                </a>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php
do_action( 'woocommerce_account_dashboard' );
do_action( 'woocommerce_before_my_account' );
do_action( 'woocommerce_after_my_account' );
