import sys
from pathlib import Path
sys.stdout.reconfigure(encoding='utf-8')
root = Path(__file__).parent
sys.path.insert(0, str(root / 'vendor'))
from playwright.sync_api import sync_playwright
with sync_playwright() as p:
    browser = p.chromium.launch(channel='chrome', headless=True)
    page = browser.new_page(viewport={'width': 1440, 'height': 1000})
    for slug in ['', 'account/login', 'account/register', 'cart']:
        page.goto('https://shop.anhceramics.vn/' + slug, wait_until='domcontentloaded', timeout=45000)
        page.wait_for_timeout(1500)
        page.screenshot(path=str(root / ('reference-' + (slug.replace('/', '-') or 'home') + '.png')), full_page=True)
        print(slug, page.locator('main').inner_text() if page.locator('main').count() else page.locator('body').inner_text()[:6000], flush=True)
        if not slug:
            print(page.locator('a,button').evaluate_all('(es)=>es.filter(e=>/Tài khoản|Giỏ hàng/.test(e.textContent)).map(e=>({tag:e.tagName,cls:e.className,id:e.id,text:e.textContent.trim().slice(0,100)}))'), flush=True)
    browser.close()
