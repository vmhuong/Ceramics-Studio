import sys
import re
from pathlib import Path
sys.path.insert(0, str(Path(__file__).parent / 'vendor'))
from playwright.sync_api import sync_playwright
with sync_playwright() as p:
    browser = p.chromium.launch(channel='chrome', headless=True)
    page = browser.new_page(viewport={'width': 1440, 'height': 1000})
    page.route(re.compile(r'https://(?:fonts\.(?:googleapis|gstatic)\.com|www\.redditstatic\.com|sc-static\.net)/'), lambda r: r.abort())
    for slug in (sys.argv[1:] or ['', 'san-pham-gom/', 'tai-khoan/', 'gio-hang-gom/', 'thanh-toan-gom/?add-to-cart=567', 'register/']):
        page.goto('http://localhost/bookverse/' + slug, wait_until='domcontentloaded')
        page.wait_for_timeout(800)
        if page.locator('.bv-site-header').count() == 0:
            print(page.locator('body').inner_text()[:1500], flush=True)
        colors = page.evaluate('''() => ({body:getComputedStyle(document.body).backgroundColor, header:getComputedStyle(document.querySelector('.bv-site-header')).backgroundColor, main:document.querySelector('main') ? getComputedStyle(document.querySelector('main')).backgroundColor : null})''')
        assert colors['body'] == colors['header'] == 'rgb(244, 240, 238)', (slug, colors)
        print(slug or 'home', colors, flush=True)
        if slug in ['san-pham-gom/', 'tai-khoan/']:
            page.screenshot(path=str(Path(__file__).parent / ('background-' + slug.strip('/') + '.png')))
    page.set_viewport_size({'width': 390, 'height': 844})
    page.goto('http://localhost/bookverse/tai-khoan/', wait_until='domcontentloaded')
    assert page.evaluate('document.documentElement.scrollWidth <= innerWidth')
    page.screenshot(path=str(Path(__file__).parent / 'background-mobile.png'))
    browser.close()
    print('PASS: shared header/background colors across pages and mobile layout.')
