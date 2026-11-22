import sys
from pathlib import Path
sys.stdout.reconfigure(encoding='utf-8')
sys.path.insert(0, str(Path(__file__).parent / 'vendor'))
from playwright.sync_api import sync_playwright
with sync_playwright() as p:
    browser = p.chromium.launch(channel='chrome', headless=True)
    page = browser.new_page(viewport={'width':1440,'height':1000})
    page.on('pageerror', lambda e: print('ERROR', str(e)))
    page.goto('http://localhost/bookverse/gio-hang-gom/?add-to-cart=549', wait_until='domcontentloaded')
    page.wait_for_timeout(1500)
    print('Ready', page.evaluate('document.readyState'), 'qty', page.locator('input.qty').input_value())
    page.locator('[data-step="1"]').click()
    print('After click:', page.locator('input.qty').input_value(), 'disabled', page.locator('[name="update_cart"]').is_disabled())
    print('handlers', page.evaluate('Object.keys(jQuery._data(document,"events") || {})'))
    page.wait_for_timeout(1000)
    print('After wait:', page.locator('[name="update_cart"]').is_disabled())
    print(page.locator('input.qty').evaluate('(e) => e.outerHTML'))
    page.locator('[name="update_cart"]').click()
    page.wait_for_timeout(9000)
    print('Updated badge', page.locator('.bv-cart-count').inner_text(), 'quantity', page.locator('input.qty').input_value(), 'total', page.locator('.order-total').inner_text())
    browser.close()
