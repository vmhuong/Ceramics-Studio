import sys
import json
from pathlib import Path

sys.stdout.reconfigure(encoding='utf-8')
sys.path.insert(0, str(Path(__file__).parent / 'vendor'))
from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(channel='chrome', headless=True)
    page = browser.new_page(viewport={'width': 1440, 'height': 1000}, device_scale_factor=1)
    errors = []
    page.on('pageerror', lambda error: errors.append(str(error)))
    for name, url in [('home', ''), ('shop', 'san-pham-gom/'), ('empty', 'gio-hang-gom/')]:
        page.goto('http://localhost/bookverse/' + url, wait_until='domcontentloaded')
        page.wait_for_timeout(1800)
        page.screenshot(path=str(Path(__file__).parent / (name + '-desktop.png')), full_page=True)
        print(name, 'main:', page.locator('main').inner_text()[:1800] if page.locator('main').count() else 'no main')
        print('header', page.locator('.elementor-92').inner_text()[:500])
    page.goto('http://localhost/bookverse/san-pham-gom/?add-to-cart=549', wait_until='domcontentloaded')
    page.goto('http://localhost/bookverse/gio-hang-gom/', wait_until='domcontentloaded')
    page.wait_for_timeout(1800)
    page.screenshot(path=str(Path(__file__).parent / 'cart-desktop.png'), full_page=True)
    print('cart', page.locator('main').inner_text()[:1800])
    page.goto('http://localhost/bookverse/thanh-toan-gom/', wait_until='domcontentloaded')
    page.wait_for_timeout(2500)
    page.screenshot(path=str(Path(__file__).parent / 'checkout-desktop.png'), full_page=True)
    print('checkout', page.locator('main').inner_text()[:1800])
    page.set_viewport_size({'width': 390, 'height': 844})
    for name, url in [('shop', 'san-pham-gom/'), ('cart', 'gio-hang-gom/'), ('checkout', 'thanh-toan-gom/')]:
        page.goto('http://localhost/bookverse/' + url, wait_until='domcontentloaded')
        page.wait_for_timeout(1800)
        page.screenshot(path=str(Path(__file__).parent / (name + '-mobile.png')), full_page=True)
        print(name, 'overflow', page.evaluate('document.documentElement.scrollWidth > innerWidth'))
        print('overflow elements', page.evaluate('Array.from(document.querySelectorAll("main *")).filter(e => e.getBoundingClientRect().right > innerWidth + 1).slice(0,12).map(e => [e.tagName, e.className])'))
    print('errors', json.dumps(errors))
    browser.close()
