import sys
from pathlib import Path
root=Path(__file__).parent
sys.stdout.reconfigure(encoding='utf-8')
sys.path.insert(0,str(root/'vendor'))
from playwright.sync_api import sync_playwright
with sync_playwright() as p:
    browser=p.chromium.launch(channel='chrome',headless=True)
    page=browser.new_page(viewport={'width':1440,'height':1000})
    page.route('**/*',lambda r:r.continue_() if r.request.url.startswith('http://localhost/') else r.abort())
    page.goto('http://localhost/bookverse/san-pham-gom/?add-to-cart=567',wait_until='domcontentloaded')
    page.locator('.bv-header-cart').click()
    page.wait_for_timeout(1500)
    print(page.locator('.woocommerce-mini-cart').inner_html(),flush=True)
    print(page.locator('.woocommerce-mini-cart, .woocommerce-mini-cart li, .woocommerce-mini-cart li a, .woocommerce-mini-cart img').evaluate_all('(es)=>es.map(e=>({tag:e.tagName,cls:e.className,style:e.getAttribute("style"),rect:e.getBoundingClientRect().toJSON(),display:getComputedStyle(e).display,position:getComputedStyle(e).position,overflow:getComputedStyle(e).overflow}))'),flush=True)
    page.screenshot(path=str(root/'diagnose-panel.png'))
    browser.close()
