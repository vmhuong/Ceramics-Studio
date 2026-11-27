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
	var pollTimer = null;
	var activeOrder = null;

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
		if (isNaN(targetStep) || targetStep < 1 || targetStep > 4) return;
		if (targetStep === 4 && !activeOrder) return;

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

	function escHtml(str) {
		return String(str || '').replace(/[&<>"']/g, function (m) {
			return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[m];
		});
	}

	function stopPolling() {
		if (pollTimer) {
			clearInterval(pollTimer);
			pollTimer = null;
		}
	}

	function checkPaymentStatus(isManual) {
		if (!activeOrder) return;
		var $statusBox = $('#bv-ws-pay-status');
		if (isManual) {
			$statusBox.text('Đang kiểm tra giao dịch...');
		}
		$.ajax({
			url: bvWorkshopData.ajaxUrl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'bv_check_workshop_payment',
				nonce: bvWorkshopData.nonce,
				order_id: activeOrder.order_id,
				order_key: activeOrder.order_key
			},
			success: function (res) {
				if (res.success && res.data && res.data.is_paid) {
					stopPolling();
					$('#bv-ws-qr-card').addClass('is-paid');
					$statusBox.html('<strong>Đã thanh toán thành công!</strong> Vé #' + escHtml(activeOrder.order_id) + ' đã được xác nhận.');
					$('.bv-ws-qr-badge').text('Thanh toán thành công').addClass('is-paid');
				} else if (res.success && res.data && res.data.is_expired) {
					stopPolling();
					$statusBox.html('<strong>Đã hết hạn giữ chỗ (10 phút).</strong> Chỗ đã được hoàn lại, vui lòng bấm "Đặt lịch khác" để chọn lại.');
					$('.bv-ws-qr-badge').text('Hết hạn thanh toán');
					$('#bv-ws-check-now').prop('disabled', true);
				} else if (isManual) {
					$statusBox.text('Chưa nhận được tín hiệu chuyển khoản. Hệ thống vẫn đang tự động theo dõi...');
				}
			}
		});
	}

	function renderPaymentStep(data) {
		activeOrder = data;
		var viewOrderBtn = data.order_view_url
			? '<a href="' + escHtml(data.order_view_url) + '" class="bv-ws-btn-prev">Xem đơn vé #' + escHtml(data.order_id) + '</a>'
			: '';

		var html = '<div class="bv-ws-qr-card" id="bv-ws-qr-card">';
		html += '  <div class="bv-ws-qr-head">';
		html += '    <span class="bv-ws-qr-badge">Chờ thanh toán</span>';
		html += '    <h3>Quét mã VietQR để hoàn tất đặt vé #' + escHtml(data.order_id) + '</h3>';
		html += '    <p>' + escHtml(data.package_name) + ' • ' + escHtml(data.date_formatted) + ' • ' + escHtml(data.slot_label) + ' • ' + escHtml(data.qty) + ' người</p>';
		html += '  </div>';
		html += '  <div class="bv-ws-qr-body">';
		html += '    <div class="bv-ws-qr-img-wrap">';
		html += '      <img src="' + escHtml(data.qr_url) + '" alt="Mã VietQR đơn #' + escHtml(data.order_id) + '" width="260" height="260">';
		html += '      <span>Tự động điền đúng số tiền &amp; nội dung CK</span>';
		html += '    </div>';
		html += '    <div class="bv-ws-qr-info">';
		html += '      <div class="bv-ws-qr-row"><span>Ngân hàng</span><strong>' + escHtml(data.bank_id) + '</strong></div>';
		html += '      <div class="bv-ws-qr-row"><span>Số tài khoản</span><strong id="bv-ws-copy-acc">' + escHtml(data.account_no) + '</strong><button type="button" class="bv-ws-copy-btn" data-copy="' + escHtml(data.account_no) + '">Sao chép</button></div>';
		html += '      <div class="bv-ws-qr-row"><span>Chủ tài khoản</span><strong>' + escHtml(data.account_name) + '</strong></div>';
		html += '      <div class="bv-ws-qr-row"><span>Số tiền</span><strong class="bv-ws-qr-amount">' + escHtml(data.amount_formatted) + '</strong><button type="button" class="bv-ws-copy-btn" data-copy="' + escHtml(data.amount) + '">Sao chép</button></div>';
		html += '      <div class="bv-ws-qr-row is-highlight"><span>Nội dung CK</span><strong>' + escHtml(data.transfer_content) + '</strong><button type="button" class="bv-ws-copy-btn" data-copy="' + escHtml(data.transfer_content) + '">Sao chép</button></div>';
		html += '      <div class="bv-ws-pay-status" id="bv-ws-pay-status" role="status">Đang chờ xác nhận chuyển khoản tự động qua Webhook...</div>';
		html += '    </div>';
		html += '  </div>';
		html += '  <div class="bv-ws-wizard-actions">';
		html += '    <button type="button" class="bv-ws-btn-prev" id="bv-ws-reset-btn">← Đặt lịch khác</button>';
		html += '    ' + viewOrderBtn;
		html += '    <button type="button" class="bv-btn-book" id="bv-ws-check-now">Tôi đã chuyển khoản — Kiểm tra ngay</button>';
		html += '  </div>';
		html += '</div>';

		$('#bv-ws-qr-container').html(html);
		stopPolling();
		pollTimer = setInterval(function () { checkPaymentStatus(false); }, 4000);
	}

	$form.on('click', '.bv-ws-copy-btn', function () {
		var $btn = $(this);
		var val = String($btn.data('copy') || '');
		if (navigator.clipboard && val) {
			navigator.clipboard.writeText(val).then(function () {
				$btn.text('Đã chép');
				setTimeout(function () { $btn.text('Sao chép'); }, 1500);
			});
		}
	});

	$form.on('click', '#bv-ws-check-now', function () {
		checkPaymentStatus(true);
	});

	$form.on('click', '#bv-ws-reset-btn', function () {
		stopPolling();
		activeOrder = null;
		maxAllowedStep = 3;
		$submitBtn.prop('disabled', false).text('Tạo Mã QR Thanh Toán →');
		goToStep(1);
	});

	// 7. Form Submission (Step 3 -> Create VietQR Order & Show Step 4 QR)
	$form.on('submit', function (e) {
		e.preventDefault();
		$msgEl.removeClass('is-error').text('');

		var pkg = $form.find('input[name="ws_package"]:checked').val();
		var date = $dateInput.val();
		var slot = $form.find('input[name="ws_slot"]:checked').val();
		var qty = parseInt($qtyInput.val(), 10) || 1;
		var name = $.trim($('#bv-ws-name').val());
		var phone = $.trim($('#bv-ws-phone').val());
		var email = $.trim($('#bv-ws-email').val());
		var note = $.trim($('#bv-ws-note').val());

		if (!pkg || !date || !slot) {
			$msgEl.addClass('is-error').text('Vui lòng chọn đầy đủ gói trải nghiệm, ngày và ca học.');
			return;
		}
		if (!name || !phone) {
			$msgEl.addClass('is-error').text('Vui lòng nhập họ tên và số điện thoại / Zalo để giữ chỗ.');
			return;
		}

		$submitBtn.prop('disabled', true).text('Đang tạo mã QR thanh toán...');

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
				qty: qty,
				name: name,
				phone: phone,
				email: email,
				note: note
			},
			success: function (res) {
				$submitBtn.prop('disabled', false).text('Tạo Mã QR Thanh Toán →');
				if (res.success && res.data && res.data.order_id) {
					renderPaymentStep(res.data);
					maxAllowedStep = 4;
					goToStep(4);
				} else {
					$msgEl.addClass('is-error').text(res.data && res.data.message ? res.data.message : 'Có lỗi xảy ra, vui lòng thử lại.');
				}
			},
			error: function () {
				$submitBtn.prop('disabled', false).text('Tạo Mã QR Thanh Toán →');
				$msgEl.addClass('is-error').text('Lỗi kết nối máy chủ, vui lòng thử lại.');
			}
		});
	});

	// Initial setup
	updateStepperNav(1);
	updateSummaryView();
});
