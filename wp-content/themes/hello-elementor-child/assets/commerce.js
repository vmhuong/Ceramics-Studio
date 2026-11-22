jQuery(function ($) {
    'use strict';

    var account = document.querySelector('body.woocommerce-account #customer_login');
    if (account) {
        var panels = [account.querySelector('.u-column1'), account.querySelector('.u-column2')];
        if (panels.every(Boolean)) {
            var tabs = document.createElement('div');
            tabs.className = 'bv-account-tabs';
            tabs.setAttribute('role', 'tablist');
            tabs.setAttribute('aria-label', 'Tài khoản');
            ['Đăng nhập', 'Đăng ký'].forEach(function (label, index) {
                var tab = document.createElement('button');
                tab.type = 'button';
                tab.textContent = label;
                tab.id = 'bv-account-tab-' + index;
                tab.setAttribute('role', 'tab');
                tab.setAttribute('aria-controls', 'bv-account-panel-' + index);
                panels[index].id = 'bv-account-panel-' + index;
                panels[index].setAttribute('role', 'tabpanel');
                panels[index].setAttribute('aria-labelledby', tab.id);
                tab.addEventListener('click', function () { activate(index); });
                tab.addEventListener('keydown', function (event) {
                    var next = event.key === 'Home' ? 0 : event.key === 'End' ? 1 : ['ArrowLeft', 'ArrowRight'].includes(event.key) ? 1 - index : null;
                    if (next !== null) { event.preventDefault(); activate(next); tabs.children[next].focus(); }
                });
                tabs.appendChild(tab);
            });
            function activate(index) {
                var heading = document.querySelector('.bv-account-guest .bv-commerce-heading h1');
                if (heading) heading.textContent = index === 1 ? 'Tạo tài khoản' : 'Đăng nhập';
                panels.forEach(function (panel, i) {
                    panel.hidden = i !== index;
                    tabs.children[i].setAttribute('aria-selected', String(i === index));
                    tabs.children[i].tabIndex = i === index ? 0 : -1;
                });
            }
            account.prepend(tabs);
            account.classList.add('bv-account-auth');
            activate(location.hash === '#register' || account.querySelector('#reg_email').value ? 1 : 0);
            window.addEventListener('hashchange', function () { activate(location.hash === '#register' ? 1 : 0); });
        }
    }

    $(document).on('click', '.bv-quantity-step', function () {
        var input = this.parentElement.querySelector('input.qty');
        if (!input || input.disabled || input.readOnly || input.type === 'hidden') return;
        if (this.dataset.step === '1') input.stepUp();
        else input.stepDown();
        $(input).trigger('change');
    });

    function updateQuantityButtons() {
        document.querySelectorAll('.quantity').forEach(function (wrapper) {
            var input = wrapper.querySelector('input.qty');
            if (!input) return;
            wrapper.querySelectorAll('.bv-quantity-step').forEach(function (button) {
                button.hidden = input.type === 'hidden' || input.readOnly;
                button.disabled = input.disabled || (button.dataset.step === '1'
                    ? input.max !== '' && Number(input.value) >= Number(input.max)
                    : input.min !== '' && Number(input.value) <= Number(input.min));
            });
        });
    }

    $(document).on('change input', 'input.qty', updateQuantityButtons);
    $(document.body).on('updated_wc_div updated_checkout found_variation', updateQuantityButtons);
    updateQuantityButtons();
});
