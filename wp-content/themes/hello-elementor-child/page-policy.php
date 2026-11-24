<?php
/**
 * Template Name: Trang Chính Sách (Policy Template)
 *
 * @package HelloElementorChild
 */

defined( 'ABSPATH' ) || exit;

wp_enqueue_style( 'bv-policy-pages', get_stylesheet_directory_uri() . '/assets/policy-pages.css', [ 'bv-commerce' ], filemtime( __DIR__ . '/assets/policy-pages.css' ) );

get_header();

$current_slug = get_post_field( 'post_name', get_post() );
$policies = [
	'chinh-sach-doi-tra' => [
		'title' => 'Chính sách Đổi trả & Bảo hành Vỡ Mẻ',
		'url'   => home_url( '/chinh-sach-doi-tra/' ),
	],
	'chinh-sach-bao-mat' => [
		'title' => 'Chính sách Bảo mật Thông tin',
		'url'   => home_url( '/chinh-sach-bao-mat/' ),
	],
	'quy-dinh-workshop'  => [
		'title' => 'Quy định Đặt & Hủy lịch Workshop',
		'url'   => home_url( '/quy-dinh-workshop/' ),
	],
];
?>

<main id="content" class="site-main bv-policy-shell">
	<div class="bv-policy-container">
		
		<!-- Breadcrumb -->
		<nav class="bv-policy-breadcrumb" aria-label="Đường dẫn trang">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">Trang chủ</a>
			<span class="bv-sep">/</span>
			<span>Chính sách &amp; Quy định</span>
			<span class="bv-sep">/</span>
			<span class="bv-current"><?php the_title(); ?></span>
		</nav>

		<div class="bv-policy-layout">
			
			<!-- Left Sticky Sidebar -->
			<aside class="bv-policy-sidebar">
				<div class="bv-policy-nav-box">
					<h3 class="bv-policy-nav-title">Danh mục chính sách</h3>
					<ul class="bv-policy-nav-list">
						<?php foreach ( $policies as $slug => $item ) : 
							$is_active = ( $current_slug === $slug );
						?>
							<li class="bv-policy-nav-item">
								<a href="<?php echo esc_url( $item['url'] ); ?>" class="bv-policy-nav-link <?php echo $is_active ? 'is-active' : ''; ?>">
									<span><?php echo esc_html( $item['title'] ); ?></span>
									<span class="bv-arrow" aria-hidden="true">→</span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>

				<div class="bv-policy-support-box">
					<h4>Cần hỗ trợ trực tiếp?</h4>
					<p>Nếu bạn cần giải đáp nhanh về đơn hàng gốm hoặc đổi ca workshop, nghệ nhân luôn sẵn sàng hỗ trợ bạn.</p>
					<a href="https://zalo.me/0363292271" target="_blank" rel="noopener" class="bv-policy-zalo-btn">
						Tư vấn Zalo: 0363 292 271
					</a>
				</div>
			</aside>

			<!-- Right Main Content -->
			<article class="bv-policy-content-card">
				<header class="bv-policy-header">
					<span class="bv-policy-eyebrow">XƯỞNG GỐM MỘC CERAMIC STUDIO</span>
					<h1 class="bv-policy-title"><?php the_title(); ?></h1>
					<div class="bv-policy-meta">
						<span>Cập nhật: Tháng <?php echo date( 'm/Y' ); ?> • Áp dụng trên toàn quốc</span>
					</div>
				</header>

				<div class="bv-policy-body">
					<?php
					while ( have_posts() ) :
						the_post();
						the_content();
					endwhile;
					?>
				</div>
			</article>

		</div>
	</div>
</main>

<?php
get_footer();
