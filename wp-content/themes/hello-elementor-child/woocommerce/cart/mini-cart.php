<?php
/**
 * Ceramic Studio Luxury Mini-Cart Template
 * Overrides WooCommerce default templates/cart/mini-cart.php
 *
 * @package HelloElementorChild
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_mini_cart' ); ?>

<?php if ( WC()->cart && ! WC()->cart->is_empty() ) : 
	$cart_summary = function_exists( 'bv_get_selected_cart_summary' ) ? bv_get_selected_cart_summary() : [ 'all_selected' => true, 'count' => count( WC()->cart->get_cart() ), 'subtotal_html' => WC()->cart->get_cart_subtotal() ];
	$cart_items   = WC()->cart->get_cart();
	$total_count  = count( $cart_items );
?>

	<!-- Select All Toolbar -->
	<div class="bv-mcart-select-all-bar">
		<label class="bv-cart-checkbox-label">
			<input type="checkbox" class="bv-cart-select-all-checkbox" <?php checked( ! empty( $cart_summary['all_selected'] ) ); ?> aria-label="Chọn tất cả sản phẩm">
			<span class="bv-cart-custom-check" aria-hidden="true"></span>
			<span class="bv-select-all-text">Chọn tất cả (<strong><?php echo esc_html( $total_count ); ?></strong> món)</span>
		</label>
	</div>

	<ul class="woocommerce-mini-cart cart_list product_list_widget <?php echo esc_attr( $args['list_class'] ?? '' ); ?>">
		<?php
		do_action( 'woocommerce_before_mini_cart_contents' );

		foreach ( $cart_items as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

			$visible = apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key );

			if ( $_product instanceof WC_Product && $_product->exists() && $cart_item['quantity'] > 0 && $visible ) {
				$is_selected       = function_exists( 'bv_is_cart_item_selected' ) ? bv_is_cart_item_selected( $cart_item_key ) : true;
				$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
				$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail' ), $cart_item, $cart_item_key );
				$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				$line_price_raw    = (float) $_product->get_price() * $cart_item['quantity'];
				$line_subtotal     = wc_price( $line_price_raw );
				$max_qty           = $_product->get_max_purchase_quantity();
				$max_qty_attr      = $max_qty > 0 ? 'max="' . esc_attr( $max_qty ) . '"' : 'max="99"';
				?>
				<li class="woocommerce-mini-cart-item bv-mini-cart-card mini_cart_item <?php echo $is_selected ? '' : 'is-unselected'; ?>" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>">
					<!-- Selection Checkbox -->
					<div class="bv-mcart-item-select">
						<label class="bv-cart-checkbox-label">
							<input type="checkbox" class="bv-cart-item-checkbox" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>" <?php checked( $is_selected ); ?> aria-label="Chọn thanh toán món này">
							<span class="bv-cart-custom-check" aria-hidden="true"></span>
						</label>
					</div>

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

						<!-- Compact Independent Attribute Pickers -->
						<?php
						$is_variation = ! empty( $cart_item['variation_id'] );
						if ( $is_variation ) {
							$parent_product = wc_get_product( $cart_item['product_id'] );
							$var_attributes = ( $parent_product && $parent_product->is_type( 'variable' ) ) ? $parent_product->get_variation_attributes() : [];
							$available_vars = ( $parent_product && $parent_product->is_type( 'variable' ) ) ? $parent_product->get_available_variations() : [];
							
							$vars_json = [];
							foreach ( $available_vars as $v ) {
								$vars_json[] = [
									'id'         => $v['variation_id'],
									'attributes' => $v['attributes'],
									'price'      => $v['display_price'],
									'price_html' => wp_strip_all_tags( wc_price( $v['display_price'] ) ),
								];
							}
							
							$attr_fields = [];
							foreach ( $var_attributes as $attr_name => $options ) {
								$matched_key = '';
								$current_val = '';
								$candidates = [
									'attribute_' . sanitize_title( $attr_name ),
									'attribute_' . $attr_name,
									sanitize_title( $attr_name ),
									$attr_name,
								];
								foreach ( $candidates as $cand ) {
									if ( isset( $cart_item['variation'][ $cand ] ) ) {
										$matched_key = $cand;
										$current_val = $cart_item['variation'][ $cand ];
										break;
									}
								}
								if ( ! $matched_key && ! empty( $available_vars ) ) {
									foreach ( array_keys( $available_vars[0]['attributes'] ) as $k ) {
										if ( strpos( $k, sanitize_title( $attr_name ) ) !== false ) {
											$matched_key = $k;
											if ( isset( $cart_item['variation'][ $k ] ) ) {
												$current_val = $cart_item['variation'][ $k ];
											}
											break;
										}
									}
								}
								if ( ! $matched_key ) {
									$matched_key = 'attribute_' . sanitize_title( $attr_name );
								}
								if ( ! $current_val && ! empty( $options ) ) {
									$current_val = $options[0];
								}
								$attr_fields[] = [
									'key'     => $matched_key,
									'label'   => wc_attribute_label( $attr_name, $parent_product ),
									'current' => $current_val,
									'options' => $options,
								];
							}
							?>
							<?php if ( ! empty( $attr_fields ) ) : ?>
								<div class="bv-mcart-attr-rows" data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>" data-product_id="<?php echo esc_attr( $product_id ); ?>" data-variations="<?php echo esc_attr( wp_json_encode( $vars_json ) ); ?>">
									<?php foreach ( $attr_fields as $field ) : ?>
										<div class="bv-mcart-attr-row">
											<span class="bv-mcart-attr-label"><?php echo esc_html( $field['label'] ); ?>:</span>
											<div class="bv-mcart-attr-picker" title="Bấm để chọn <?php echo esc_attr( strtolower( $field['label'] ) ); ?> khác">
												<span class="bv-mcart-attr-badge">
													<span class="bv-mcart-attr-val"><?php echo esc_html( $field['current'] ); ?></span>
													<span class="bv-mcart-attr-caret" aria-hidden="true">▾</span>
												</span>
												<select class="bv-mcart-attr-select" data-attr-key="<?php echo esc_attr( $field['key'] ); ?>" aria-label="Chọn <?php echo esc_attr( $field['label'] ); ?>">
													<?php foreach ( $field['options'] as $opt ) : ?>
														<option value="<?php echo esc_attr( $opt ); ?>" <?php selected( $opt, $field['current'] ); ?>>
															<?php echo esc_html( $opt ); ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
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
		<strong>Tạm tính:</strong>
		<span class="woocommerce-Price-amount amount bv-selected-subtotal"><?php echo $cart_summary['subtotal_html']; ?></span>
	</p>

	<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

	<p class="woocommerce-mini-cart__buttons buttons">
		<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="button wc-forward bv-view-cart-btn">Xem giỏ hàng</a>
		<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="button checkout wc-forward bv-checkout-btn <?php echo ( $cart_summary['count'] === 0 ) ? 'is-disabled' : ''; ?>" <?php echo ( $cart_summary['count'] === 0 ) ? 'aria-disabled="true"' : ''; ?>>
			Thanh toán (<span class="bv-btn-count"><?php echo esc_html( $cart_summary['count'] ); ?></span>)
		</a>
	</p>

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
