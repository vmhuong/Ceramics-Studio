import sys
from pathlib import Path
sys.stdout.reconfigure(encoding='utf-8')
root = Path(__file__).parent
sys.path.insert(0, str(root / 'vendor'))
from playwright.sync_api import sync_playwright
with sync_playwright() as p:
    browser = p.chromium.launch(channel='chrome', headless=True)
    page = browser.new_page(viewport={'width':1440,'height':1000})
    page.goto('https://shop.anhceramics.vn/', wait_until='domcontentloaded')
    for target in ['account', 'cart']:
        page.locator('#site-' + target + '-handle').click()
        page.wait_for_timeout(500)
        page.screenshot(path=str(root / ('reference-panel-' + target + '.png')))
        page.keyboard.press('Escape')
    link = page.get_by_role('link', name='BỘ SETTING XÔNG THƠM NHÀ', exact=True).first
    link.click()
    print('PRODUCT BUTTONS', page.locator('button').all_text_contents(), flush=True)
    print('PRODUCT URL', page.url, flush=True)
    page.screenshot(path=str(root / 'reference-product.png'))
    add = page.locator('#add-to-cart')
    if add.count():
        add.click()
        page.wait_for_timeout(1500)
        page.screenshot(path=str(root / 'reference-added.png'))
        page.goto('https://shop.anhceramics.vn/cart', wait_until='domcontentloaded')
        print('CART',page.locator('main').inner_text(),flush=True)
        page.screenshot(path=str(root / 'reference-cart-filled.png'), full_page=True)
    browser.close()
