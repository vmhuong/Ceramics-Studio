/**
 * Wabi-Sabi Header & Footer Interactive Controller
 * Project: Ceramic Studio (Bookverse)
 */

(function () {
	'use strict';

	function initHeaderFooter() {
		var header = document.getElementById('site-header');
		var mobileToggle = document.querySelector('.bv-mobile-toggle');
		var mobileDrawer = document.getElementById('bv-mobile-drawer');
		var drawerClose = document.querySelector('.bv-drawer-close');
		var drawerBackdrop = document.querySelector('.bv-drawer-backdrop');
		var backToTop = document.getElementById('bv-back-to-top');

		// 1. MOBILE DRAWER NAVIGATION
		function openDrawer() {
			if (!mobileDrawer) return;
			mobileDrawer.classList.add('is-open');
			mobileDrawer.setAttribute('aria-hidden', 'false');
			if (mobileToggle) mobileToggle.setAttribute('aria-expanded', 'true');
			document.body.style.overflow = 'hidden';
		}

		function closeDrawer() {
			if (!mobileDrawer) return;
			mobileDrawer.classList.remove('is-open');
			mobileDrawer.setAttribute('aria-hidden', 'true');
			if (mobileToggle) mobileToggle.setAttribute('aria-expanded', 'false');
			document.body.style.overflow = '';
		}

		if (mobileToggle) {
			mobileToggle.addEventListener('click', function (e) {
				e.preventDefault();
				if (mobileDrawer && mobileDrawer.classList.contains('is-open')) {
					closeDrawer();
				} else {
					openDrawer();
				}
			});
		}

		if (drawerClose) {
			drawerClose.addEventListener('click', function (e) {
				e.preventDefault();
				closeDrawer();
			});
		}

		if (drawerBackdrop) {
			drawerBackdrop.addEventListener('click', function () {
				closeDrawer();
			});
		}

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && mobileDrawer && mobileDrawer.classList.contains('is-open')) {
				closeDrawer();
			}
		});

		// 2. STICKY HEADER & SCROLL BEHAVIOR
		var scheduled = false;
		function onScroll() {
			var scrollY = window.pageYOffset || document.documentElement.scrollTop;

			// Header Sticky & Compact
			if (header) {
				if (scrollY > 15) {
					header.classList.add('is-scrolled');
				} else {
					header.classList.remove('is-scrolled');
				}

				if (scrollY > 100) {
					header.classList.add('is-compact');
				} else {
					header.classList.remove('is-compact');
				}
			}

			// Back to Top Button
			if (backToTop) {
				if (scrollY > 350) {
					backToTop.classList.add('is-visible');
				} else {
					backToTop.classList.remove('is-visible');
				}
			}
		}

		window.addEventListener('scroll', function () {
			if (scheduled) return;
			scheduled = true;
			requestAnimationFrame(function () {
				onScroll();
				scheduled = false;
			});
		}, { passive: true });

		// Initial check
		onScroll();

		// 3. BACK TO TOP CLICK
		if (backToTop) {
			backToTop.addEventListener('click', function (e) {
				e.preventDefault();
				window.scrollTo({
					top: 0,
					behavior: 'smooth'
				});
			});
		}

		// 4. WOOCOMMERCE AJAX CART FRAGMENTS SYNC
		if (window.jQuery) {
			window.jQuery(document.body).on('added_to_cart removed_from_cart wc_fragments_refreshed', function (e, fragments) {
				if (fragments && fragments['.bv-cart-count']) {
					var countEl = document.querySelector('.bv-cart-count');
					if (countEl) {
						var parsed = document.createElement('div');
						parsed.innerHTML = fragments['.bv-cart-count'];
						var newCount = parsed.textContent.trim();
						countEl.textContent = newCount;
					}
				}
			});
		}

		// 5. HERO VISUAL AUTO SLIDER (4-second interval with cross-fade & description update)
		var heroSlider = document.getElementById('bv-hero-slider');
		if (heroSlider) {
			var slides = heroSlider.querySelectorAll('.bv-hero-slide');
			var dots = heroSlider.querySelectorAll('.bv-hero-dot');
			var badgeInner = document.getElementById('bv-hero-badge-inner');
			var badgeTitle = document.getElementById('bv-hero-badge-title');
			var badgeDesc = document.getElementById('bv-hero-badge-desc');
			var currentIdx = 0;
			var total = slides.length;
			var autoTimer = null;
			var INTERVAL = 4000; // 4 seconds per user request

			function switchSlide(nextIdx) {
				if (nextIdx === currentIdx || nextIdx < 0 || nextIdx >= total) return;

				// Change slides
				slides[currentIdx].classList.remove('is-active');
				slides[nextIdx].classList.add('is-active');

				// Change dots
				if (dots.length) {
					dots[currentIdx].classList.remove('is-active');
					dots[nextIdx].classList.add('is-active');
				}

				// Change floating badge with smooth transition
				if (badgeInner && badgeTitle && badgeDesc) {
					badgeInner.classList.add('is-changing');
					setTimeout(function () {
						var targetSlide = slides[nextIdx];
						badgeTitle.textContent = targetSlide.getAttribute('data-title') || '';
						badgeDesc.textContent = targetSlide.getAttribute('data-desc') || '';
						badgeInner.classList.remove('is-changing');
					}, 220);
				}

				currentIdx = nextIdx;
			}

			function nextSlide() {
				var next = (currentIdx + 1) % total;
				switchSlide(next);
			}

			function startTimer() {
				stopTimer();
				autoTimer = setInterval(nextSlide, INTERVAL);
			}

			function stopTimer() {
				if (autoTimer) {
					clearInterval(autoTimer);
					autoTimer = null;
				}
			}

			// Dot click handlers
			dots.forEach(function (dot) {
				dot.addEventListener('click', function () {
					var idx = parseInt(this.getAttribute('data-index'), 10);
					if (!isNaN(idx)) {
						switchSlide(idx);
						startTimer();
					}
				});
			});

			// Pause on hover
			heroSlider.addEventListener('mouseenter', stopTimer);
			heroSlider.addEventListener('mouseleave', startTimer);

			// Start timer
			startTimer();
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initHeaderFooter);
	} else {
		initHeaderFooter();
	}
})();
