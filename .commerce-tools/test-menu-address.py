import sys,json,subprocess
from pathlib import Path
root=Path(__file__).parent
sys.path.insert(0,str(root/'vendor'))
from playwright.sync_api import sync_playwright,expect
php=r'D:\usbwebserver_v8.6.5\usbwebserver\php\php.exe'
fixture=json.loads(subprocess.check_output([php,str(root/'account-fixture.php')],text=True))
try:
    with sync_playwright() as p:
        browser=p.chromium.launch(channel='chrome',headless=True)
        page=browser.new_page(viewport={'width':1440,'height':900})
        page.route('**/*',lambda r:r.continue_() if r.request.url.startswith('http://localhost/') else r.abort())
        page.goto('http://localhost/bookverse/tai-khoan/',wait_until='domcontentloaded')
        expect(page.get_by_role('link',name='Đặt lịch',exact=True)).to_have_attribute('href','http://localhost/bookverse/register/')
        page.locator('#username').fill(fixture['login'])
        page.locator('#password').fill(fixture['password'])
        page.locator('.bv-commerce-shell button[name="login"]').click()
        expect(page.locator('.woocommerce-MyAccount-navigation')).to_be_visible(timeout=20000)
        page.goto('http://localhost/bookverse/tai-khoan/edit-address/',wait_until='domcontentloaded')
        expect(page.locator('.woocommerce-Address')).to_have_count(1)
        expect(page.locator('.woocommerce-Address h2')).to_contain_text('giao hàng')
        expect(page.locator('.woocommerce-Addresses a[href*="billing"]')).to_have_count(0)
        expect(page.locator('.woocommerce-Address a.edit')).to_have_attribute('href', 'http://localhost/bookverse/tai-khoan/edit-address/van-chuyen/')
        page.screenshot(path=str(root/'address-shipping-only.png'))
        page.set_viewport_size({'width':390,'height':844})
        page.locator('.wpr-mobile-toggle').click()
        expect(page.get_by_role('link',name='Đặt lịch',exact=True)).to_be_visible()
        assert page.evaluate('document.documentElement.scrollWidth<=innerWidth')
        page.screenshot(path=str(root/'booking-menu-mobile.png'))
        browser.close()
        print('PASS: booking menu on desktop/mobile; account shows shipping address only.')
finally:
    subprocess.run([php,str(root/'account-fixture.php'),'delete',str(fixture['id'])],check=True)
