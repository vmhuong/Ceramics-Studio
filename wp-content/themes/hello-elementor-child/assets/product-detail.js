jQuery(function ($) {
    'use strict';
    $('.summary .single_add_to_cart_button').each(function () {
        var icon = document.createElement('i');
        icon.className = 'eicon-cart-light';
        icon.setAttribute('aria-hidden', 'true');
        this.prepend(icon);
    });
    $('.woocommerce-message').has('.bv-added-message').each(function () {
        var notice = this;
        var message = notice.querySelector('.bv-added-message');
        var link = notice.querySelector('a.wc-forward');
        var title = document.createElement('strong');
        title.textContent = 'Đã thêm vào giỏ hàng';
        var detail = document.createElement('span');
        detail.textContent = message.dataset.bvCount + ' sản phẩm';
        message.append(title, detail);
        notice.replaceChildren(message);
        if (link) {
            link.className = 'bv-added-link wc-forward';
            link.textContent = 'Xem giỏ hàng';
            notice.append(link);
        }
        notice.classList.add('bv-product-toast');
        notice.setAttribute('role', 'status');
        notice.setAttribute('aria-live', 'polite');
        var icon = document.createElement('i');
        icon.className = 'eicon-check bv-toast-icon';
        icon.setAttribute('aria-hidden', 'true');
        var close = document.createElement('button');
        close.type = 'button';
        close.className = 'bv-toast-close';
        close.setAttribute('aria-label', 'Đóng thông báo');
        close.title = 'Đóng thông báo';
        close.innerHTML = '<i class="eicon-close" aria-hidden="true"></i>';
        close.addEventListener('click', function () {
            if (notice.contains(document.activeElement)) {
                var button = document.querySelector('.summary .single_add_to_cart_button');
                if (button) button.focus({preventScroll: true});
            }
            notice.remove();
        });
        notice.prepend(icon);
        notice.append(close);
        document.body.append(notice);
        function dismiss() {
            if (!notice.isConnected) return;
            if (notice.matches(':hover') || notice.contains(document.activeElement)) {
                setTimeout(dismiss, 2000);
            } else {
                notice.remove();
            }
        }
        setTimeout(dismiss, 8000);
    });
    // Keep WooCommerce's selects as the source of truth for variation validation.
    $('.variations_form').each(function () {
        var form = $(this);
        var price = form.closest('.summary').children('p.price').first();
        var originalPrice = price.html();
        form.on('found_variation', function (event, variation) {
            if (price.length && variation.price_html) {
                price.html(variation.price_html);
                form.addClass('bv-price-synced');
            }
        }).on('reset_data', function () {
            price.html(originalPrice);
            form.removeClass('bv-price-synced');
        });
        form.find('.variations select').each(function () {
            var select = this;
            var options = Array.from(select.options).filter(function (option) { return option.value; });
            if (!options.length || options.length > 10) return;
            var label = form.find('label[for="' + select.id + '"]').text().trim();
            var group = document.createElement('div');
            group.className = 'bv-product-options';
            group.setAttribute('role', 'group');
            group.setAttribute('aria-label', label);
            var selected = document.createElement('span');
            selected.className = 'bv-option-value';
            selected.setAttribute('aria-live', 'polite');
            options.forEach(function (option) {
                var button = document.createElement('button');
                button.type = 'button';
                button.dataset.value = option.value;
                button.className = 'bv-product-option';
                button.title = option.text;
                button.setAttribute('aria-label', label + ': ' + option.text);
                if (bvProduct.colors[option.text]) {
                    button.classList.add('bv-color-option');
                    button.style.setProperty('--swatch', bvProduct.colors[option.text]);
                } else {
                    button.textContent = option.text;
                }
                button.addEventListener('click', function () {
                    $(select).val(option.value).trigger('change');
                });
                group.appendChild(button);
            });
            select.after(group, selected);
            select.classList.add('bv-enhanced-select');
            select.tabIndex = -1;
            select.setAttribute('aria-hidden', 'true');
            function syncOptions() {
                Array.from(group.children).forEach(function (button) {
                    var option = Array.from(select.options).find(function (item) { return item.value === button.dataset.value; });
                    button.disabled = select.disabled || !option || option.disabled;
                    button.setAttribute('aria-pressed', String(button.dataset.value === select.value));
                });
                selected.textContent = select.value && bvProduct.colors[select.value] ? select.options[select.selectedIndex].text : '';
                selected.hidden = !selected.textContent;
            }
            $(select).on('change', syncOptions);
            form.on('woocommerce_update_variation_values found_variation reset_data', syncOptions);
            syncOptions();
        });
    });
    function accessibleThumbnails() {
        $('.woocommerce-product-gallery .flex-control-thumbs img').each(function (index) {
            if (this.dataset.bvAccessible) return;
            this.dataset.bvAccessible = 'true';
            this.tabIndex = 0;
            this.setAttribute('role', 'button');
            this.setAttribute('aria-label', 'Xem ảnh sản phẩm ' + (index + 1));
            this.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    this.click();
                }
            });
        });
    }
    $(document.body).on('wc-product-gallery-after-init', accessibleThumbnails);
    accessibleThumbnails();
});
