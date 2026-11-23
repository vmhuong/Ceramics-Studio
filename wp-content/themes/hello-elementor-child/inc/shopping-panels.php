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
    wp_enqueue_style( 'bv-shopping-panels', get_stylesheet_directory_uri() . '/assets/shopping-panels.css', [ 'bv-commerce' ], filemtime( __DIR__ . '/../assets/shopping-panels.css' ) );
    wp_enqueue_script( 'bv-shopping-panels', get_stylesheet_directory_uri() . '/assets/shopping-panels.js', [ 'bv-commerce', 'wc-add-to-cart' ], filemtime( __DIR__ . '/../assets/shopping-panels.js' ), true );
    wp_localize_script( 'bv-shopping-panels', 'bvShopping', [ 'noteUrl' => WC_AJAX::get_endpoint( 'bv_cart_note' ), 'nonce' => wp_create_nonce( 'bv_cart_note' ) ] );
}, 50 );

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
add_action( 'woocommerce_proceed_to_checkout', function () {
    echo '<a class="bv-cart-continue" href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '">Tiếp tục mua hàng</a>';
}, 30 );

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
