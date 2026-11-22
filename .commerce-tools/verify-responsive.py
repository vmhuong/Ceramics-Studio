import sys
import re
from pathlib import Path
sys.stdout.reconfigure(encoding='utf-8')
sys.path.insert(0, str(Path(__file__).parent / 'vendor'))
from playwright.sync_api import sync_playwright, expect
expect.set_options(timeout=20000)
BASE = 'http://localhost/bookverse/'
OUT = Path(__file__).parent
with sync_playwright() as p:
    browser = p.chromium.launch(channel='chrome', headless=True)
    page = browser.new_page(viewport={'width':1440,'height':1000})
    page.route(re.compile(r'https://(?:fonts\.(?:googleapis|gstatic)\.com|www\.redditstatic\.com|sc-static\.net)/'), lambda route: route.abort())
    page.set_default_navigation_timeout(60000)
    page.goto(BASE + 'gio-hang-gom/?add-to-cart=549&quantity=2', wait_until='domcontentloaded')
    page.goto(BASE + 'thanh-toan-gom/', wait_until='domcontentloaded')
    expect(page.get_by_placeholder('Nhập họ và tên', exact=True)).to_be_visible()
    page.locator('#select2-billing_state-container').click()
    page.locator('.select2-results__option').filter(has_text='Thành phố Hà Nội').click()
    page.wait_for_function('document.querySelector("#billing_city").options.length > 1')
    ward = page.locator('#billing_city').evaluate('(e) => e.options[1].textContent')
    page.locator('#select2-billing_city-container').click()
    page.locator('.select2-results__option').filter(has_text=ward).first.click()
    page.locator('#payment_method_cod').check()
    page.wait_for_timeout(3000)
    expect(page.locator('#payment_method_cod')).to_be_checked()
    print('PASS: Province and ward choices, COD selection, checkout fields.')
    page.screenshot(path=str(OUT/'checkout-desktop.png'), full_page=True)
    for width in [390, 320]:
        page.set_viewport_size({'width':width,'height':844})
        for name, slug in [('shop','san-pham-gom/'),('cart','gio-hang-gom/'),('checkout','thanh-toan-gom/')]:
            page.goto(BASE+slug,wait_until='domcontentloaded')
            page.wait_for_timeout(1200)
            assert page.evaluate('document.documentElement.scrollWidth <= innerWidth'), str((name,width,'overflow'))
            expect(page.locator('.bv-cart-count')).to_have_text('2')
            a=page.locator('.bv-header-cart').bounding_box()
            b=page.locator('#regester').bounding_box()
            assert a and b and a['x']+a['width']<=b['x']+1, str((name,width,'header overlap'))
            if width==390:
                page.screenshot(path=str(OUT/(name+'-mobile.png')),full_page=True)
            if width==320 and name=='shop':
                page.locator('.wpr-mobile-toggle').click()
                page.wait_for_timeout(500)
                expect(page.locator('.wpr-mobile-nav-menu').get_by_text('Cửa hàng', exact=True)).to_be_visible()
                page.screenshot(path=str(OUT/'menu-mobile.png'))
                page.locator('.wpr-mobile-toggle').click()
        print('PASS: Store/cart/checkout fit at width',width)
    page.goto(BASE+'gio-hang-gom/',wait_until='domcontentloaded')
    page.wait_for_timeout(1000)
    page.locator('a.remove').click()
    expect(page.locator('.bv-cart-count')).to_have_text('0')
    expect(page.locator('.bv-empty-cart')).to_be_visible()
    page.screenshot(path=str(OUT/'empty-mobile.png'),full_page=True)
    print('PASS: Empty cart and badge reset.')
    browser.close()
