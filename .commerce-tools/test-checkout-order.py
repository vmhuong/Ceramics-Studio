import json
import re
import subprocess
import sys
from pathlib import Path
sys.stdout.reconfigure(encoding='utf-8')
root = Path(__file__).parent
sys.path.insert(0, str(root / 'vendor'))
from playwright.sync_api import sync_playwright, expect
expect.set_options(timeout=60000)
def fixture(command):
    return subprocess.check_output(['docker', 'exec', 'bookverse_php', 'php', '/var/www/html/.commerce-tools/checkout-fixture.php', command], text=True)
assert (root.parent / 'wp-content/mu-plugins/bv-checkout-test.php').is_file(), 'Install the local test mail guard before running.'
state = json.loads(fixture('create'))
customer = None
base = 'http://localhost:8080/'
try:
    with sync_playwright() as p:
        browser = p.chromium.launch(channel='chrome', headless=True)
        customer = json.loads(subprocess.check_output(['docker', 'exec', 'bookverse_php', 'php', '/var/www/html/.commerce-tools/account-fixture.php'], text=True))
        for mode in ['guest', 'customer']:
            gateway = 'cod'
            context = browser.new_context(extra_http_headers={'X-BV-Checkout-Test': state['token']}, viewport={'width':390 if mode == 'customer' else 1440, 'height':1000})
            context.route('**/*', lambda r: r.continue_() if r.request.url.startswith(base) else r.abort())
            page = context.new_page()
            page.set_default_timeout(60000)
            response = page.goto(base + f"gio-hang-gom/?add-to-cart={state['product']}&quantity=2", wait_until='domcontentloaded')
            assert response.headers.get('x-bv-test-mail') == 'blocked', 'Mail guard is not active.'
            if mode == 'customer':
                page.locator('.bv-header-account').click()
                page.locator('#bv-panel-username').fill(customer['login'])
                page.locator('#bv-panel-password').fill(customer['password'])
                page.locator('#bv-account-panel button[name="login"]').click()
                expect(page.locator('.woocommerce-MyAccount-navigation')).to_be_visible()
                page.goto(base + 'gio-hang-gom/', wait_until='domcontentloaded')
            expect(page.locator('.cart_item')).to_have_count(1)
            page.locator('#bv-cart-note').fill('BV isolated checkout test ' + gateway)
            page.locator('.checkout-button').click()
            expect(page.locator('#order_comments')).to_have_value('BV isolated checkout test ' + gateway)
            expect(page.locator('#payment_method_bacs')).to_have_count(0)
            if mode == 'guest':
                expect(page.locator('.blockUI:visible')).to_have_count(0)
                page.locator('#place_order').click()
                expect(page.locator('.woocommerce-NoticeGroup-checkout .woocommerce-error')).to_be_visible()
                assert not json.loads(fixture('inspect'))['orders']
            page.locator('#billing_last_name').fill('BV Test Customer')
            page.locator('#billing_phone').fill('0900000000')
            page.locator('#billing_email').fill('bv-checkout@example.invalid')
            page.locator('#billing_address_1').fill('BV test address - not a delivery')
            page.locator('#select2-billing_state-container').evaluate('e => e.scrollIntoView({block:"center"})')
            page.locator('#select2-billing_state-container').click()
            page.locator('.select2-search__field').fill('Hà Nội')
            page.keyboard.press('Enter')
            page.wait_for_function('document.querySelector("#billing_city").options.length > 1')
            ward = page.locator('#billing_city').evaluate('(e) => e.options[1].textContent')
            page.locator('#select2-billing_city-container').evaluate('e => e.scrollIntoView({block:"center"})')
            page.locator('#select2-billing_city-container').click()
            page.locator('.select2-search__field').fill(ward)
            page.keyboard.press('Enter')
            page.locator('#payment_method_' + gateway).check()
            if page.locator('#terms').count():
                page.locator('#terms').check()
            expect(page.locator('.blockUI:visible')).to_have_count(0)
            assert page.evaluate('document.documentElement.scrollWidth <= innerWidth')
            if mode == 'customer':
                page.set_viewport_size({'width':320, 'height':844})
                assert page.evaluate('document.documentElement.scrollWidth <= innerWidth')
                page.set_viewport_size({'width':390, 'height':1000})
            page.screenshot(path=str(root / ('verified-checkout-' + mode + '.png')), full_page=True)
            page.locator('#place_order').click()
            page.wait_for_url(re.compile('.*order-received.*'), wait_until='domcontentloaded')
            expect(page.locator('.woocommerce-order-overview')).to_be_visible()
            expect(page.locator('.bv-cart-count')).to_have_text('0')
            page.screenshot(path=str(root / ('verified-order-' + mode + '.png')), full_page=True)
            data = json.loads(fixture('inspect'))
            order = data['orders'][-1]
            assert order['payment'] == gateway and float(order['total']) == 360000, data
            assert float(order['shipping']) == 30000 and order['quantity'] == 2, data
            assert order['note'] == 'BV isolated checkout test ' + gateway, data
            assert order['address'] == 'BV test address - not a delivery', data
            assert order['status'] == ('processing' if gateway == 'cod' else 'on-hold'), data
            assert data['stock'] == 5 - 2 * len(data['orders']), data
            assert order['customer'] == (customer['id'] if mode == 'customer' else 0), data
            if mode == 'customer':
                page.goto(base + 'tai-khoan/orders/', wait_until='domcontentloaded')
                link = page.locator(f'a.woocommerce-button.view[href*="view-order/{order["id"]}"]').first
                expect(link).to_be_visible()
                page.screenshot(path=str(root / 'verified-account-orders.png'), full_page=True)
                link.click()
                expect(page.locator('.woocommerce-order-details')).to_be_visible()
                assert page.evaluate('document.documentElement.scrollWidth <= innerWidth')
                page.screenshot(path=str(root / 'verified-account-order.png'), full_page=True)
            print('PASS:', mode, 'COD order, totals, shipping, note, stock, ownership and empty cart.', flush=True)
            context.close()
        browser.close()
finally:
    print(fixture('delete'), flush=True)
    if customer:
        subprocess.run(['docker', 'exec', 'bookverse_php', 'php', '/var/www/html/.commerce-tools/account-fixture.php', 'delete', str(customer['id'])], check=True)
