import sys
import json
from pathlib import Path
sys.stdout.reconfigure(encoding='utf-8')
sys.path.insert(0, str(Path(__file__).parent / 'vendor'))
from playwright.sync_api import sync_playwright, expect
expect.set_options(timeout=20000)

ROOT = 'http://localhost/bookverse/'
OUT = Path(__file__).parent

with sync_playwright() as p:
    browser = p.chromium.launch(channel='chrome', headless=True)
    context = browser.new_context(viewport={'width': 1440, 'height': 1000})
    page = context.new_page()
    page.set_default_timeout(20000)
    errors = []
    page.on('pageerror', lambda e: errors.append(str(e)))

    def visit(slug):
        page.goto(ROOT + slug, wait_until='domcontentloaded')
        page.wait_for_timeout(1200)

    def screenshot(name, full=False):
        page.screenshot(path=str(OUT / (name + '.png')), full_page=full)

    def badge(count):
        expect(page.locator('.bv-cart-count')).to_have_text(str(count))
        expect(page.locator('.bv-header-cart')).to_have_attribute('aria-label', 'Giỏ hàng, %s sản phẩm' % count)

    def fit():
        assert page.evaluate('document.documentElement.scrollWidth <= innerWidth'), 'Horizontal overflow'
        cart = page.locator('.bv-header-cart').bounding_box()
        booking = page.locator('#regester').bounding_box()
        assert cart and booking and cart['x'] + cart['width'] <= booking['x'] + 1, 'Header controls overlap'

    visit('san-pham-gom/')
    expect(page.locator('.bv-shop-grid .wpr-grid-item')).to_have_count(4)
    badge(0)
    fit()
    page.wait_for_timeout(1300)
    print('PRODUCT POSITIONS', page.locator('.bv-shop-grid .wpr-grid-item').evaluate_all('(items) => items.map(i => ({x:i.getBoundingClientRect().x,y:i.getBoundingClientRect().y,w:i.getBoundingClientRect().width}))'))
    screenshot('shop-desktop', True)
    page.locator('.bv-shop-grid a.ajax_add_to_cart[data-product_id="549"]').click()
    badge(1)
    page.locator('.bv-shop-grid a.ajax_add_to_cart[data-product_id="550"]').click()
    badge(2)
    assert page.url == ROOT + 'san-pham-gom/', 'AJAX add navigated away'
    print('PASS: Royal v2 AJAX add-to-cart updates badge without navigation.')

    filter_link = page.locator('.wpr-grid-filters a').filter(has_text='Ly gốm').first
    if filter_link.count():
        filter_link.click()
        page.wait_for_timeout(800)
        assert page.locator('.wpr-grid-item:visible').count() == 1
        page.locator('.wpr-grid-filters a').filter(has_text='Tất cả sản phẩm').first.click()
    print('PASS: Product category filtering.')

    page.locator('.bv-header-cart').click()
    page.wait_for_load_state('domcontentloaded')
    page.wait_for_timeout(1200)
    expect(page.locator('.cart_item')).to_have_count(2)
    row = page.locator('.cart_item').filter(has_text='Ly gốm men tro')
    row.locator('[data-step="1"]').click()
    expect(row.locator('input.qty')).to_have_value('2')
    page.locator('button[name="update_cart"]').click()
    badge(3)
    page.locator('.cart_item').filter(has_text='Bình hoa').locator('a.remove').click()
    badge(2)
    expect(page.locator('.cart_item')).to_have_count(1)
    assert '390.000' in page.locator('.order-total').inner_text()
    screenshot('cart-desktop', True)
    print('PASS: Quantity update, remove item, shipping and totals (390,000 VND).')

    visit('san-pham/ly-gom-men-tro-thu-cong-2/')
    expect(page.locator('.single_add_to_cart_button')).to_be_visible()
    expect(page.locator('.woocommerce-product-gallery img').first).to_be_visible()
    screenshot('product-desktop')
    badge(2)
    print('PASS: Product detail and cart persistence.')

    visit('thanh-toan-gom/')
    expect(page.get_by_placeholder('Nhập họ và tên', exact=True)).to_be_visible()
    page.locator('#select2-billing_state-container').click()
    page.locator('.select2-results__option').filter(has_text='Thành phố Hà Nội').click()
    page.wait_for_function('document.querySelector("#billing_city").options.length > 1')
    ward = page.locator('#billing_city').evaluate('(e) => e.options[1].textContent')
    page.locator('#select2-billing_city-container').click()
    page.locator('.select2-results__option').filter(has_text=ward).first.click()
    page.locator('#payment_method_cod').check()
    page.wait_for_timeout(1500)
    expect(page.locator('#payment_method_cod')).to_be_checked()
    expect(page.locator('#place_order')).to_be_visible()
    print('ADDRESS OPTIONS', page.locator('#billing_city').evaluate('(e) => e.options.length'))
    screenshot('checkout-desktop', True)
    print('PASS: Province/ward selection, COD and checkout summary.')

    for width in [1024, 768, 390, 320]:
        page.set_viewport_size({'width': width, 'height': 900 if width >= 768 else 844})
        for name, slug in [('shop', 'san-pham-gom/'), ('cart', 'gio-hang-gom/'), ('checkout', 'thanh-toan-gom/')]:
            visit(slug)
            badge(2)
            fit()
            if width == 390:
                screenshot(name + '-mobile', True)
        print('PASS: No overflow or header overlap at width', width)

    visit('gio-hang-gom/')
    page.locator('a.remove').click()
    badge(0)
    expect(page.locator('.bv-empty-cart')).to_be_visible()
    screenshot('empty-mobile', True)
    print('PASS: Empty cart resets counter.')
    print('EXISTING PAGE ERRORS', json.dumps(sorted(set(errors))))
    browser.close()
