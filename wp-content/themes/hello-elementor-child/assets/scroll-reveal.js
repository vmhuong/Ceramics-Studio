/**
 * Ceramic Studio - Smooth Scroll Reveal Controller
 * Aesthetic: Wabi-Sabi Luxury Craft Minimalism
 *
 * Automatically attaches smooth scroll-reveal animations to key sections,
 * cards, headers and editorial blocks across Homepage and all Static Pages.
 */

(function () {
	'use strict';

	// Target selectors for scroll reveal
	var TARGET_SELECTORS = [
		// 1. Homepage Sections & Components
		'.bv-craft-intro',
		'.bv-steps-column .bv-step-item',
		'.bv-testimonials-grid .bv-review-card',
		'.bv-promises-grid .bv-promise-item',
		'.bv-ws-box',
		'.bv-collections-grid .bv-col-card',
		'.bv-ceramic-grid .bv-product-card',
		'.bv-hero-content',
		'.bv-hero-visual',
		'.bv-section-header',

		// 2. About Us Page (Về Xưởng Gốm)
		'.bv-about-hero-header',
		'.bv-about-hero-banner',
		'.bv-story-media',
		'.bv-story-content',
		'.bv-process-grid .bv-process-card',
		'.bv-values-grid .bv-value-card',
		'.bv-about-cta-box',

		// 3. Workshop Page (Trải Nghiệm Làm Gốm)
		'.bv-ws-hero-inner',
		'.bv-ws-hero-banner',
		'.bv-benefits-grid .bv-benefit-card',
		'.bv-ws-booking-section',
		'.bv-faq-grid .bv-faq-card',
		'.bv-ws-contact-bar',

		// 4. Contact Us Page (Liên Hệ Xưởng Gốm)
		'.bv-contact-hero-inner',
		'.bv-contact-info-card',
		'.bv-info-items .bv-info-item',
		'.bv-contact-form-card',

		// 5. Product Detail Page & Shop
		'.bv-sp-editorial-tabs',
		'.bv-reviews-container',
		'.bv-sp-related-section',
		'.bv-sp-related-grid .bv-product-card',

		// 6. Generic / Custom Marker
		'.bv-reveal'
	];

	// Containers whose direct target children should be staggered
	var STAGGER_CONTAINER_SELECTORS = [
		'.bv-steps-column',
		'.bv-testimonials-grid',
		'.bv-promises-grid',
		'.bv-collections-grid',
		'.bv-ceramic-grid',
		'.bv-products-grid',
		'.bv-process-grid',
		'.bv-values-grid',
		'.bv-benefits-grid',
		'.bv-faq-grid',
		'.bv-info-items',
		'.bv-sp-related-grid'
	];

	function initScrollReveal() {
		// Do not run inside Elementor Editor preview / edit mode
		if (document.body.classList.contains('elementor-editor-active')) {
			return;
		}

		var root = document.documentElement;

		// Fallback for browsers without IntersectionObserver
		if (!('IntersectionObserver' in window)) {
			return;
		}

		// Activate reveal styling on document
		root.classList.add('bv-reveal-ready');

		// Query all target elements
		var selectorString = TARGET_SELECTORS.join(', ');
		var elements = Array.prototype.slice.call(document.querySelectorAll(selectorString));

		// Filter out elements already observed or revealed
		elements = elements.filter(function (el) {
			return !el.classList.contains('is-revealed') && !el.classList.contains('bv-observing');
		});

		if (!elements.length) {
			return;
		}

		// Auto-assign stagger delays for grid/list children
		STAGGER_CONTAINER_SELECTORS.forEach(function (cSel) {
			var containers = document.querySelectorAll(cSel);
			containers.forEach(function (container) {
				var children = Array.prototype.slice.call(container.children);
				var validIndex = 0;
				children.forEach(function (child) {
					if (elements.indexOf(child) !== -1 || child.matches(selectorString)) {
						validIndex++;
						if (validIndex <= 8) {
							child.classList.add('bv-reveal-delay-' + validIndex);
						}
					}
				});
			});
		});

		// Create IntersectionObserver
		var observer = new IntersectionObserver(
			function (entries, obs) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						var target = entry.target;
						target.classList.add('is-revealed');
						obs.unobserve(target);

						// Compute total transition time (base duration 750ms + transition-delay)
						var computed = window.getComputedStyle(target);
						var delaySec = parseFloat(computed.transitionDelay) || 0;
						var totalWaitMs = Math.round((0.75 + delaySec) * 1000) + 100;

						// After animation finishes, free transform so native hover transitions take over
						setTimeout(function () {
							target.classList.add('reveal-done');
						}, totalWaitMs);
					}
				});
			},
			{
				root: null,
				rootMargin: '0px 0px -40px 0px', // Trigger slightly before it hits the bottom
				threshold: 0.08
			}
		);

		// Observe each element
		elements.forEach(function (el) {
			// Ensure it has base class
			el.classList.add('bv-reveal');
			el.classList.add('bv-observing');
			observer.observe(el);
		});
	}

	// Expose globally for dynamic components or AJAX
	window.bvRefreshScrollReveal = initScrollReveal;

	// Run on DOM ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initScrollReveal);
	} else {
		initScrollReveal();
	}
})();
