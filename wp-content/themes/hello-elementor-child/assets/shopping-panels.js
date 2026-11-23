jQuery(function ($) {
    'use strict';

    var panels = Array.from(document.querySelectorAll('.bv-shopping-panel'));

    function openDrawer(id, trigger) {
        var panel = document.getElementById(id);
        if (!panel || !panel.showModal) return false;

        // Close any other open dialogs
        panels.forEach(function (other) {
            if (other !== panel && other.open) {
                other.close();
            }
        });

        panel.bvTrigger = trigger;
        panel.showModal();
        document.body.classList.add('bv-panel-open');
        requestAnimationFrame(function () {
            panel.classList.add('is-active');
        });

        if (trigger) trigger.setAttribute('aria-expanded', 'true');
        return true;
    }

    function closeDrawer(panel) {
        if (!panel || !panel.open) return;
        panel.classList.remove('is-active');
        // Allow CSS transition to finish before calling close()
        setTimeout(function () {
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

    $(document).on('click', 'body.woocommerce-cart .checkout-button, body.woocommerce-cart .woocommerce-mini-cart__buttons .checkout', function (event) {
        if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        var href = this.href;
        saveNote().then(function () { window.location.assign(href); }).catch(function () {});
    });

    bindTriggers();
});
