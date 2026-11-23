<?php
/**
 * Ceramic Studio Luxury Account Navigation
 * WooCommerce template version 9.3.0
 *
 * @package HelloElementorChild
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_navigation' );
$user       = wp_get_current_user();
$name       = $user->display_name ?: ( $user->user_login ?: 'Khách hàng' );
$first_char = mb_substr( $name, 0, 1 );
?>
<nav class="woocommerce-MyAccount-navigation" aria-label="Menu tài khoản">
    <div class="bv-account-profile-card">
        <div class="bv-profile-avatar" aria-hidden="true">
            <?php echo esc_html( mb_strtoupper( $first_char ) ); ?>
        </div>
        <div class="bv-profile-info">
            <span class="bv-profile-badge">Khách hàng xưởng</span>
            <strong class="bv-profile-name"><?php echo esc_html( $name ); ?></strong>
            <small class="bv-profile-email"><?php echo esc_html( $user->user_email ); ?></small>
        </div>
    </div>

    <ul class="bv-account-nav-list">
        <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : 
            $is_active = wc_is_current_account_menu_item( $endpoint );
        ?>
        <li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?><?php echo $is_active ? ' is-active' : ''; ?>">
            <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" <?php echo $is_active ? 'aria-current="page"' : ''; ?>>
                <span class="bv-nav-label"><?php echo esc_html( $label ); ?></span>
                <span class="bv-nav-arrow" aria-hidden="true">→</span>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
</nav>
<?php do_action( 'woocommerce_after_account_navigation' ); ?>
