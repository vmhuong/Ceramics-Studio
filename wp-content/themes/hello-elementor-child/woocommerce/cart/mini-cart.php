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

						<!-- Professional Variant Display & Inline Switcher -->
						<?php
						$is_variation = ! empty( $cart_item['variation_id'] );
						if ( $is_variation ) {
							$parent_product = wc_get_product( $cart_item['product_id'] );
							$available_vars = ( $parent_product && $parent_product->is_type( 'variable' ) ) ? $parent_product->get_available_variations() : [];
							
							$current_attr_parts = [];
							if ( ! empty( $cart_item['variation'] ) ) {
								foreach ( $cart_item['variation'] as $attr_k => $attr_v ) {
									$taxonomy = str_replace( 'attribute_', '', $attr_k );
									$label = wc_attribute_label( $taxonomy );
									$current_attr_parts[] = $label . ': ' . $attr_v;
								}
							}
							$current_attr_str = implode( ' • ', $current_attr_parts );
							?>
							<div class="bv-mcart-variant-row" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>">
								<?php if ( ! empty( $available_vars ) && count( $available_vars ) > 1 ) : ?>
									<div class="bv-mcart-variant-picker-wrap">
										<label class="bv-mcart-variant-picker-label" for="bv-var-select-<?php echo esc_attr( $cart_item_key ); ?>" title="Bấm để chọn phân loại khác">
											<span class="bv-mcart-variant-pill">
												<span class="bv-mcart-var-text"><?php echo esc_html( $current_attr_str ); ?></span>
												<span class="bv-mcart-var-arrow" aria-hidden="true">▾</span>
											</span>
										</label>
										<select id="bv-var-select-<?php echo esc_attr( $cart_item_key ); ?>" class="bv-mcart-variant-select" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>" data-product_id="<?php echo esc_attr( $product_id ); ?>" aria-label="Đổi phân loại sản phẩm">
											<?php foreach ( $available_vars as $v ) : 
												$v_id = $v['variation_id'];
												$v_attrs = [];
												foreach ( $v['attributes'] as $attr_key => $attr_val ) {
													$lbl = wc_attribute_label( str_replace( 'attribute_', '', $attr_key ) );
													$v_attrs[] = $lbl . ': ' . $attr_val;
												}
												$v_label = implode( ' • ', $v_attrs );
												$v_price = wc_price( $v['display_price'] );
												$is_current = ( (int) $v_id === (int) $cart_item['variation_id'] );
											?>
												<option value="<?php echo esc_attr( $v_id ); ?>" <?php selected( $is_current ); ?>>
													<?php echo esc_html( $v_label ); ?> (<?php echo wp_strip_all_tags( $v_price ); ?>)<?php echo $is_current ? ' ✓' : ''; ?>
												</option>
											<?php endforeach; ?>
										</select>
									</div>
								<?php else : ?>
									<div class="bv-mcart-variant-pill bv-mcart-variant-pill--static">
										<span class="bv-mcart-var-text"><?php echo esc_html( $current_attr_str ); ?></span>
									</div>
								<?php endif; ?>
							</div>
							<?php
						} else {
							echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>

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
