import sys
from pathlib import Path
sys.stdout.reconfigure(encoding='utf-8')
root = Path(__file__).parent
sys.path.insert(0, str(root / 'vendor'))
from playwright.sync_api import sync_playwright, expect
expect.set_options(timeout=30000)
base = 'http://localhost:8080/'
product_url = base + 'san-pham/binh-gom-dang-ruou-bo-suu-tap-men-pastel/'
with sync_playwright() as p:
    browser = p.chromium.launch(channel='chrome', headless=True)
    page = browser.new_page(viewport={'width':1440,'height':1000})
    page.set_default_timeout(60000)
    page.route('**/*', lambda r: r.continue_() if r.request.url.startswith('http://localhost:8080/') else r.abort())
    page.goto(product_url, wait_until='domcontentloaded')
    expect(page.locator('.bv-product-option')).to_have_count(6)
    expect(page.locator('.single_add_to_cart_button')).to_be_enabled()
    expect(page.locator('.woocommerce-variation-price')).to_contain_text('285.000')
    expect(page.locator('.summary > p.price')).to_contain_text('285.000')
    gallery = page.locator('.woocommerce-product-gallery')
    summary = page.locator('.summary')
    assert gallery.bounding_box()['width'] > 500 and summary.bounding_box()['width'] > 500
    assert gallery.bounding_box()['x'] + gallery.bounding_box()['width'] < summary.bounding_box()['x']
    assert page.locator('.summary .woocommerce-tabs').count() == 1
    page.screenshot(path=str(root / 'product-detail-desktop.png'))
    page.locator('.flex-control-thumbs li').nth(1).locator('img').click()
    expect(page.locator('.flex-control-thumbs li').nth(1).locator('img')).to_have_class('flex-active')
    page.locator('.woocommerce-product-gallery__trigger').click()
    expect(page.locator('.pswp')).to_have_class(__import__('re').compile('.*pswp--open.*'))
    page.keyboard.press('Escape')
    page.get_by_role('button', name='Kích thước: Lớn', exact=True).click()
    page.get_by_role('button', name='Màu men: Xanh sương', exact=True).click()
    expect(page.locator('.woocommerce-variation-price')).to_contain_text('345.000')
    expect(page.locator('.summary > p.price')).to_contain_text('345.000')
    expect(page.locator('.flex-control-thumbs li').nth(2).locator('img')).to_have_class('flex-active')
    expect(page.locator('.sku')).to_contain_text('lon-3')
    expect(page.locator('#mau-men')).to_have_value('Xanh sương')
    expect(page.locator('.woocommerce-product-gallery__image img').first).to_have_attribute('src', __import__('re').compile('.*gom-demo-1-.*'))
    for width in [768,390,320]:
        page.set_viewport_size({'width':width,'height':900})
        page.evaluate('window.scrollTo(0, 0)')
        assert page.evaluate('document.documentElement.scrollWidth <= innerWidth'), ('overflow', width)
        assert gallery.bounding_box()['width'] > 240
        if width < 768:
            assert gallery.bounding_box()['y'] + gallery.bounding_box()['height'] <= summary.bounding_box()['y'] + 1
        page.screenshot(path=str(root / f'product-detail-{width}.png'))
    page.locator('input.qty').fill('2')
    page.locator('input.qty').dispatch_event('change')
    page.locator('.single_add_to_cart_button').click()
    expect(page.locator('.bv-cart-count')).to_have_text('2')
    page.goto(base + 'gio-hang-gom/', wait_until='domcontentloaded')
    expect(page.locator('.cart_item')).to_have_count(1)
    expect(page.locator('.cart_item')).to_contain_text('Xanh sương')
    expect(page.locator('.cart_item')).to_contain_text('Lớn')
    expect(page.locator('.cart_item .product-subtotal')).to_contain_text('690.000')
    page.locator('.cart_item .remove').click()
    expect(page.locator('.bv-cart-count')).to_have_text('0')
    print('PASS: gallery, zoom, variation price/SKU, responsive layout and exact variation added to cart.', flush=True)
    page.set_viewport_size({'width':1440,'height':1000})
    page.goto(base + 'san-pham-gom/', wait_until='domcontentloaded')
    cards = page.locator('.bv-shop-grid .wpr-grid-item')
    expect(cards).to_have_count(12)
    for total in [24,32]:
        page.locator('.wpr-load-more-btn').click()
        expect(cards).to_have_count(total)
    page.locator('.bv-shop-grid img').evaluate_all('(es)=>es.forEach(e=>e.loading="eager")')
    page.wait_for_function('Array.from(document.querySelectorAll(".bv-shop-grid img")).every(e=>e.complete && e.naturalWidth > 0)')
    page.evaluate('window.scrollTo(0,0)')
    page.wait_for_timeout(500)
    boxes = [cards.nth(i).bounding_box() for i in range(5)]
    assert max(b['y'] for b in boxes[:4]) - min(b['y'] for b in boxes[:4]) < 3
    assert boxes[4]['y'] > boxes[0]['y'] + 100
    page.screenshot(path=str(root / 'catalog-32-desktop.png'))
    # Exercise an unchanged simple-product cart form too.
    page.goto(base + 'san-pham/chen-cham-thap-9cm/', wait_until='domcontentloaded')
    expect(page.locator('.single_add_to_cart_button')).to_be_enabled()
    page.locator('.single_add_to_cart_button').click()
    expect(page.locator('.bv-cart-count')).to_have_text('1')
    page.goto(base + 'gio-hang-gom/', wait_until='domcontentloaded')
    page.locator('.cart_item .remove').click()
    expect(page.locator('.bv-cart-count')).to_have_text('0')
    print('PASS: all 32 products load, images render, four desktop columns, and simple add/remove works.', flush=True)
    browser.close()
