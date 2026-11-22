jQuery(function ($) {
    'use strict';

    var $form = $('#bv-ws-form');
    if (!$form.length) return;

    var $dateInput = $('#bv-ws-date');
    var $slotsContainer = $('#bv-slots-container');
    var $qtyInput = $('#bv-ws-qty');
    var $totalEl = $('#bv-ws-total');
    var $submitBtn = $('#bv-ws-submit');
    var $msgEl = $('#bv-ws-message');

    function formatVND(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' ₫';
    }

    function updateTotal() {
        var $pkg = $form.find('input[name="ws_package"]:checked');
        var price = parseInt($pkg.data('price'), 10) || 0;
        var qty = parseInt($qtyInput.val(), 10) || 1;
        var total = price * qty;
        $totalEl.text(formatVND(total));
    }

    // Attendees counter
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

    // Package change
    $form.on('change', 'input[name="ws_package"]', updateTotal);

    // Date change -> reload slots via AJAX
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

    // Submit booking
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

        $submitBtn.prop('disabled', true).html('<i class="eicon-loading eicon-animation-spin" aria-hidden="true"></i> Đang chuẩn bị vé...');

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
                    $submitBtn.prop('disabled', false).html('<i class="eicon-cart" aria-hidden="true"></i> Tiến hành Đặt lịch & Thanh toán');
                    $msgEl.addClass('is-error').text(res.data && res.data.message ? res.data.message : 'Có lỗi xảy ra, vui lòng thử lại.');
                }
            },
            error: function () {
                $submitBtn.prop('disabled', false).html('<i class="eicon-cart" aria-hidden="true"></i> Tiến hành Đặt lịch & Thanh toán');
                $msgEl.addClass('is-error').text('Lỗi kết nối máy chủ, vui lòng thử lại.');
            }
        });
    });

    updateTotal();
});
