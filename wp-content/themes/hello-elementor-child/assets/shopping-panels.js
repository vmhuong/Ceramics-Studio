jQuery(function ($) {
    'use strict';

    // Purge stale mini-cart cache in sessionStorage if it lacks selective checkboxes
    try {
        var fragKey = typeof wc_cart_fragments_params !== 'undefined' ? wc_cart_fragments_params.fragment_name : null;
        if (fragKey) {
            var cached = sessionStorage.getItem(fragKey);
            if (cached && cached.indexOf('bv-cart-item-checkbox') === -1) {
                sessionStorage.removeItem(fragKey);
                if (wc_cart_fragments_params.cart_hash_key) {
                    sessionStorage.removeItem(wc_cart_fragments_params.cart_hash_key);
                }
            }
        }
    } catch (e) {}

    var panels = Array.from(document.querySelectorAll('.bv-shopping-panel'));

    function openDrawer(id, trigger) {
        var panel = document.getElementById(id);
        if (!panel || !panel.showModal) return false;

        if (panel._closeTimer) {
            clearTimeout(panel._closeTimer);
            panel._closeTimer = null;
        }

        // Close any other open dialogs
        panels.forEach(function (other) {
            if (other !== panel) {
                if (other._closeTimer) {
                    clearTimeout(other._closeTimer);
                    other._closeTimer = null;
                }
                if (other.open) {
                    other.close();
                }
            }
        });

        panel.bvTrigger = trigger;
        if (!panel.open) {
            panel.showModal();
        }
        document.body.classList.add('bv-panel-open');
        requestAnimationFrame(function () {
            panel.classList.add('is-active');
        });

        if (trigger) trigger.setAttribute('aria-expanded', 'true');
        return true;
    }

    function closeDrawer(panel) {
        if (!panel || !panel.open) return;
        if (panel._closeTimer) {
            clearTimeout(panel._closeTimer);
        }
        panel.classList.remove('is-active');
        // Allow CSS transition to finish before calling close()
        panel._closeTimer = setTimeout(function () {
            panel._closeTimer = null;
            if (panel.open) panel.close();
        }, 280);
    }

    function bindTriggers() {
        document.querySelectorAll('.bv-header-cart, .bv-header-account').forEach(function (trigger) {
            var id = trigger.classList.contains('bv-header-cart') ? 'bv-cart-panel' : 'bv-account-panel';
            var panel = document.getElementById(id);
            trigger.setAttribute('aria-haspopup', 'dialog');
            trigger.setAttribute('aria-controls', id);
            trigger.setAttribute('aria-expanded', panel ? String(panel.open) : 'false');
        });
    }

    // CLICK ONLY TO OPEN (NO HOVER DISRUPTIONS)
    $(document).on('click', '.bv-header-cart, .bv-header-account', function (event) {
        if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        var id = this.classList.contains('bv-header-cart') ? 'bv-cart-panel' : 'bv-account-panel';
        if (openDrawer(id, this)) {
            event.preventDefault();
        }
    });

    // CLOSE BUTTON CLICK
    $(document).on('click', '.bv-panel-close', function () {
        var panel = $(this).closest('.bv-shopping-panel')[0];
        if (panel) closeDrawer(panel);
    });

    // CLICK OUTSIDE (BACKDROP) CLOSES DRAWER
    panels.forEach(function (panel) {
        panel.addEventListener('click', function (event) {
            var r = panel.getBoundingClientRect();
            // In <dialog>, clicks on backdrop report coordinates outside the bounding client rect
            if (event.target === panel && (event.clientX < r.left || event.clientX > r.right || event.clientY < r.top || event.clientY > r.bottom)) {
                closeDrawer(panel);
            }
        });

        panel.addEventListener('close', function () {
            panel.classList.remove('is-active');
            if (!panels.some(function (p) { return p.open; })) {
                document.body.classList.remove('bv-panel-open');
            }
            bindTriggers();
            if (panel.bvTrigger && panel.bvTrigger.isConnected) {
                panel.bvTrigger.focus();
            }
        });
    });

    // ESCAPE KEY SUPPORT
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            panels.forEach(function (panel) {
                if (panel.open) closeDrawer(panel);
            });
        }
    });

    // SWITCH LOGIN / RECOVER VIEWS
    $(document).on('click', '[data-account-switch]', function (e) {
        e.preventDefault();
        var view = this.dataset.accountSwitch;
        var $dialog = $(this).closest('.bv-shopping-panel');
        $dialog.find('[data-account-view]').each(function () {
            this.hidden = this.dataset.accountView !== view;
        });
        var $title = $dialog.find('#bv-account-panel-title');
        if (view === 'recover') {
            $title.text('Khôi phục mật khẩu');
        } else {
            $title.text('Đăng nhập');
        }
        $dialog.find('[data-account-view="' + view + '"] input:not([type="hidden"]):first').focus();
    });

    // SHOW / HIDE PASSWORD TOGGLE
    $(document).on('click', '.bv-password-toggle', function () {
        var $input = $(this).siblings('input');
        if (!$input.length) return;
        var isPassword = $input.attr('type') === 'password';
        $input.attr('type', isPassword ? 'text' : 'password');
        $(this).text(isPassword ? 'Ẩn' : 'Hiện');
        $(this).attr('aria-label', isPassword ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
    });

    // REGISTER LINK IN DRAWER CLOSES DRAWER AND FOLLOWS
    $(document).on('click', '#bv-account-panel a[href*="#register"]', function () {
        var panel = document.getElementById('bv-account-panel');
        if (panel && panel.open) panel.close();
    });

    // AUTO-OPEN CART DRAWER WHEN AN ITEM IS ADDED VIA AJAX
    $(document.body).on('added_to_cart', function () {
        bindTriggers();
        var cartTrigger = document.querySelector('.bv-header-cart');
        openDrawer('bv-cart-panel', cartTrigger);
    });

    $(document.body).on('wc_fragments_loaded wc_fragments_refreshed removed_from_cart updated_wc_div', bindTriggers);

    // QUANTITY STEPPER BUTTONS IN MINI-CART
    $(document).on('click', '.bv-mcart-qty-btn', function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $btn = $(this);
        var action = $btn.data('action');
        var $stepper = $btn.closest('.bv-mcart-qty-stepper');
        var $input = $stepper.find('.bv-mcart-qty-input');
        var currentVal = parseInt($input.val(), 10) || 1;
        var min = parseInt($input.attr('min'), 10) || 1;
        var max = parseInt($input.attr('max'), 10) || 99;

        if (action === 'plus') {
            if (currentVal < max) {
                $input.val(currentVal + 1).trigger('change');
            }
        } else if (action === 'minus') {
            if (currentVal > min) {
                $input.val(currentVal - 1).trigger('change');
            }
        }
    });

    // AJAX QUANTITY UPDATE (DEBOUNCED)
    var qtyTimer = null;
    $(document).on('change', '.bv-mcart-qty-input', function () {
        var $input = $(this);
        var cartKey = $input.data('cart_item_key') || $input.closest('.bv-mcart-qty-stepper').data('cart_item_key');
        var qty = parseInt($input.val(), 10);
        var min = parseInt($input.attr('min'), 10) || 1;
        var max = parseInt($input.attr('max'), 10) || 99;

        if (isNaN(qty) || qty < min) qty = min;
        if (qty > max) qty = max;
        $input.val(qty);

        var $card = $input.closest('.bv-mini-cart-card');

        clearTimeout(qtyTimer);
        qtyTimer = setTimeout(function () {
            if (!cartKey || typeof bvShopping === 'undefined' || !bvShopping.qtyUrl) return;
            $card.addClass('is-updating');

            $.ajax({
                type: 'POST',
                url: bvShopping.qtyUrl,
                data: {
                    cart_item_key: cartKey,
                    quantity: qty,
                    nonce: bvShopping.qtyNonce
                },
                dataType: 'json',
                success: function (response) {
                    if (response && response.fragments) {
                        $.each(response.fragments, function (key, value) {
                            $(key).replaceWith(value);
                        });
                        $(document.body).trigger('wc_fragments_refreshed');
                        $(document.body).trigger('updated_wc_div');
                    }
                },
                complete: function () {
                    $card.removeClass('is-updating');
                }
            });
        }, 300);
    });

    $(document).on('keydown', '.bv-mcart-qty-input', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            $(this).trigger('change');
        }
    });

    // AJAX INDEPENDENT ATTRIBUTE SWITCH IN MINI-CART (Size / Color / Finish)
    $(document).on('change', '.bv-mcart-attr-select', function () {
        var $changedSelect = $(this);
        var $rowsWrap = $changedSelect.closest('.bv-mcart-attr-rows');
        var cartKey = $rowsWrap.data('cart_item_key');
        var variations = $rowsWrap.data('variations');
        var $card = $rowsWrap.closest('.bv-mini-cart-card');

        if (!cartKey || !variations || !variations.length || typeof bvShopping === 'undefined' || !bvShopping.switchVarUrl) return;

        // Collect all currently selected attributes for this cart item
        var currentAttrs = {};
        $rowsWrap.find('.bv-mcart-attr-select').each(function () {
            var $sel = $(this);
            var key = $sel.data('attr-key');
            var val = $sel.val();
            currentAttrs[key] = val;
            // Optimistically update the badge text
            $sel.siblings('.bv-mcart-attr-badge').find('.bv-mcart-attr-val').text(val);
        });

        // Find matching variation in variations array
        var matched = null;
        for (var i = 0; i < variations.length; i++) {
            var v = variations[i];
            var isMatch = true;
            for (var attrKey in currentAttrs) {
                var chosenVal = currentAttrs[attrKey];
                var vVal = v.attributes[attrKey];
                if (typeof vVal === 'undefined') {
                    var altKey = attrKey.indexOf('attribute_') === 0 ? attrKey.replace('attribute_', '') : ('attribute_' + attrKey);
                    vVal = v.attributes[altKey];
                }
                if (typeof vVal !== 'undefined' && vVal !== '' && vVal !== chosenVal) {
                    isMatch = false;
                    break;
                }
            }
            if (isMatch) {
                matched = v;
                break;
            }
        }

        // If exact combination not found, fallback to best match with changed attribute
        if (!matched) {
            var changedKey = $changedSelect.data('attr-key');
            var changedVal = $changedSelect.val();
            for (var j = 0; j < variations.length; j++) {
                var cand = variations[j];
                var cVal = cand.attributes[changedKey] || cand.attributes[changedKey.replace('attribute_', '')] || cand.attributes['attribute_' + changedKey];
                if (cVal === changedVal || cVal === '') {
                    matched = cand;
                    break;
                }
            }
        }

        if (!matched || !matched.id) {
            return;
        }

        // Optimistically update line price and unit price for immediate visual feedback
        if (matched.price_html) {
            var qtyInput = $card.find('.bv-mcart-qty-input');
            var qty = parseInt(qtyInput.val(), 10) || 1;
            var newTotal = matched.price * qty;
            // Format VNĐ
            var formattedPrice = new Intl.NumberFormat('vi-VN').format(newTotal) + ' ₫';
            $card.find('.bv-mcart-total-price').text(formattedPrice);
            $card.find('.bv-mcart-unit-price').text(matched.price_html + '/món');
        }

        $card.addClass('is-updating');

        $.ajax({
            type: 'POST',
            url: bvShopping.switchVarUrl,
            data: {
                cart_item_key: cartKey,
                variation_id: matched.id,
                nonce: bvShopping.qtyNonce
            },
            dataType: 'json',
            success: function (response) {
                if (response && response.fragments) {
                    $.each(response.fragments, function (key, value) {
                        $(key).replaceWith(value);
                    });
                    $(document.body).trigger('wc_fragments_refreshed');
                    $(document.body).trigger('updated_wc_div');
                }
            },
            error: function () {
                $card.removeClass('is-updating');
            },
            complete: function () {
                $card.removeClass('is-updating');
            }
        });
    });

    // Fallback: Legacy Single Variation Select if present
    $(document).on('change', '.bv-mcart-variant-select', function () {
        var $select = $(this);
        var cartKey = $select.data('cart_item_key');
        var newVarId = parseInt($select.val(), 10);
        var $card = $select.closest('.bv-mini-cart-card');

        if (!cartKey || !newVarId || typeof bvShopping === 'undefined' || !bvShopping.switchVarUrl) return;

        $card.addClass('is-updating');

        $.ajax({
            type: 'POST',
            url: bvShopping.switchVarUrl,
            data: {
                cart_item_key: cartKey,
                variation_id: newVarId,
                nonce: bvShopping.qtyNonce
            },
            dataType: 'json',
            success: function (response) {
                if (response && response.fragments) {
                    $.each(response.fragments, function (key, value) {
                        $(key).replaceWith(value);
                    });
                    $(document.body).trigger('wc_fragments_refreshed');
                    $(document.body).trigger('updated_wc_div');
                }
            },
            error: function () {
                alert('Không thể đổi phân loại lúc này, vui lòng thử lại.');
            },
            complete: function () {
                $card.removeClass('is-updating');
            }
        });
    });

    // CART NOTE PERSISTENCE LOGIC
    var timer, pending = Promise.resolve(), savedNote = $('#bv-cart-note').val() || '';
    function saveNote() {
        clearTimeout(timer);
        pending = pending.catch(function () {}).then(function () {
            var field = document.getElementById('bv-cart-note');
            if (!field || field.value === savedNote) return;
            var value = field.value;
            $('#bv-note-status').text('Đang lưu...');
            return $.post(bvShopping.noteUrl, {nonce: bvShopping.nonce, note: value}).then(function (response) {
                if (!response.success) throw new Error('save');
                savedNote = value;
                $('#bv-note-status').text('Đã lưu ghi chú');
            }, function () {
                $('#bv-note-status').text('Chưa lưu được. Vui lòng thử lại.');
                throw new Error('save');
            });
        });
        return pending;
    }

    $(document).on('input', '#bv-cart-note', function () {
        clearTimeout(timer);
        timer = setTimeout(function () { saveNote().catch(function () {}); }, 500);
    });

    // ==========================================================================
    // SELECTIVE CART CHECKOUT (CHỌN MÓN THANH TOÁN / CHỌN TẤT CẢ)
    // ==========================================================================
    var selectionXhr = null;
    function syncCartSelection(postData) {
        if (typeof bvShopping === 'undefined' || !bvShopping.selectUrl) return;

        if (selectionXhr && selectionXhr.readyState !== 4) {
            selectionXhr.abort();
        }

        postData.nonce = bvShopping.selectNonce;

        selectionXhr = $.ajax({
            type: 'POST',
            url: bvShopping.selectUrl,
            data: postData,
            dataType: 'json',
            success: function (res) {
                if (res && res.success && res.data) {
                    var d = res.data;
                    // Update subtotal formatted text
                    $('.bv-selected-subtotal').html(d.subtotal_html);
                    $('.cart-subtotal .amount').html(d.subtotal_html);
                    // Update total formatted text
                    if ($('.bv-selected-total').length) {
                        $('.bv-selected-total').html(d.total_html);
                    }
                    $('.order-total .amount').html(d.total_html);
                    // Update count numbers on checkout buttons
                    $('.bv-btn-count').text(d.selected_count);
                    // Update Select All checkbox state
                    $('.bv-cart-select-all-checkbox').prop('checked', d.all_selected);

                    // Update checkout button disabled state
                    var $checkoutBtns = $('.bv-checkout-btn, .checkout-button');
                    if (d.is_empty_selection) {
                        $checkoutBtns.addClass('is-disabled').attr('aria-disabled', 'true');
                    } else {
                        $checkoutBtns.removeClass('is-disabled').removeAttr('aria-disabled');
                    }
                }
            }
        });
    }

    // Toggle single item checkbox
    $(document).on('change', '.bv-cart-item-checkbox', function () {
        var $checkbox = $(this);
        var key = $checkbox.data('cart_item_key');
        var isChecked = $checkbox.is(':checked');
        var $row = $checkbox.closest('.bv-mini-cart-card, .bv-cart-item-row');

        $row.toggleClass('is-unselected', !isChecked);

        // Check if all are checked
        var $allItemChecks = $('.bv-cart-item-checkbox');
        var totalItems = $allItemChecks.length;
        var checkedCount = $allItemChecks.filter(':checked').length;
        $('.bv-cart-select-all-checkbox').prop('checked', totalItems > 0 && checkedCount === totalItems);

        syncCartSelection({ cart_item_key: key, selected: isChecked ? 1 : 0 });
    });

    // Toggle Select All checkbox
    $(document).on('change', '.bv-cart-select-all-checkbox', function () {
        var isChecked = $(this).is(':checked');
        $('.bv-cart-select-all-checkbox').prop('checked', isChecked);
        $('.bv-cart-item-checkbox').prop('checked', isChecked);
        $('.bv-mini-cart-card, .bv-cart-item-row').toggleClass('is-unselected', !isChecked);

        syncCartSelection({ select_all: isChecked ? 1 : 0 });
    });

    // Prevent proceeding to checkout when 0 items are selected
    $(document).on('click', '.bv-checkout-btn.is-disabled, .checkout-button.is-disabled', function (e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        alert('Vui lòng chọn ít nhất một sản phẩm trong giỏ để tiến hành thanh toán.');
        return false;
    });

    $(document).on('click', 'body.woocommerce-cart .checkout-button, body.woocommerce-cart .woocommerce-mini-cart__buttons .checkout', function (event) {
        if ($(this).hasClass('is-disabled')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            alert('Vui lòng chọn ít nhất một sản phẩm trong giỏ để tiến hành thanh toán.');
            return false;
        }
        if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        var href = this.href;
        saveNote().then(function () { window.location.assign(href); }).catch(function () {});
    });

    bindTriggers();
});
