jQuery(function ($) {
	'use strict';

	var $form = $('#bv-ws-form');
	if (!$form.length) return;

	var $app = $('#bv-workshop-booking');
	var $stepsNav = $app.find('.bv-ws-steps-nav');
	var $panels = $form.find('.bv-ws-wizard-panel');
	var $dateInput = $('#bv-ws-date');
	var $slotsContainer = $('#bv-slots-container');
	var $qtyInput = $('#bv-ws-qty');
	var $totalEl = $('#bv-ws-total');
	var $submitBtn = $('#bv-ws-submit');
	var $msgEl = $('#bv-ws-message');

	var currentStep = 1;
	var maxAllowedStep = 1;

	function formatVND(amount) {
		return new Intl.NumberFormat('vi-VN').format(amount) + ' ₫';
	}

	function formatVnDate(dateStr) {
		if (!dateStr) return 'Chưa chọn';
		var parts = dateStr.split('-');
		if (parts.length !== 3) return dateStr;
		var d = new Date(parts[0], parts[1] - 1, parts[2]);
		var days = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
		var dayName = days[d.getDay()];
		return dayName + ', ' + parts[2] + '/' + parts[1] + '/' + parts[0];
	}

	function getSelectedPackage() {
		var $pkg = $form.find('input[name="ws_package"]:checked');
		return {
			id: $pkg.val(),
			name: $pkg.data('name') || 'Làm gốm bàn xoay thủ công',
			price: parseInt($pkg.data('price'), 10) || 350000,
			duration: $pkg.data('duration') || '2.5 giờ'
		};
	}

	function getSelectedSlot() {
		var $slot = $form.find('input[name="ws_slot"]:checked');
		if (!$slot.length) return null;
		return {
			id: $slot.val(),
			label: $slot.data('label') || 'Ca Sáng (09:00 - 11:30)'
		};
	}

	function updateTotal() {
		var pkg = getSelectedPackage();
		var qty = parseInt($qtyInput.val(), 10) || 1;
		var total = pkg.price * qty;
		$totalEl.text(formatVND(total));
	}

	function updateSummaryView() {
		var pkg = getSelectedPackage();
		var date = $dateInput.val();
		var slot = getSelectedSlot();

		// Step 2 badge
		$('#bv-ws-summary-pkg-name').text(pkg.name);

		// Step 3 confirmation card
		$('#bv-conf-pkg').text(pkg.name + ' (' + formatVND(pkg.price) + ' / người)');
		$('#bv-conf-duration').text('Thời lượng: ' + pkg.duration);
		$('#bv-conf-date').text(formatVnDate(date));
		$('#bv-conf-slot').text(slot ? slot.label : 'Chưa chọn ca học');

		updateTotal();
	}

	function updateStepperNav(step) {
		$stepsNav.find('li').each(function () {
			var s = parseInt($(this).data('step'), 10);
			$(this).removeClass('is-current is-complete');
			if (s === step) {
				$(this).addClass('is-current');
			} else if (s < step) {
				$(this).addClass('is-complete');
			}
		});
	}

	function goToStep(targetStep) {
		targetStep = parseInt(targetStep, 10);
		if (isNaN(targetStep) || targetStep < 1 || targetStep > 3) return;

		// Validation before advancing
		if (targetStep > currentStep) {
			// Advancing to step 2: package must be checked
			if (currentStep === 1) {
				var pkg = $form.find('input[name="ws_package"]:checked').val();
				if (!pkg) {
					alert('Vui lòng chọn một gói trải nghiệm.');
					return;
				}
			}

			// Advancing to step 3: date and slot must be checked
			if (currentStep === 2 || targetStep === 3) {
				var date = $dateInput.val();
				var slot = $form.find('input[name="ws_slot"]:checked').val();
				if (!date) {
					alert('Vui lòng chọn ngày dự kiến tham gia.');
					return;
				}
				if (!slot) {
					alert('Vui lòng chọn một ca học còn chỗ trống.');
					return;
				}
			}
		}

		// Update max visited step
		if (targetStep > maxAllowedStep) {
			maxAllowedStep = targetStep;
		}

		// Switch panels smoothly
		$panels.removeClass('is-active').hide();
		var $targetPanel = $form.find('.bv-ws-wizard-panel[data-panel="' + targetStep + '"]');
		$targetPanel.fadeIn(250, function () {
			$(this).addClass('is-active');
		});

		// Update current step & navbar
		currentStep = targetStep;
		updateStepperNav(currentStep);
		updateSummaryView();

		// Smooth scroll to top of app if needed
		if ($app.length) {
			var appTop = $app.offset().top - 120;
			if ($(window).scrollTop() > appTop + 80) {
				$('html, body').animate({ scrollTop: appTop }, 350);
			}
		}
	}

	// 1. Navigation Button Handlers
	$form.on('click', '.bv-ws-btn-next', function (e) {
		e.preventDefault();
		goToStep($(this).data('goto'));
	});

	$form.on('click', '.bv-ws-btn-prev, .bv-ws-change-link, .bv-ws-change-btn', function (e) {
		e.preventDefault();
		goToStep($(this).data('goto'));
	});

	// 2. Stepper Nav Click Handler
	$stepsNav.on('click', '.bv-ws-step-btn', function (e) {
		e.preventDefault();
		var target = parseInt($(this).data('step-target'), 10);
		// Allow navigating back anytime, or forward if validated
		if (target <= maxAllowedStep || target === currentStep + 1) {
			goToStep(target);
		}
	});

	// 3. Attendees Counter
	$form.on('click', '.bv-qty-btn', function (e) {
		e.preventDefault();
		var action = $(this).data('action');
		var current = parseInt($qtyInput.val(), 10) || 1;
		var min = parseInt($qtyInput.attr('min'), 10) || 1;
		var max = parseInt($qtyInput.attr('max'), 10) || 8;

		if (action === 'plus' && current < max) {
			$qtyInput.val(current + 1);
		} else if (action === 'minus' && current > min) {
			$qtyInput.val(current - 1);
		}
		updateTotal();
	});

	// 4. Package Selection
	$form.on('change', 'input[name="ws_package"]', function () {
		updateSummaryView();
	});

	// 5. Slot Selection
	$form.on('change', 'input[name="ws_slot"]', function () {
		updateSummaryView();
	});

	// 6. Date Picker & Dynamic Slots AJAX
	$dateInput.on('change', function () {
		var selectedDate = $(this).val();
		if (!selectedDate) return;

		$slotsContainer.css('opacity', '0.5');

		$.ajax({
			url: bvWorkshopData.ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'bv_get_date_slots',
				nonce: bvWorkshopData.nonce,
				date: selectedDate
			},
			success: function (res) {
				$slotsContainer.css('opacity', '1');
				if (res.success && res.data) {
					renderSlots(res.data);
					updateSummaryView();
				}
			},
			error: function () {
				$slotsContainer.css('opacity', '1');
			}
		});
	});

	function renderSlots(slots) {
		var html = '';
		var hasChecked = false;

		slots.forEach(function (slot) {
			var isFull = slot.is_full || !slot.is_open;
			var isChecked = !isFull && !hasChecked;
			if (isChecked) hasChecked = true;

			var statusText = !slot.is_open ? 'Không mở' : (slot.is_full ? 'Đã kín chỗ' : 'Còn ' + slot.remain + ' chỗ');
			var urgencyClass = (slot.remain > 0 && slot.remain <= 2) ? 'is-urgency' : '';

			html += '<label class="bv-slot-card ' + (isFull ? 'is-full' : '') + '">';
			html += '<input type="radio" name="ws_slot" value="' + slot.id + '" data-label="' + slot.label + ' (' + slot.time + ')" ' + (isFull ? 'disabled' : '') + ' ' + (isChecked ? 'checked' : '') + '>';
			html += '<div class="bv-slot-content">';
			html += '<span class="bv-slot-name">' + slot.label + '</span>';
			html += '<strong class="bv-slot-time">' + slot.time + '</strong>';
			html += '<span class="bv-slot-status ' + urgencyClass + '">' + statusText + '</span>';
			html += '</div>';
			html += '</label>';
		});

		$slotsContainer.html(html);
	}

	// 7. Form Submission (Step 3 Checkout Trigger)
	$form.on('submit', function (e) {
		e.preventDefault();
		$msgEl.removeClass('is-error').text('');

		var pkg = $form.find('input[name="ws_package"]:checked').val();
		var date = $dateInput.val();
		var slot = $form.find('input[name="ws_slot"]:checked').val();
		var qty = parseInt($qtyInput.val(), 10) || 1;

		if (!pkg || !date || !slot) {
			$msgEl.addClass('is-error').text('Vui lòng chọn đầy đủ gói trải nghiệm, ngày và ca học.');
			return;
		}

		$submitBtn.prop('disabled', true).text('Đang chuẩn bị vé workshop...');

		$.ajax({
			url: bvWorkshopData.ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'bv_book_workshop',
				nonce: bvWorkshopData.nonce,
				package: pkg,
				date: date,
				slot: slot,
				qty: qty
			},
			success: function (res) {
				if (res.success && res.data && res.data.redirect) {
					window.location.href = res.data.redirect;
				} else {
					$submitBtn.prop('disabled', false).text('Tiến Hành Đặt Lịch & Thanh Toán');
					$msgEl.addClass('is-error').text(res.data && res.data.message ? res.data.message : 'Có lỗi xảy ra, vui lòng thử lại.');
				}
			},
			error: function () {
				$submitBtn.prop('disabled', false).text('Tiến Hành Đặt Lịch & Thanh Toán');
				$msgEl.addClass('is-error').text('Lỗi kết nối máy chủ, vui lòng thử lại.');
			}
		});
	});

	// Initial setup
	updateStepperNav(1);
	updateSummaryView();
});
