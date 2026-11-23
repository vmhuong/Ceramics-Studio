/**
 * Ceramic Shop & Single Product Interactions
 * Project: Ceramic Studio (Bookverse)
 * Author: Vu Manh Huong (PM28.11)
 */

(function () {
	'use strict';

	function initCeramicShop() {
		// =====================================================================
		// 1. SINGLE PRODUCT GALLERY THUMBNAILS SWITCHER
		// =====================================================================
		var mainImg = document.getElementById('bv-main-product-img');
		var thumbBtns = document.querySelectorAll('.bv-thumb-item');

		if (mainImg && thumbBtns.length > 0) {
			thumbBtns.forEach(function (btn) {
				btn.addEventListener('click', function (e) {
					e.preventDefault();
					var largeSrc = btn.getAttribute('data-large-src');
					if (!largeSrc || mainImg.src === largeSrc) return;

					// Update active thumb
					thumbBtns.forEach(function (b) { b.classList.remove('is-active'); });
					btn.classList.add('is-active');

					// Smooth image fade
					mainImg.style.opacity = '0.3';
					setTimeout(function () {
						mainImg.src = largeSrc;
						mainImg.onload = function () {
							mainImg.style.opacity = '1';
						};
					}, 150);
				});
			});
		}

		// =====================================================================
		// 2. LIGHTBOX ZOOM MODAL
		// =====================================================================
		var zoomBtn = document.getElementById('bv-zoom-btn');
		var modal = document.getElementById('bv-image-modal');
		var modalImg = document.getElementById('bv-modal-img');
		var modalClose = document.getElementById('bv-modal-close');
		var modalOverlay = document.querySelector('.bv-modal-overlay');

		function openModal() {
			if (!modal || !modalImg || !mainImg) return;
			modalImg.src = mainImg.src;
			modal.classList.add('is-open');
			modal.setAttribute('aria-hidden', 'false');
			document.body.style.overflow = 'hidden';
		}

		function closeModal() {
			if (!modal) return;
			modal.classList.remove('is-open');
			modal.setAttribute('aria-hidden', 'true');
			document.body.style.overflow = '';
		}

		if (zoomBtn) {
			zoomBtn.addEventListener('click', function (e) {
				e.preventDefault();
				openModal();
			});
		}

		if (modalClose) {
			modalClose.addEventListener('click', function (e) {
				e.preventDefault();
				closeModal();
			});
		}

		if (modalOverlay) {
			modalOverlay.addEventListener('click', closeModal);
		}

		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && modal && modal.classList.contains('is-open')) {
				closeModal();
			}
		});

		// =====================================================================
		// 3. QUANTITY PLUS & MINUS CONTROLLERS
		// =====================================================================
		var qtyInput = document.getElementById('bv-quantity');
		var minusBtn = document.querySelector('.bv-qty-minus');
		var plusBtn = document.querySelector('.bv-qty-plus');

		if (qtyInput) {
			if (minusBtn) {
				minusBtn.addEventListener('click', function (e) {
					e.preventDefault();
					var val = parseInt(qtyInput.value, 10) || 1;
					if (val > 1) {
						qtyInput.value = val - 1;
						triggerChange(qtyInput);
					}
				});
			}

			if (plusBtn) {
				plusBtn.addEventListener('click', function (e) {
					e.preventDefault();
					var val = parseInt(qtyInput.value, 10) || 1;
					var max = parseInt(qtyInput.getAttribute('max'), 10) || 99;
					if (val < max) {
						qtyInput.value = val + 1;
						triggerChange(qtyInput);
					}
				});
			}
		}

		function triggerChange(el) {
			var event;
			if (typeof (Event) === 'function') {
				event = new Event('change', { bubbles: true });
			} else {
				event = document.createEvent('Event');
				event.initEvent('change', true, true);
			}
			el.dispatchEvent(event);
		}

		// =====================================================================
		// 4. BUY NOW (MUA NGAY - DIRECT CHECKOUT)
		// =====================================================================
		var buyNowBtn = document.querySelector('.bv-btn-buy-now');
		if (buyNowBtn) {
			buyNowBtn.addEventListener('click', function (e) {
				e.preventDefault();
				var form = document.querySelector('form.cart');
				var pid = buyNowBtn.getAttribute('data-product-id');
				var checkoutUrl = buyNowBtn.getAttribute('data-checkout-url') || '/thanh-toan/';

				// If variable product, check if variation is selected
				if (form && form.classList.contains('variations_form')) {
					var varIdInput = form.querySelector('input.variation_id');
					var varId = varIdInput ? parseInt(varIdInput.value, 10) : 0;
					if (!varId || varId <= 0) {
						alert('Vui lòng chọn đầy đủ phân loại sản phẩm (màu men, kích thước) trước khi mua hàng.');
						var firstSelect = form.querySelector('.variations select');
						if (firstSelect) firstSelect.focus();
						return;
					}
				}

				buyNowBtn.disabled = true;
				var origText = buyNowBtn.innerHTML;
				buyNowBtn.innerHTML = '<span>Đang chuyển tới thanh toán...</span>';

				var formData = form ? new FormData(form) : new FormData();
				if (!formData.has('add-to-cart')) {
					formData.append('add-to-cart', pid);
				}

				fetch(window.location.href, {
					method: 'POST',
					body: formData
				}).then(function () {
					window.location.href = checkoutUrl;
				}).catch(function () {
					if (form) {
						form.submit();
					} else {
						window.location.href = checkoutUrl + '?add-to-cart=' + pid;
					}
				});
			});
		}

		// =====================================================================
		// 5. PRODUCT TABS SWITCHER
		// =====================================================================
		var tabBtns = document.querySelectorAll('.bv-tab-btn');
		var tabPanels = document.querySelectorAll('.bv-tab-content');

		if (tabBtns.length > 0) {
			tabBtns.forEach(function (btn) {
				btn.addEventListener('click', function (e) {
					e.preventDefault();
					var targetId = btn.getAttribute('data-tab');
					var targetPanel = document.getElementById(targetId);
					if (!targetPanel) return;

					tabBtns.forEach(function (b) {
						b.classList.remove('is-active');
						b.setAttribute('aria-selected', 'false');
					});
					tabPanels.forEach(function (p) {
						p.classList.remove('is-active');
					});

					btn.classList.add('is-active');
					btn.setAttribute('aria-selected', 'true');
					targetPanel.classList.add('is-active');
				});
			});
		}

		// =====================================================================
		// 6. COLLAPSIBLE SIDEBAR FILTER ACCORDION
		// =====================================================================
		var collHeadings = document.querySelectorAll('.bv-collapsible-heading');
		if (collHeadings.length > 0) {
			collHeadings.forEach(function (heading) {
				heading.addEventListener('click', function () {
					var group = heading.closest('.bv-filter-group');
					if (!group) return;
					var isCollapsed = group.classList.toggle('is-collapsed');
					var icon = heading.querySelector('.bv-filter-heading-icon');
					if (icon) {
						icon.textContent = isCollapsed ? '+' : '−';
					}
					heading.setAttribute('aria-expanded', isCollapsed ? 'false' : 'true');
				});
				heading.addEventListener('keydown', function (e) {
					if (e.key === 'Enter' || e.key === ' ') {
						e.preventDefault();
						heading.click();
					}
				});
			});
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', initCeramicShop);
	} else {
		initCeramicShop();
	}
})();
