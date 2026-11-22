import sys
from pathlib import Path
sys.stdout.reconfigure(encoding='utf-8')
root = Path(__file__).parent
sys.path.insert(0, str(root / 'vendor'))
from playwright.sync_api import sync_playwright
with sync_playwright() as p:
    browser = p.chromium.launch(channel='chrome', headless=True)
    page = browser.new_page(viewport={'width':1440,'height':1000})
    page.set_default_timeout(60000)
    page.route('**/*', lambda r: r.continue_() if r.request.url.startswith('http://localhost/') else r.abort())
    page.on('pageerror', lambda e: print('ERROR', e, flush=True))
    page.goto('http://localhost/bookverse/san-pham/binh-gom-dang-ruou-bo-suu-tap-men-pastel/', wait_until='domcontentloaded')
    page.wait_for_timeout(1800)
    print(page.locator('.summary').inner_text(), flush=True)
    print('options', page.locator('.variations select').evaluate_all('(es)=>es.map(e=>({name:e.name,value:e.value,options:Array.from(e.options).map(o=>({v:o.value,t:o.text}))}))'), flush=True)
    for selector in ['#product-604', '.summary', '.woocommerce-product-gallery', '.flex-viewport', '.flex-control-thumbs', '.flex-control-thumbs li', '.variations select']:
        print(selector, page.locator(selector).first.evaluate('(e)=>({box:e.getBoundingClientRect().toJSON(),display:getComputedStyle(e).display,grid:getComputedStyle(e).gridTemplateColumns,inline:e.getAttribute("style")})'), flush=True)
    page.screenshot(path=str(root / 'new-product-desktop.png'), full_page=True)
    page.set_viewport_size({'width':390,'height':844})
    page.screenshot(path=str(root / 'new-product-mobile.png'), full_page=True)
    browser.close()
