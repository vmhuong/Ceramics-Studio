(function () {
    'use strict';
    function init() {
        if (document.body.classList.contains('elementor-editor-active')) return;
        var section = document.querySelector('.bv-site-header');
        var header = section && section.closest('.elementor');
        if (!header || header.classList.contains('bv-sticky-header')) return;
        var root = document.documentElement;
        var spacer = document.createElement('div');
        spacer.className = 'bv-header-spacer';
        spacer.setAttribute('aria-hidden', 'true');
        function measure() {
            root.style.setProperty('--bv-header-height', header.getBoundingClientRect().height + 'px');
        }
        function updateScroll() {
            var bar = document.getElementById('wpadminbar');
            var offset = bar ? Math.max(0, bar.getBoundingClientRect().bottom) : 0;
            root.style.setProperty('--bv-admin-offset', offset + 'px');
            header.classList.toggle('is-scrolled', window.scrollY > 8);
            if (window.scrollY > 96) header.classList.add('is-compact');
            else if (window.scrollY < 32) header.classList.remove('is-compact');
        }
        measure();
        header.before(spacer);
        header.classList.add('bv-sticky-header');
        root.classList.add('bv-has-sticky-header');
        updateScroll();
        if ('ResizeObserver' in window) new ResizeObserver(measure).observe(header);
        window.addEventListener('resize', function () { measure(); updateScroll(); });
        var scheduled = false;
        window.addEventListener('scroll', function () {
            if (scheduled) return;
            scheduled = true;
            requestAnimationFrame(function () { updateScroll(); scheduled = false; });
        }, {passive: true});
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
    else init();
}());
