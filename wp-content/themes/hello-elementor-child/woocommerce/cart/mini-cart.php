<?php
/**
 * Ceramic Studio Luxury Mini-Cart Template
 * Overrides WooCommerce default templates/cart/mini-cart.php
 *
 * @package HelloElementorChild
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' ); ?>

<?php if ( WC()->cart && ! WC()->cart->is_empty() ) : ?>

	<ul class="woocommerce-mini-cart cart_list product_list_widget <?php echo esc_attr( $args['list_class'] ?? '' ); ?>">
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			$visible = apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key );

			if ( $_product instanceof WC_Product && $_product->exists() && $cart_item['quantity'] > 0 && $visible ) {
				$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
				$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );
				$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				$line_price_raw    = (float) $_product->get_price() * $cart_item['quantity'];
				$line_subtotal     = wc_price( $line_price_raw );
				$max_qty           = $_product->get_max_purchase_quantity();
				$max_qty_attr      = $max_qty > 0 ? 'max="' . esc_attr( $max_qty ) . '"' : 'max="99"';
				?>
				<li class="woocommerce-mini-cart-item bv-mini-cart-card mini_cart_item" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>">
					<!-- Thumbnail -->
					<div class="bv-mcart-thumb">
						<?php if ( empty( $product_permalink ) ) : ?>
							<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php else : ?>
							<a href="<?php echo esc_url( $product_permalink ); ?>" tabindex="-1">
								<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php endif; ?>
					</div>

					<!-- Details Body -->
					<div class="bv-mcart-details">
						<div class="bv-mcart-header-line">
							<h4 class="bv-mcart-title">
								<?php if ( empty( $product_permalink ) ) : ?>
									<?php echo esc_html( wp_strip_all_tags( $product_name ) ); ?>
								<?php else : ?>
									<a href="<?php echo esc_url( $product_permalink ); ?>">
										<?php echo esc_html( wp_strip_all_tags( $product_name ) ); ?>
									</a>
								<?php endif; ?>
							</h4>

							<!-- Remove button -->
							<?php
							echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								'woocommerce_cart_item_remove_link',
								sprintf(
									'<a role="button" href="%s" class="remove remove_from_cart_button bv-mcart-remove" aria-label="%s" data-product_id="%s" data-cart_item_key="%s" data-product_sku="%s" title="Xóa món này">&times;</a>',
									esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
									/* translators: %s is the product name */
									esc_attr( sprintf( __( 'Remove %s from cart', 'woocommerce' ), wp_strip_all_tags( $product_name ) ) ),
									esc_attr( $product_id ),
									esc_attr( $cart_item_key ),
									esc_attr( $_product->get_sku() )
								),
								$cart_item_key
							);
							?>
						</div>

						<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

						<!-- Price & Quantity Stepper Controls -->
						<div class="bv-mcart-controls-line">
							<div class="bv-mcart-qty-stepper" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>">
								<button type="button" class="bv-mcart-qty-btn bv-mcart-qty-btn--minus" data-action="minus" aria-label="Giảm số lượng">−</button>
								<input type="number" class="bv-mcart-qty-input" value="<?php echo esc_attr( $cart_item['quantity'] ); ?>" min="1" <?php echo $max_qty_attr; ?> step="1" inputmode="numeric" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>" aria-label="Số lượng">
								<button type="button" class="bv-mcart-qty-btn bv-mcart-qty-btn--plus" data-action="plus" aria-label="Tăng số lượng">+</button>
							</div>

							<div class="bv-mcart-price-box">
								<span class="bv-mcart-total-price"><?php echo $line_subtotal; ?></span>
								<?php if ( $cart_item['quantity'] > 1 ) : ?>
									<span class="bv-mcart-unit-price"><?php echo $product_price; ?>/món</span>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</li>
				<?php
			}
		}

		do_action( 'woocommerce_mini_cart_contents' );
		?>
	</ul>

	<p class="woocommerce-mini-cart__total total">
		<?php
		/**
		 * Hook: woocommerce_widget_shopping_cart_total.
		 *
		 * @hooked woocommerce_widget_shopping_cart_subtotal - 10
		 */
		do_action( 'woocommerce_widget_shopping_cart_total' );
		?>
	</p>

	<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

	<p class="woocommerce-mini-cart__buttons buttons"><?php do_action( 'woocommerce_widget_shopping_cart_buttons' ); ?></p>

	<?php do_action( 'woocommerce_widget_shopping_cart_after_buttons' ); ?>

<?php else : ?>

	<div class="bv-mini-cart-empty">
		<p class="woocommerce-mini-cart__empty-message">Chưa có sản phẩm nào trong giỏ hàng của bạn.</p>
		<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="bv-panel-primary">
			Khám phá tác phẩm gốm
		</a>
	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>
