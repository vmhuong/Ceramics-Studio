import sys
import re
from pathlib import Path
sys.stdout.reconfigure(encoding='utf-8')
sys.path.insert(0, str(Path(__file__).parent / 'vendor'))
from playwright.sync_api import sync_playwright, expect

with sync_playwright() as p:
    browser = p.chromium.launch(channel='chrome', headless=True)
    page = browser.new_page(viewport={'width': 1440, 'height': 1000})
    page.route(re.compile(r'https://(?:fonts\.(?:googleapis|gstatic)\.com|www\.redditstatic\.com|sc-static\.net)/'), lambda r: r.abort())
    page.goto('http://localhost/bookverse/san-pham-gom/', wait_until='domcontentloaded')
    expect(page.locator('.wpr-grid-item')).to_have_count(12)
    page.wait_for_timeout(2500)
    boxes = page.locator('.wpr-grid-item').evaluate_all('(items)=>items.map(e=>{const r=e.getBoundingClientRect();return {x:r.x,y:r.y,width:r.width}})')
    print('Grid:', boxes)
    assert max(b['y'] for b in boxes[:4]) - min(b['y'] for b in boxes[:4]) < 2
    assert boxes[4]['y'] > boxes[0]['y'] + 100
    assert page.evaluate('document.documentElement.scrollWidth <= innerWidth')
    print('Booking:', page.locator('#regester').evaluate('(e)=>{const s=getComputedStyle(e);return {background:s.backgroundColor,radius:s.borderRadius}}'))
    page.screenshot(path=str(Path(__file__).parent / 'refined-shop-desktop.png'), full_page=True)
    for width in [390, 320]:
        page.set_viewport_size({'width': width, 'height': 844})
        page.wait_for_timeout(800)
        assert page.evaluate('document.documentElement.scrollWidth <= innerWidth'), width
        page.screenshot(path=str(Path(__file__).parent / ('refined-shop-' + str(width) + '.png')))
    page.set_viewport_size({'width': 1440, 'height': 1000})
    page.goto('http://localhost/bookverse/gio-hang-gom/?add-to-cart=567', wait_until='domcontentloaded')
    expect(page.locator('.cart_item')).to_have_count(1)
    expect(page.locator('.bv-cart-count').first).to_have_text('1', timeout=20000)
    page.screenshot(path=str(Path(__file__).parent / 'refined-cart.png'), full_page=True)
    page.goto('http://localhost/bookverse/thanh-toan-gom/', wait_until='domcontentloaded')
    expect(page.locator('#place_order')).to_be_visible()
    page.wait_for_timeout(3000)
    expect(page.locator('.blockUI:visible')).to_have_count(0, timeout=20000)
    print('Checkout button:', page.locator('#place_order').evaluate('(e)=>getComputedStyle(e).backgroundColor'))
    page.screenshot(path=str(Path(__file__).parent / 'refined-checkout.png'), full_page=True)
    browser.close()
    print('PASS: 12 products, four desktop columns, mobile overflow, cart count and checkout rendering.')
