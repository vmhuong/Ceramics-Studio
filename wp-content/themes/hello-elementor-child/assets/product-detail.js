/**
 * Product Detail Variation Swatches & Interactive Actions
 * Style: Anh Ceramics (Circular Swatches with Double Ring, Pill Buttons, Real-time Price Sync)
 * Project: Ceramic Studio (Bookverse)
 * Author: Vu Manh Huong (PM28.11)
 */

jQuery(function ($) {
    'use strict';

    // Default Color Map for Ceramic Glazes
    var defaultGlazes = {
        'San hô': '#C77B4B',
        'Hồng phấn': '#CEB8A5',
        'Xanh sương': '#AFC4CA',
        'Cam': '#C4572E',
        'Vàng': '#C99E4D',
        'Nâu': '#7A4F3E',
        'Đen': '#2C2523',
        'Trắng': '#FAF7F2',
        'Xanh biển': '#314E59',
        'Rêu': '#7B8C70',
        'Gốm mộc': '#E8DFC8',
        'Pale Yellow': '#D4A359',
        'Plain': '#EFEBE4',
        'Gradient': '#8C5835',
        'Mộc be cát': '#E8DFC8',
        'Men tro': '#D8C3A5',
        'Đất nung': '#B88258'
    };

    var colorMap = (window.bvProduct && window.bvProduct.colors) 
        ? $.extend({}, defaultGlazes, window.bvProduct.colors) 
        : defaultGlazes;

    // Enhance WooCommerce Variations Form
    $('.variations_form').each(function () {
        var form = $(this);
        var summary = form.closest('.bv-product-summary, .summary');
        var priceEl = summary.find('.bv-summary-price, p.price').first();
        var origPriceHtml = priceEl.html();
        var mainImg = document.getElementById('bv-main-product-img');
        var origImgSrc = mainImg ? mainImg.src : '';
        var stockEl = summary.find('.bv-stock-scarcity strong');

        // Handle Found Variation
        form.on('found_variation', function (event, variation) {
            // 1. Sync Price
            if (priceEl.length && variation.price_html) {
                priceEl.html(variation.price_html);
            }

            // 2. Sync Gallery Image if variation has its own image
            if (mainImg && variation.image && variation.image.src) {
                mainImg.src = variation.image.full_src || variation.image.src;
            }

            // 3. Sync Stock
            if (stockEl.length && variation.max_qty) {
                stockEl.text(variation.max_qty);
            }
        }).on('reset_data', function () {
            priceEl.html(origPriceHtml);
            if (mainImg && origImgSrc) {
                mainImg.src = origImgSrc;
            }
        });

        // Convert Select Elements to Swatches / Buttons
        form.find('.variations select').each(function () {
            var select = this;
            var $select = $(select);
            var options = Array.from(select.options).filter(function (opt) { return opt.value; });
            if (!options.length) return;

            var labelText = form.find('label[for="' + select.id + '"]').text().trim();
            var isColorAttr = /màu|mau|color/i.test(labelText) || /màu|mau|color/i.test(select.name);

            // Container
            var wrap = document.createElement('div');
            wrap.className = 'bv-variation-custom-wrap';

            // Active Label
            var activeLabel = document.createElement('div');
            activeLabel.className = 'bv-selected-variant-label';
            var activeTextSpan = document.createElement('span');
            activeTextSpan.className = 'bv-active-val-name';
            activeLabel.appendChild(document.createTextNode(labelText + ': '));
            activeLabel.appendChild(activeTextSpan);
            wrap.appendChild(activeLabel);

            // Buttons / Swatches Group
            var group = document.createElement('div');
            group.className = isColorAttr ? 'bv-color-swatches-wrap' : 'bv-variant-buttons-wrap';
            group.setAttribute('role', 'group');
            group.setAttribute('aria-label', labelText);

            options.forEach(function (option) {
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.dataset.value = option.value;
                btn.title = option.text;
                btn.setAttribute('aria-label', labelText + ': ' + option.text);

                var colorHex = colorMap[option.text] || (isColorAttr ? '#C77B4B' : null);

                if (isColorAttr && colorHex) {
                    btn.className = 'bv-color-swatch-circle';
                    btn.style.setProperty('--swatch', colorHex);
                    btn.style.backgroundColor = colorHex;
                } else {
                    btn.className = 'bv-variant-btn';
                    btn.textContent = option.text;
                }

                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    $select.val(option.value).trigger('change');
                });

                group.appendChild(btn);
            });

            wrap.appendChild(group);
            $select.after(wrap);
            $select.addClass('bv-enhanced-select').hide();

            function syncState() {
                var currentVal = select.value;
                var currentText = '';

                Array.from(group.children).forEach(function (btn) {
                    var isSelected = (btn.dataset.value === currentVal);
                    if (isSelected) {
                        btn.classList.add('is-active');
                        btn.setAttribute('aria-pressed', 'true');
                        currentText = btn.title || btn.textContent;
                    } else {
                        btn.classList.remove('is-active');
                        btn.setAttribute('aria-pressed', 'false');
                    }
                });

                activeTextSpan.textContent = currentText || 'Vui lòng chọn';
            }

            $select.on('change', syncState);
            form.on('woocommerce_update_variation_values found_variation reset_data', syncState);
            
            // Initial sync (if preselected)
            syncState();
        });
    });

    // Single Product Gallery Thumbnails (Click switches stage image)
    var mainProductImg = document.getElementById('bv-main-product-img');
    var thumbButtons = document.querySelectorAll('.bv-thumb-item');

    if (mainProductImg && thumbButtons.length > 0) {
        thumbButtons.forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var largeSrc = btn.getAttribute('data-large-src');
                if (!largeSrc || mainProductImg.src === largeSrc) return;

                thumbButtons.forEach(function (b) { b.classList.remove('is-active'); });
                btn.classList.add('is-active');

                mainProductImg.style.opacity = '0.3';
                setTimeout(function () {
                    mainProductImg.src = largeSrc;
                    mainProductImg.onload = function () {
                        mainProductImg.style.opacity = '1';
                    };
                }, 120);
            });
        });
    }

    // Single Add To Cart Stepper (+ / -)
    $(document).on('click', '.bv-quantity-step', function (e) {
        e.preventDefault();
        var stepBtn = $(this);
        var dir = parseInt(stepBtn.data('step'), 10) || 1;
        var input = stepBtn.siblings('.qty');
        if (!input.length) return;

        var val = parseInt(input.val(), 10) || 1;
        var min = parseInt(input.attr('min'), 10) || 1;
        var max = parseInt(input.attr('max'), 10) || 99;

        var newVal = val + dir;
        if (newVal >= min && newVal <= max) {
            input.val(newVal).trigger('change');
        }
    });

});
