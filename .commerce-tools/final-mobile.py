import sys
import re
from pathlib import Path
sys.stdout.reconfigure(encoding='utf-8')
sys.path.insert(0,str(Path(__file__).parent/'vendor'))
from playwright.sync_api import sync_playwright, expect
with sync_playwright() as p:
    browser=p.chromium.launch(channel='chrome',headless=True)
    page=browser.new_page(viewport={'width':320,'height':844})
    page.route(re.compile(r'https://(?:fonts\.(?:googleapis|gstatic)\.com|www\.redditstatic\.com|sc-static\.net)/'),lambda r:r.abort())
    page.goto('http://localhost/bookverse/san-pham-gom/',wait_until='domcontentloaded')
    page.locator('.wpr-mobile-toggle').click()
    page.wait_for_timeout(700)
    menu=page.locator('.wpr-mobile-nav-menu').bounding_box()
    header=page.locator('.bv-site-header').bounding_box()
    assert menu['y']>=header['y']+header['height']-1, (menu,header)
    assert page.evaluate('document.documentElement.scrollWidth<=innerWidth')
    page.screenshot(path=str(Path(__file__).parent/'menu-mobile.png'))
    print('PASS: Mobile menu opens below header without overflow.')
    page.set_viewport_size({'width':390,'height':844})
    page.goto('http://localhost/bookverse/thanh-toan-gom/?add-to-cart=549',wait_until='domcontentloaded')
    page.wait_for_timeout(3500)
    expect(page.locator('.blockUI:visible')).to_have_count(0,timeout=20000)
    page.screenshot(path=str(Path(__file__).parent/'checkout-mobile.png'),full_page=True)
    print('PASS: Mobile checkout settles after AJAX and renders.')
    browser.close()
