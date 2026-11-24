<?php
defined( 'ABSPATH' ) || exit;

// These pages depend on the current session and must not be prerendered on hover.
add_filter( 'wp_speculation_rules_href_exclude_paths', function ( $paths ) {
    if ( ! function_exists( 'wc_get_page_permalink' ) ) { return $paths; }
    $home_path = untrailingslashit( (string) wp_parse_url( home_url( '/' ), PHP_URL_PATH ) );
    foreach ( [ 'cart', 'checkout', 'myaccount' ] as $page ) {
        $path = (string) wp_parse_url( wc_get_page_permalink( $page ), PHP_URL_PATH );
        if ( $home_path && 0 === strpos( $path, $home_path . '/' ) ) {
            $path = substr( $path, strlen( $home_path ) );
        }
        if ( '/' !== $path && '' !== $path ) { $paths[] = untrailingslashit( $path ) . '*'; }
    }
    return array_values( array_unique( $paths ) );
} );

// Keep billing data available to checkout, but show only delivery addresses in My Account.
add_filter( 'woocommerce_my_account_get_addresses', function ( $addresses ) {
    unset( $addresses['billing'] );
    return $addresses;
} );

add_filter( 'gettext', function ( $translation, $text, $domain ) {
    return 'woocommerce' === $domain && 'Subtotal:' === $text ? 'Tạm tính:' : $translation;
}, 10, 3 );

add_action( 'wp_enqueue_scripts', function () {
    if ( ! function_exists( 'WC' ) ) { return; }
    wp_enqueue_style( 'bv-shopping-panels', get_stylesheet_directory_uri() . '/assets/shopping-panels.css', [ 'bv-commerce' ], time() );
    wp_enqueue_script( 'bv-shopping-panels', get_stylesheet_directory_uri() . '/assets/shopping-panels.js', [ 'bv-commerce', 'wc-add-to-cart' ], time(), true );
    wp_localize_script( 'bv-shopping-panels', 'bvShopping', [ 
        'noteUrl'      => WC_AJAX::get_endpoint( 'bv_cart_note' ), 
        'nonce'        => wp_create_nonce( 'bv_cart_note' ),
        'qtyUrl'       => WC_AJAX::get_endpoint( 'bv_update_cart_quantity' ),
        'qtyNonce'     => wp_create_nonce( 'bv_cart_qty_nonce' ),
        'switchVarUrl' => WC_AJAX::get_endpoint( 'bv_switch_cart_variation' ),
        'selectUrl'    => WC_AJAX::get_endpoint( 'bv_toggle_cart_selection' ),
        'selectNonce'  => wp_create_nonce( 'bv_cart_select_nonce' ),
    ] );
}, 50 );

// AJAX Handler for Quantity Update in Mini-Cart Drawer
add_action( 'wc_ajax_bv_update_cart_quantity', 'bv_update_cart_quantity_ajax' );
add_action( 'wp_ajax_bv_update_cart_quantity', 'bv_update_cart_quantity_ajax' );
add_action( 'wp_ajax_nopriv_bv_update_cart_quantity', 'bv_update_cart_quantity_ajax' );

function bv_update_cart_quantity_ajax() {
    check_ajax_referer( 'bv_cart_qty_nonce', 'nonce' );
    $cart_item_key = sanitize_text_field( $_POST['cart_item_key'] ?? '' );
    $quantity      = isset( $_POST['quantity'] ) ? max( 0, intval( $_POST['quantity'] ) ) : 1;

    if ( $cart_item_key && WC()->cart && WC()->cart->get_cart_item( $cart_item_key ) ) {
        if ( $quantity <= 0 ) {
            WC()->cart->remove_cart_item( $cart_item_key );
        } else {
            WC()->cart->set_quantity( $cart_item_key, $quantity, true );
        }
        WC()->cart->calculate_totals();

        WC_AJAX::get_refreshed_fragments();
    } else {
        wp_send_json_error( [ 'message' => 'Mục không hợp lệ' ] );
    }
}

// AJAX Handler for Switching Variant in Mini-Cart Drawer
add_action( 'wc_ajax_bv_switch_cart_variation', 'bv_switch_cart_variation_ajax' );
add_action( 'wp_ajax_bv_switch_cart_variation', 'bv_switch_cart_variation_ajax' );
add_action( 'wp_ajax_nopriv_bv_switch_cart_variation', 'bv_switch_cart_variation_ajax' );

function bv_switch_cart_variation_ajax() {
    check_ajax_referer( 'bv_cart_qty_nonce', 'nonce' );
    $old_cart_item_key = sanitize_text_field( $_POST['cart_item_key'] ?? '' );
    $new_variation_id  = isset( $_POST['variation_id'] ) ? intval( $_POST['variation_id'] ) : 0;

    if ( ! $old_cart_item_key || ! $new_variation_id || ! WC()->cart ) {
        wp_send_json_error( [ 'message' => 'Dữ liệu không hợp lệ' ] );
    }

    $cart_item = WC()->cart->get_cart_item( $old_cart_item_key );
    if ( ! $cart_item ) {
        wp_send_json_error( [ 'message' => 'Không tìm thấy sản phẩm trong giỏ' ] );
    }

    $product_id = (int) $cart_item['product_id'];
    $quantity   = (int) $cart_item['quantity'];

    $variation_product = wc_get_product( $new_variation_id );
    if ( ! $variation_product || ! $variation_product->is_type( 'variation' ) || (int) $variation_product->get_parent_id() !== $product_id ) {
        wp_send_json_error( [ 'message' => 'Biến thể không hợp lệ' ] );
    }

    // Get variation attributes
    $variation_attributes = $variation_product->get_variation_attributes();

    // Remove old cart item
    WC()->cart->remove_cart_item( $old_cart_item_key );

    // Add new variation to cart
    $new_cart_item_key = WC()->cart->add_to_cart( $product_id, $quantity, $new_variation_id, $variation_attributes );
    WC()->cart->calculate_totals();

    if ( $new_cart_item_key ) {
        WC_AJAX::get_refreshed_fragments();
    } else {
        wp_send_json_error( [ 'message' => 'Không thể đổi biến thể' ] );
    }
}

// ==========================================================================
// SELECTIVE CART CHECKOUT (CHỌN MÓN THANH TOÁN / CHỌN TẤT CẢ)
// ==========================================================================

/**
 * Get list of currently selected cart item keys.
 * Default: all items in cart are selected.
 */
function bv_get_selected_cart_item_keys() {
    if ( ! function_exists( 'WC' ) || ! WC()->session || ! WC()->cart ) {
        return [];
    }
    $cart_keys = array_keys( WC()->cart->get_cart() );
    $selected  = WC()->session->get( 'bv_selected_cart_items' );

    if ( null === $selected || ! is_array( $selected ) ) {
        $selected = $cart_keys;
        WC()->session->set( 'bv_selected_cart_items', $selected );
    } else {
        // Intersect to ensure only valid cart items remain
        $selected = array_values( array_intersect( $selected, $cart_keys ) );
    }
    return $selected;
}

/**
 * Check if a specific cart item is selected.
 */
function bv_is_cart_item_selected( $cart_item_key ) {
    $selected_keys = bv_get_selected_cart_item_keys();
    return in_array( $cart_item_key, $selected_keys, true );
}

/**
 * Calculate subtotal and count for currently selected cart items.
 */
function bv_get_selected_cart_summary() {
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) {
        return [
            'subtotal'      => 0,
            'subtotal_html' => wc_price( 0 ),
            'count'         => 0,
            'total_items'   => 0,
            'all_selected'  => true,
        ];
    }

    $cart          = WC()->cart->get_cart();
    $total_items   = count( $cart );
    $selected_keys = bv_get_selected_cart_item_keys();
    $subtotal      = 0.0;
    $count         = 0;

    foreach ( $cart as $key => $item ) {
        if ( in_array( $key, $selected_keys, true ) ) {
            $_product = $item['data'] ?? null;
            if ( $_product && $_product->exists() ) {
                $qty = (int) $item['quantity'];
                $price = (float) $_product->get_price();
                $subtotal += ( $price * $qty );
                $count += $qty;
            }
        }
    }

    $all_selected = ( $total_items > 0 && count( $selected_keys ) === $total_items );

    return [
        'subtotal'      => $subtotal,
        'subtotal_html' => wc_price( $subtotal ),
        'count'         => $count,
        'total_items'   => $total_items,
        'all_selected'  => $all_selected,
    ];
}

// When item is added to cart, make sure it is marked as selected
add_action( 'woocommerce_add_to_cart', function ( $cart_item_key ) {
    if ( WC()->session ) {
        $selected = WC()->session->get( 'bv_selected_cart_items', [] );
        if ( is_array( $selected ) && ! in_array( $cart_item_key, $selected, true ) ) {
            $selected[] = $cart_item_key;
            WC()->session->set( 'bv_selected_cart_items', $selected );
        }
    }
} );

// When item is removed from cart, clean up from selected keys
add_action( 'woocommerce_cart_item_removed', function ( $cart_item_key ) {
    if ( WC()->session ) {
        $selected = WC()->session->get( 'bv_selected_cart_items', [] );
        if ( is_array( $selected ) ) {
            $selected = array_values( array_diff( $selected, [ $cart_item_key ] ) );
            WC()->session->set( 'bv_selected_cart_items', $selected );
        }
    }
} );

// AJAX Handler for toggling selection (single item or all items)
add_action( 'wc_ajax_bv_toggle_cart_selection', 'bv_toggle_cart_selection_ajax' );
add_action( 'wp_ajax_bv_toggle_cart_selection', 'bv_toggle_cart_selection_ajax' );
add_action( 'wp_ajax_nopriv_bv_toggle_cart_selection', 'bv_toggle_cart_selection_ajax' );

function bv_toggle_cart_selection_ajax() {
    check_ajax_referer( 'bv_cart_select_nonce', 'nonce' );
    if ( ! WC()->session || ! WC()->cart ) {
        wp_send_json_error( [ 'message' => 'Phiên giỏ hàng không hợp lệ' ] );
    }

    $all_keys = array_keys( WC()->cart->get_cart() );
    $selected_keys = WC()->session->get( 'bv_selected_cart_items' );
    if ( null === $selected_keys || ! is_array( $selected_keys ) ) {
        $selected_keys = $all_keys;
    }

    if ( isset( $_POST['select_all'] ) ) {
        $select_all = (bool) intval( $_POST['select_all'] );
        $selected_keys = $select_all ? $all_keys : [];
    } elseif ( isset( $_POST['cart_item_key'] ) ) {
        $key = sanitize_text_field( $_POST['cart_item_key'] );
        $is_checked = isset( $_POST['selected'] ) ? (bool) intval( $_POST['selected'] ) : true;

        if ( $is_checked ) {
            if ( ! in_array( $key, $selected_keys, true ) && in_array( $key, $all_keys, true ) ) {
                $selected_keys[] = $key;
            }
        } else {
            $selected_keys = array_values( array_diff( $selected_keys, [ $key ] ) );
        }
    }

    WC()->session->set( 'bv_selected_cart_items', $selected_keys );
    $summary = bv_get_selected_cart_summary();

    wp_send_json_success( [
        'selected_keys'      => $selected_keys,
        'subtotal_html'      => $summary['subtotal_html'],
        'subtotal_raw'       => $summary['subtotal'],
        'selected_count'     => $summary['count'],
        'total_items'        => $summary['total_items'],
        'all_selected'       => $summary['all_selected'],
        'is_empty_selection' => ( $summary['count'] === 0 ),
        'checkout_text'      => 'Thanh toán (' . $summary['count'] . ')',
    ] );
}

// Stash unselected items when entering Checkout, and Restore when returning to Cart or after order
add_action( 'template_redirect', 'bv_handle_checkout_stash_restore' );
function bv_handle_checkout_stash_restore() {
    if ( ! function_exists( 'WC' ) || ! WC()->session || ! WC()->cart ) {
        return;
    }

    // 1. If on any non-checkout page (Cart, Shop, Home, etc.):
    // Restore any stashed items from an abandoned checkout!
    if ( ! is_checkout() || is_cart() ) {
        $stashed = WC()->session->get( 'bv_stashed_cart_items' );
        if ( ! empty( $stashed ) && is_array( $stashed ) ) {
            foreach ( $stashed as $item ) {
                WC()->cart->add_to_cart(
                    $item['product_id'],
                    $item['quantity'],
                    $item['variation_id'] ?? 0,
                    $item['variation'] ?? [],
                    $item
                );
            }
            WC()->session->__unset( 'bv_stashed_cart_items' );
            WC()->cart->calculate_totals();
        }
    }

    // 2. If on Checkout page (and not order-received/thankyou or pay endpoint):
    if ( is_checkout() && ! is_wc_endpoint_url( 'order-received' ) && ! is_wc_endpoint_url( 'order-pay' ) ) {
        $selected_keys = bv_get_selected_cart_item_keys();
        $cart_items    = WC()->cart->get_cart();

        // If zero items selected, redirect back to cart
        if ( empty( $selected_keys ) && ! empty( $cart_items ) ) {
            wc_add_notice( 'Vui lòng chọn ít nhất một sản phẩm để thanh toán.', 'notice' );
            wp_safe_redirect( wc_get_cart_url() );
            exit;
        }

        $stashed = WC()->session->get( 'bv_stashed_cart_items', [] );
        $has_new_stash = false;

        foreach ( $cart_items as $key => $item ) {
            if ( ! in_array( $key, $selected_keys, true ) ) {
                $stashed[] = $item;
                WC()->cart->remove_cart_item( $key );
                $has_new_stash = true;
            }
        }

        if ( $has_new_stash ) {
            WC()->session->set( 'bv_stashed_cart_items', $stashed );
            WC()->cart->calculate_totals();
        }
    }
}

// 3. After order is placed, restore remaining stashed items so they stay in cart
add_action( 'woocommerce_checkout_order_processed', 'bv_restore_stashed_after_order' );
function bv_restore_stashed_after_order( $order_id ) {
    if ( ! function_exists( 'WC' ) || ! WC()->session || ! WC()->cart ) {
        return;
    }
    $stashed = WC()->session->get( 'bv_stashed_cart_items' );
    if ( ! empty( $stashed ) && is_array( $stashed ) ) {
        foreach ( $stashed as $item ) {
            WC()->cart->add_to_cart(
                $item['product_id'],
                $item['quantity'],
                $item['variation_id'] ?? 0,
                $item['variation'] ?? [],
                $item
            );
        }
        WC()->session->__unset( 'bv_stashed_cart_items' );
        WC()->session->__unset( 'bv_selected_cart_items' );
        WC()->cart->calculate_totals();
    }
}

add_action( 'wp_footer', function () {
    if ( ! function_exists( 'WC' ) || ! WC()->cart ) { return; }
    $account_url = wc_get_page_permalink( 'myaccount' );
    ?>
    <!-- SIDE DRAWER: GIỎ HÀNG -->
    <dialog id="bv-cart-panel" class="bv-shopping-panel bv-side-drawer" aria-labelledby="bv-cart-panel-title">
        <header class="bv-panel-heading">
            <div class="bv-panel-title-group">
                <span class="bv-panel-eyebrow">GỐM THỦ CÔNG</span>
                <h2 id="bv-cart-panel-title">Giỏ hàng của bạn</h2>
            </div>
            <button type="button" class="bv-panel-close" aria-label="Đóng giỏ hàng" title="Đóng">✕</button>
        </header>
        <div class="bv-panel-content-wrap">
            <div class="woocommerce widget_shopping_cart"><div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div></div>
        </div>
        <footer class="bv-panel-footer">
            <a class="bv-panel-continue" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">← Tiếp tục xem gốm</a>
        </footer>
    </dialog>

    <!-- SIDE DRAWER: ĐĂNG NHẬP / TÀI KHOẢN -->
    <dialog id="bv-account-panel" class="bv-shopping-panel bv-side-drawer" aria-labelledby="bv-account-panel-title">
        <header class="bv-panel-heading">
            <div class="bv-panel-title-group">
                <span class="bv-panel-eyebrow">XƯỞNG GỐM MỘC</span>
                <h2 id="bv-account-panel-title"><?php echo is_user_logged_in() ? 'Tài khoản của bạn' : 'Đăng nhập'; ?></h2>
            </div>
            <button type="button" class="bv-panel-close" aria-label="Đóng tài khoản" title="Đóng">✕</button>
        </header>

        <div class="bv-panel-content-wrap">
        <?php if ( is_user_logged_in() ) : ?>
            <div class="bv-panel-user-box">
                <div class="bv-panel-user-initial">
                    <?php echo esc_html( mb_substr( wp_get_current_user()->display_name, 0, 1 ) ); ?>
                </div>
                <div class="bv-panel-user-details">
                    <span class="bv-user-label">Chào mừng trở lại,</span>
                    <strong class="bv-user-name"><?php echo esc_html( wp_get_current_user()->display_name ); ?></strong>
                </div>
            </div>

            <nav class="bv-panel-account-links" aria-label="Quản lý tài khoản">
                <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
                    <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" class="bv-account-nav-item">
                        <span><?php echo esc_html( $label ); ?></span>
                        <span class="bv-nav-arrow" aria-hidden="true">→</span>
                    </a>
                <?php endforeach; ?>
            </nav>
        <?php else : ?>
            <div data-account-view="login" class="bv-account-view">
                <p class="bv-account-lead">Đăng nhập để xem trạng thái đơn hàng gốm và theo dõi lịch hẹn workshop của bạn.</p>

                <form method="post" action="<?php echo esc_url( $account_url ); ?>" class="bv-panel-login">
                    <?php do_action( 'woocommerce_login_form_start' ); ?>
                    <div class="bv-form-field">
                        <label for="bv-panel-username">Email hoặc tên đăng nhập <span class="required">*</span></label>
                        <input id="bv-panel-username" name="username" autocomplete="username" placeholder="Nhập email hoặc tên tài khoản" required>
                    </div>

                    <div class="bv-form-field">
                        <label for="bv-panel-password">Mật khẩu <span class="required">*</span></label>
                        <span class="bv-panel-password">
                            <input id="bv-panel-password" type="password" name="password" autocomplete="current-password" placeholder="Nhập mật khẩu" required>
                            <button type="button" class="bv-password-toggle" aria-label="Hiện mật khẩu" title="Hiện mật khẩu">Hiện</button>
                        </span>
                    </div>

                    <div class="bv-form-aux-row">
                        <label class="bv-panel-remember">
                            <input type="checkbox" name="rememberme" value="forever">
                            <span>Ghi nhớ</span>
                        </label>
                        <button type="button" class="bv-panel-text-button" data-account-switch="recover">Quên mật khẩu?</button>
                    </div>

                    <?php do_action( 'woocommerce_login_form' ); ?>
                    <input type="hidden" name="woocommerce-login-nonce" value="<?php echo esc_attr( wp_create_nonce( 'woocommerce-login' ) ); ?>">
                    <input type="hidden" name="redirect" value="<?php echo esc_url( $account_url ); ?>">
                    <button type="submit" class="bv-panel-primary" name="login" value="1">Đăng nhập</button>
                    <?php do_action( 'woocommerce_login_form_end' ); ?>
                </form>

                <div class="bv-panel-switch">
                    <span>Khách hàng mới?</span>
                    <a href="<?php echo esc_url( $account_url . '#register' ); ?>" class="bv-register-link">Tạo tài khoản</a>
                </div>
            </div>

            <div data-account-view="recover" class="bv-account-view" hidden>
                <p class="bv-account-lead">Vui lòng nhập email tài khoản của bạn. Xưởng gốm sẽ gửi đường dẫn đặt lại mật khẩu mới.</p>

                <form method="post" action="<?php echo esc_url( wc_get_account_endpoint_url( 'lost-password' ) ); ?>" class="bv-panel-login">
                    <div class="bv-form-field">
                        <label for="bv-panel-recover">Email hoặc tên đăng nhập <span class="required">*</span></label>
                        <input id="bv-panel-recover" name="user_login" autocomplete="username" placeholder="Nhập email đăng ký" required>
                    </div>
                    <?php do_action( 'woocommerce_lostpassword_form' ); ?>
                    <input type="hidden" name="wc_reset_password" value="true">
                    <input type="hidden" name="woocommerce-lost-password-nonce" value="<?php echo esc_attr( wp_create_nonce( 'lost_password' ) ); ?>">
                    <button type="submit" class="bv-panel-primary">Khôi phục mật khẩu</button>
                </form>

                <div class="bv-panel-switch">
                    <button type="button" class="bv-panel-back-btn" data-account-switch="login">← Quay lại Đăng nhập</button>
                </div>
            </div>
        <?php endif; ?>
        </div>
    </dialog>
    <?php
}, 30 );

add_action( 'woocommerce_after_cart_table', function () {
    $note = WC()->session ? WC()->session->get( 'bv_cart_note', '' ) : '';
    echo '<div class="bv-cart-note"><label for="bv-cart-note">Ghi chú đơn hàng</label><textarea id="bv-cart-note" name="bv_cart_note" maxlength="2000" rows="3" placeholder="Lời nhắn dành cho cửa hàng...">' . esc_textarea( $note ) . '</textarea><span id="bv-note-status" role="status" aria-live="polite"></span></div>';
} );
add_action( 'woocommerce_cart_updated', function () {
    if ( WC()->session && isset( $_POST['bv_cart_note'], $_POST['woocommerce-cart-nonce'] ) && is_string( $_POST['bv_cart_note'] ) && is_string( $_POST['woocommerce-cart-nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['woocommerce-cart-nonce'] ) ), 'woocommerce-cart' ) ) {
        WC()->session->set( 'bv_cart_note', mb_substr( sanitize_textarea_field( wp_unslash( $_POST['bv_cart_note'] ) ), 0, 2000 ) );
    }
} );
add_action( 'wc_ajax_bv_cart_note', function () {
    check_ajax_referer( 'bv_cart_note', 'nonce' );
    if ( ! WC()->session || ! isset( $_POST['note'] ) || ! is_string( $_POST['note'] ) ) { wp_send_json_error( [], 400 ); }
    $note = sanitize_textarea_field( wp_unslash( $_POST['note'] ) );
    if ( mb_strlen( $note ) > 2000 ) { wp_send_json_error( [], 400 ); }
    WC()->session->set( 'bv_cart_note', $note );
    wp_send_json_success();
} );
add_filter( 'woocommerce_checkout_get_value', function ( $value, $key ) {
    return 'order_comments' === $key && null === $value && WC()->session ? WC()->session->get( 'bv_cart_note', '' ) : $value;
}, 10, 2 );
add_action( 'woocommerce_cart_emptied', function () { if ( WC()->session ) { WC()->session->__unset( 'bv_cart_note' ); } } );

// Customize Proceed to Checkout button on Cart Page to reflect selected count
remove_action( 'woocommerce_proceed_to_checkout', 'woocommerce_button_proceed_to_checkout', 20 );
add_action( 'woocommerce_proceed_to_checkout', function () {
    $summary     = function_exists( 'bv_get_selected_cart_summary' ) ? bv_get_selected_cart_summary() : [ 'count' => ( WC()->cart ? count( WC()->cart->get_cart() ) : 0 ) ];
    $is_disabled = ( empty( $summary['count'] ) );
    ?>
    <a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="checkout-button button alt wc-forward bv-checkout-btn <?php echo $is_disabled ? 'is-disabled' : ''; ?>" <?php echo $is_disabled ? 'aria-disabled="true"' : ''; ?>>
        Tiến hành thanh toán (<span class="bv-btn-count"><?php echo esc_html( $summary['count'] ); ?></span>)
    </a>
    <?php
}, 20 );

add_action( 'woocommerce_proceed_to_checkout', function () {
    echo '<a class="bv-cart-continue" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">Tiếp tục mua hàng</a>';
}, 30 );

add_filter( 'woocommerce_cart_subtotal', function ( $subtotal, $compound, $cart ) {
    if ( ( is_cart() || wp_doing_ajax() ) && function_exists( 'bv_get_selected_cart_summary' ) ) {
        $summary = bv_get_selected_cart_summary();
        return '<span class="bv-selected-subtotal">' . $summary['subtotal_html'] . '</span>';
    }
    return $subtotal;
}, 10, 3 );

add_action( 'woocommerce_register_form_start', function () {
    foreach ( [ 'last_name' => 'Họ', 'first_name' => 'Tên' ] as $key => $label ) {
        $value = isset( $_POST['bv_' . $key] ) && is_string( $_POST['bv_' . $key] ) ? sanitize_text_field( wp_unslash( $_POST['bv_' . $key] ) ) : '';
        echo '<p class="woocommerce-form-row form-row form-row-wide"><label for="bv-reg-' . esc_attr( $key ) . '">' . esc_html( $label ) . '</label><input class="input-text" id="bv-reg-' . esc_attr( $key ) . '" name="bv_' . esc_attr( $key ) . '" autocomplete="' . ( 'first_name' === $key ? 'given-name' : 'family-name' ) . '" maxlength="100" value="' . esc_attr( $value ) . '"></p>';
    }
} );
add_action( 'woocommerce_created_customer', function ( $customer_id ) {
    if ( empty( $_POST['woocommerce-register-nonce'] ) || ! is_string( $_POST['woocommerce-register-nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['woocommerce-register-nonce'] ) ), 'woocommerce-register' ) ) { return; }
    foreach ( [ 'first_name', 'last_name' ] as $key ) {
        if ( isset( $_POST['bv_' . $key] ) && is_string( $_POST['bv_' . $key] ) ) {
            update_user_meta( $customer_id, $key, mb_substr( sanitize_text_field( wp_unslash( $_POST['bv_' . $key] ) ), 0, 100 ) );
        }
    }
} );
