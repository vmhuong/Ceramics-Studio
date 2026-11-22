import sys
from pathlib import Path
sys.stdout.reconfigure(encoding='utf-8')
sys.path.insert(0, str(Path(__file__).parent / 'vendor'))
from playwright.sync_api import sync_playwright, expect
with sync_playwright() as p:
    browser = p.chromium.launch(channel='chrome', headless=True)
    page = browser.new_page()
    page.set_default_timeout(60000)
    page.route('**/*', lambda r: r.continue_() if r.request.url.startswith('http://localhost/') else r.abort())
    page.goto('http://localhost/bookverse/san-pham-gom/', wait_until='domcontentloaded')
    page.locator('a.ajax_add_to_cart[data-product_id="567"]').first.click()
    expect(page.locator('.bv-cart-count')).to_have_text('1', timeout=60000)
    page.locator('.remove_from_cart_button').click()
    expect(page.locator('.bv-cart-count')).to_have_text('0', timeout=60000)
    page.keyboard.press('Escape')
    page.locator('a.ajax_add_to_cart[data-product_id="567"]').first.click()
    expect(page.locator('.bv-cart-count')).to_have_text('1', timeout=60000)
    page.wait_for_timeout(3000)
    print('AJAX COOKIES', [(c['name'], c['path'], c['expires']) for c in page.context.cookies() if 'woocommerce' in c['name']], flush=True)
    for step in ['gio-hang-gom/', 'gio-hang-gom/']:
        response = page.goto('http://localhost/bookverse/' + step, wait_until='domcontentloaded')
        print('PAGE', response.status, page.url, 'rows', page.locator('.cart_item').count(), 'empty', page.locator('.bv-empty-cart').count(), flush=True)
        print('COOKIES', [(c['name'], c['path'], c['expires']) for c in page.context.cookies() if 'woocommerce' in c['name']], flush=True)
        print('BODY', page.locator('body').inner_text()[:700], flush=True)
    browser.close()
