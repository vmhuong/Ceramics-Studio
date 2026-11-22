<?php
/** Account navigation, WooCommerce template version 9.3.0. */
defined( 'ABSPATH' ) || exit;
do_action( 'woocommerce_before_account_navigation' );
$user = wp_get_current_user();
$icons = [ 'dashboard' => 'eicon-dashboard', 'orders' => 'eicon-cart-light', 'downloads' => 'eicon-download-button', 'edit-address' => 'eicon-map-pin', 'edit-account' => 'eicon-user-circle-o', 'customer-logout' => 'eicon-sign-out' ];
?>
<nav class="woocommerce-MyAccount-navigation" aria-label="Tài khoản">
    <div class="bv-account-identity"><i class="eicon-user-circle-o" aria-hidden="true"></i><div><small>Tài khoản của bạn</small><strong><?php echo esc_html( $user->first_name ?: $user->display_name ); ?></strong></div></div>
    <ul>
        <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
        <li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?>">
            <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" <?php echo wc_is_current_account_menu_item( $endpoint ) ? 'aria-current="page"' : ''; ?>>
                <i class="<?php echo esc_attr( $icons[$endpoint] ?? 'eicon-folder' ); ?>" aria-hidden="true"></i><span><?php echo esc_html( $label ); ?></span>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</nav>
<?php do_action( 'woocommerce_after_account_navigation' ); ?>
