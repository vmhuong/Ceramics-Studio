jQuery(function ($) {
    'use strict';
    var panels = Array.from(document.querySelectorAll('.bv-shopping-panel'));
    var hoverTimer;
    var canHover = window.matchMedia('(hover: hover) and (pointer: fine)');
    function positionPanel(panel, trigger) {
        var box = trigger ? trigger.getBoundingClientRect() : {right: innerWidth - 24, bottom: 80};
        panel.style.top = Math.max(12, Math.min(box.bottom + 14, innerHeight - 180)) + 'px';
        panel.style.right = Math.max(12, innerWidth - box.right) + 'px';
    }
    function openPanel(id, trigger, hover) {
        var panel = document.getElementById(id);
        if (!panel || !panel.showModal) return false;
        clearTimeout(hoverTimer);
        if (panel.open) return true;
        panels.forEach(function (other) { if (other.open) { other.bvRestoreFocus = false; other.close(); } });
        panel.bvTrigger = trigger;
        panel.bvRestoreFocus = !hover;
        positionPanel(panel, trigger);
        if (hover) {
            var previousFocus = document.activeElement;
            panel.show();
            if (panel.contains(document.activeElement)) document.activeElement.blur();
            if (previousFocus) previousFocus.focus({preventScroll: true});
        } else {
            panel.showModal();
            document.body.classList.add('bv-panel-open');
        }
        if (trigger) trigger.setAttribute('aria-expanded', 'true');
        return true;
    }
    function bindTriggers() {
        document.querySelectorAll('.bv-header-cart, .bv-header-account').forEach(function (trigger) {
            var id = trigger.classList.contains('bv-header-cart') ? 'bv-cart-panel' : 'bv-account-panel';
            trigger.setAttribute('aria-haspopup', 'dialog');
            trigger.setAttribute('aria-controls', id);
            trigger.setAttribute('aria-expanded', String(document.getElementById(id).open));
        });
    }
    $(document).on('click', '.bv-header-cart, .bv-header-account', function (event) {
        if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        if (openPanel(this.classList.contains('bv-header-cart') ? 'bv-cart-panel' : 'bv-account-panel', this)) event.preventDefault();
    });
    $(document).on('mouseenter', '.bv-header-cart, .bv-header-account', function () {
        if (canHover.matches) openPanel(this.classList.contains('bv-header-cart') ? 'bv-cart-panel' : 'bv-account-panel', this, true);
    });
    function scheduleClose() {
        if (!canHover.matches) return;
        clearTimeout(hoverTimer);
        hoverTimer = setTimeout(function () {
            panels.forEach(function (panel) {
                if (panel.open && !panel.matches(':modal') && !panel.matches(':hover') && !(panel.bvTrigger && panel.bvTrigger.matches(':hover')) && !panel.contains(document.activeElement)) panel.close();
            });
        }, 280);
    }
    $(document).on('mouseleave', '.bv-header-cart, .bv-header-account', scheduleClose);
    document.addEventListener('pointerdown', function (event) {
        panels.forEach(function (panel) {
            if (panel.open && !panel.matches(':modal') && !panel.contains(event.target) && !(panel.bvTrigger && panel.bvTrigger.contains(event.target))) panel.close();
        });
    });
    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') return;
        panels.forEach(function (panel) {
            if (panel.open && !panel.matches(':modal')) { panel.bvRestoreFocus = true; panel.close(); }
        });
    });
    panels.forEach(function (panel) {
        panel.addEventListener('mouseenter', function () { clearTimeout(hoverTimer); });
        panel.addEventListener('mouseleave', scheduleClose);
        panel.querySelector('.bv-panel-close').addEventListener('click', function () { panel.close(); });
        panel.addEventListener('click', function (event) {
            var r = panel.getBoundingClientRect();
            if (event.target === panel && (event.clientX < r.left || event.clientX > r.right || event.clientY < r.top || event.clientY > r.bottom)) panel.close();
        });
        panel.addEventListener('close', function () {
            if (!panels.some(function (other) { return other.open && other.matches(':modal'); })) document.body.classList.remove('bv-panel-open');
            bindTriggers();
            if (panel.bvRestoreFocus && panel.bvTrigger && panel.bvTrigger.isConnected) panel.bvTrigger.focus();
        });
    });
    $(document).on('click', '[data-account-switch]', function () {
        var view = this.dataset.accountSwitch;
        document.querySelectorAll('[data-account-view]').forEach(function (element) { element.hidden = element.dataset.accountView !== view; });
        document.getElementById('bv-account-panel-title').textContent = view === 'recover' ? 'Khôi phục mật khẩu' : 'Đăng nhập tài khoản';
        document.querySelector('[data-account-view="' + view + '"] input:not([type="hidden"])').focus();
    });
    $(document).on('click', '.bv-password-toggle', function () {
        var input = this.parentElement.querySelector('input');
        input.type = input.type === 'password' ? 'text' : 'password';
        var label = input.type === 'password' ? 'Hiện mật khẩu' : 'Ẩn mật khẩu';
        this.setAttribute('aria-label', label);
        this.title = label;
    });
    $(document).on('click', '#bv-account-panel a[href*="#register"]', function () {
        document.getElementById('bv-account-panel').close();
    });
    $(document.body).on('wc_fragments_loaded wc_fragments_refreshed removed_from_cart updated_wc_div', bindTriggers);
    $(document.body).on('removed_from_cart', function (event, fragments, hash, button) {
        if (button && button.attr('data-product_id')) {
            var productId = button.attr('data-product_id');
            $('.bv-shop-grid a.ajax_add_to_cart').filter(function () { return this.dataset.product_id === productId; }).each(function () {
                $(this).removeClass('added').show().siblings('.added_to_cart').remove();
            });
        }
        if (document.body.classList.contains('woocommerce-cart')) $(document.body).trigger('wc_update_cart');
        if (document.body.classList.contains('woocommerce-checkout')) $(document.body).trigger('update_checkout');
    });
    $(document.body).on('added_to_cart', function () {
        bindTriggers();
        openPanel('bv-cart-panel', document.querySelector('.bv-header-cart'));
    });
    window.addEventListener('resize', function () {
        panels.forEach(function (panel) { if (panel.open) positionPanel(panel, document.querySelector(panel.id === 'bv-cart-panel' ? '.bv-header-cart' : '.bv-header-account')); });
    });
    window.addEventListener('scroll', function () {
        panels.forEach(function (panel) { if (panel.open && !panel.matches(':modal')) positionPanel(panel, panel.bvTrigger); });
    }, {passive: true});
    bindTriggers();

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
});
