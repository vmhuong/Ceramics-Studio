import sys
import re
import json
import subprocess
from pathlib import Path
sys.stdout.reconfigure(encoding='utf-8')
root = Path(__file__).parent
sys.path.insert(0, str(root / 'vendor'))
from playwright.sync_api import sync_playwright, expect
php = r'D:\usbwebserver_v8.6.5\usbwebserver\php\php.exe'
fixture = json.loads(subprocess.check_output([php, str(root / 'account-fixture.php')], text=True))
assert not fixture['can_manage_options']
try:
    with sync_playwright() as p:
        browser = p.chromium.launch(channel='chrome', headless=True)
        page = browser.new_page(viewport={'width': 1440, 'height': 1000})
        page.route(re.compile(r'https://(?:fonts\.(?:googleapis|gstatic)\.com|www\.redditstatic\.com|sc-static\.net)/'), lambda r: r.abort())
        page.goto('http://localhost/bookverse/tai-khoan/', wait_until='domcontentloaded')
        expect(page.locator('.bv-account-tabs')).to_be_visible()
        expect(page.locator('.bv-header-account')).not_to_have_class(re.compile('is-signed-in'))
        page.get_by_role('tab', name='Đăng ký', exact=True).click()
        expect(page.locator('#reg_email')).to_be_visible()
        expect(page.locator('#reg_password')).to_be_visible()
        page.screenshot(path=str(root / 'account-register.png'), full_page=True)
        page.get_by_role('tab', name='Đăng nhập', exact=True).click()
        page.screenshot(path=str(root / 'account-login-new.png'), full_page=True)
        page.locator('#username').fill(fixture['login'])
        page.locator('#password').fill('invalid-test-password')
        page.locator('.bv-commerce-shell button[name="login"]').click()
        expect(page.locator('.woocommerce-error')).to_be_visible()
        page.locator('#password').fill(fixture['password'])
        page.locator('.bv-commerce-shell button[name="login"]').click()
        expect(page.locator('.woocommerce-MyAccount-navigation')).to_be_visible(timeout=20000)
        page.wait_for_timeout(2500)
        expect(page.locator('.bv-header-account')).to_have_class(re.compile('is-signed-in'))
        expect(page.locator('.bv-account-status')).to_be_visible()
        page.screenshot(path=str(root / 'account-dashboard.png'), full_page=True)
        for endpoint in ['orders', 'edit-address', 'edit-account']:
            page.goto('http://localhost/bookverse/tai-khoan/' + endpoint + '/', wait_until='domcontentloaded')
            expect(page.locator('.woocommerce-MyAccount-content')).to_be_visible()
            page.screenshot(path=str(root / ('account-' + endpoint + '-new.png')), full_page=True)
        page.locator('#account_first_name').fill('Khách')
        page.locator('#account_last_name').fill('Thử nghiệm')
        page.locator('button[name="save_account_details"]').click()
        expect(page.locator('.woocommerce-message')).to_be_visible(timeout=20000)
        expect(page.locator('#account_first_name')).to_have_value('Khách')
        for width in [1024, 768, 390, 320]:
            page.set_viewport_size({'width': width, 'height': 900})
            page.wait_for_timeout(600)
            assert page.evaluate('document.documentElement.scrollWidth <= innerWidth'), width
            cart = page.locator('.bv-header-cart').bounding_box()
            account = page.locator('.bv-header-account').bounding_box()
            booking = page.locator('#regester').bounding_box()
            assert cart['x'] + cart['width'] <= account['x'] + 1, (width, cart, account)
            assert account['x'] + account['width'] <= booking['x'] + 1, (width, account, booking)
        page.screenshot(path=str(root / 'account-mobile.png'), full_page=True)
        page.locator('.woocommerce-MyAccount-navigation-link--customer-logout a').click()
        expect(page.locator('#username')).to_be_visible(timeout=20000)
        expect(page.locator('.bv-header-account')).not_to_have_class(re.compile('is-signed-in'))
        page.goto('http://localhost/bookverse/tai-khoan/orders/', wait_until='domcontentloaded')
        expect(page.locator('#username')).to_be_visible()
        page.goto('http://localhost/bookverse/tai-khoan/lost-password/', wait_until='domcontentloaded')
        expect(page.locator('#user_login')).to_be_visible()
        browser.close()
        print('PASS: login errors, login/logout state, account endpoints, profile save, guest protection, reset form and responsive header.')
finally:
    subprocess.run([php, str(root / 'account-fixture.php'), 'delete', str(fixture['id'])], check=True)
