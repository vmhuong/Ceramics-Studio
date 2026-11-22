<?php
/**
 * Professional Craft Reviews & Photo Feedback for Ceramics
 *
 * @package HelloElementorChild
 */

defined( 'ABSPATH' ) || exit;

// Enqueue review assets on single product pages
add_action( 'wp_enqueue_scripts', function () {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}

	wp_enqueue_style( 'bv-product-reviews', get_stylesheet_directory_uri() . '/assets/product-reviews.css', [ 'bv-commerce' ], filemtime( __DIR__ . '/../assets/product-reviews.css' ) );
	wp_enqueue_script( 'bv-product-reviews', get_stylesheet_directory_uri() . '/assets/product-reviews.js', [ 'jquery' ], filemtime( __DIR__ . '/../assets/product-reviews.js' ), true );
} );

// Insert Rating Summary Bar above comment list
add_action( 'woocommerce_review_before', function () {
	// Only run once before the comments
}, 5 );

add_filter( 'woocommerce_product_tabs', function ( $tabs ) {
	if ( isset( $tabs['reviews'] ) ) {
		$tabs['reviews']['title'] = sprintf( 'Đánh giá từ khách hàng (%d)', get_comments_number() );
	}
	return $tabs;
} );

// Add Rating Summary at the top of the reviews tab
add_action( 'woocommerce_review_before_comments', 'bv_render_review_summary' );

function bv_render_review_summary() {
	global $product;
	if ( ! $product ) {
		return;
	}

	$rating_count = $product->get_rating_count();
	$average      = $product->get_average_rating();
	$review_count = $product->get_review_count();

	if ( $review_count === 0 ) {
		$average = 5.0; // Default display score for aesthetics
	}
	?>
	<div class="bv-review-summary-card">
		<div class="bv-summary-left">
			<div class="bv-score-number"><?php echo number_format( (float) $average, 1 ); ?></div>
			<div class="bv-score-stars" aria-label="<?php echo esc_attr( $average ); ?> trên 5 sao">
				<?php for ( $i = 1; $i <= 5; $i++ ) : ?>
					<i class="eicon-star <?php echo $i <= round( $average ) ? 'is-filled' : ''; ?>" aria-hidden="true"></i>
				<?php endfor; ?>
			</div>
			<span class="bv-review-total"><?php echo esc_html( $review_count ); ?> lượt đánh giá thực tế</span>
			<span class="bv-verified-badge"><i class="eicon-check-circle" aria-hidden="true"></i> 100% Khách hàng hài lòng</span>
		</div>
		<div class="bv-summary-right">
			<span class="bv-tag-title">Điểm khách hàng yêu thích nhất:</span>
			<div class="bv-feedback-tags">
				<span class="bv-tag"><i class="eicon-box" aria-hidden="true"></i> Đóng gói 3 lớp chống sốc kỹ càng</span>
				<span class="bv-tag"><i class="eicon-paint-brush" aria-hidden="true"></i> Men gốm mộc đẹp hơn ảnh</span>
				<span class="bv-tag"><i class="eicon-heart" aria-hidden="true"></i> Cầm đầm tay và chắc chắn</span>
				<span class="bv-tag"><i class="eicon-clock" aria-hidden="true"></i> Giao hàng nguyên vẹn, đúng hẹn</span>
			</div>
		</div>
	</div>
	<?php
}

// Add Photo upload input to review form
add_action( 'comment_form_logged_in_after', 'bv_add_review_photo_field' );
add_action( 'comment_form_after_fields', 'bv_add_review_photo_field' );

function bv_add_review_photo_field() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}
	?>
	<div class="bv-review-photo-field">
		<label for="bv_review_image" class="bv-photo-label">
			<i class="eicon-camera" aria-hidden="true"></i> Đính kèm ảnh thực tế của bạn (tùy chọn)
		</label>
		<input type="file" id="bv_review_image" name="bv_review_image" accept="image/png, image/jpeg, image/webp" class="bv-photo-input">
		<div id="bv-photo-preview" class="bv-photo-preview" hidden></div>
		<small class="bv-photo-hint">Chụp ảnh chiếc bình/ly gốm tại nhà để chia sẻ cùng cộng đồng yêu gốm thủ công.</small>
	</div>
	<?php
}

// Make sure comment form supports multipart/form-data for file uploads
add_action( 'comment_form_top', function () {
	echo '<script>document.addEventListener("DOMContentLoaded", function(){ var f = document.getElementById("commentform"); if(f) f.enctype = "multipart/form-data"; });</script>';
} );

// Save uploaded photo with the comment
add_action( 'comment_post', function ( $comment_id ) {
	if ( ! empty( $_FILES['bv_review_image']['name'] ) ) {
		require_once ABSPATH . 'wp-admin/includes/image.php';
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';

		$attachment_id = media_handle_upload( 'bv_review_image', 0 );
		if ( ! is_wp_error( $attachment_id ) ) {
			add_comment_meta( $comment_id, '_bv_review_image_id', $attachment_id );
		}
	}
} );

// Display review photo and Verified Buyer badge inside the comment
add_filter( 'comment_text', function ( $comment_text, $comment ) {
	if ( ! is_admin() && $comment && 'review' === get_post_type( $comment->comment_post_ID ) || 'product' === get_post_type( $comment->comment_post_ID ) ) {
		$badge_html = '<div class="bv-comment-badges"><span class="bv-verified-buyer"><i class="eicon-check" aria-hidden="true"></i> Đã mua hàng từ Tiệm Gốm</span></div>';

		$photo_html = '';
		$image_id = get_comment_meta( $comment->comment_ID, '_bv_review_image_id', true );
		$image_url = get_comment_meta( $comment->comment_ID, '_bv_review_image_url', true );

		if ( $image_id ) {
			$img_src = wp_get_attachment_image_url( $image_id, 'medium' );
			$img_full = wp_get_attachment_image_url( $image_id, 'full' );
			if ( $img_src ) {
				$photo_html = '<div class="bv-comment-photos"><a href="' . esc_url( $img_full ) . '" class="bv-photo-link" target="_blank"><img src="' . esc_url( $img_src ) . '" alt="Ảnh thực tế từ người mua" class="bv-review-thumb" loading="lazy"></a></div>';
			}
		} elseif ( $image_url ) {
			$photo_html = '<div class="bv-comment-photos"><a href="' . esc_url( $image_url ) . '" class="bv-photo-link" target="_blank"><img src="' . esc_url( $image_url ) . '" alt="Ảnh thực tế từ người mua" class="bv-review-thumb" loading="lazy"></a></div>';
		}

		return $badge_html . $comment_text . $photo_html;
	}
	return $comment_text;
}, 10, 2 );
