import sys
from pathlib import Path
root=Path(__file__).parent
sys.path.insert(0,str(root/'vendor'))
from playwright.sync_api import sync_playwright,expect
with sync_playwright() as p:
    browser=p.chromium.launch(channel='chrome',headless=True)
    page=browser.new_page(viewport={'width':1440,'height':1000})
    page.route('**/*',lambda r:r.continue_() if r.request.url.startswith('http://localhost/') else r.abort())
    page.goto('http://localhost/bookverse/san-pham-gom/',wait_until='domcontentloaded')
    expect(page.locator('.wpr-grid-item')).to_have_count(12,timeout=20000)
    expect(page.locator('.bv-shop-intro')).to_have_count(0)
    expect(page.locator('.bv-shop-heading h1')).to_have_text('Sản phẩm')
    expect(page.locator('.bv-shop-breadcrumb a')).to_have_attribute('href','http://localhost/bookverse/')
    expect(page.locator('.bv-shop-breadcrumb [aria-current="page"]')).to_have_text('Sản phẩm')
    expect(page.locator('.wpr-grid-filters')).to_be_visible()
    page.wait_for_timeout(1500)
    boxes=page.locator('.wpr-grid-item').evaluate_all('(es)=>es.map(e=>e.getBoundingClientRect().toJSON())')
    assert max(b['y'] for b in boxes[:4])-min(b['y'] for b in boxes[:4])<2
    assert boxes[4]['y']>boxes[0]['y']+100
    assert page.locator('.wpr-grid-item img').evaluate_all('(es)=>es.every(e=>e.complete&&e.naturalWidth>0)')
    page.screenshot(path=str(root/'shop-restored-desktop.png'))
    page.set_viewport_size({'width':390,'height':844})
    page.wait_for_timeout(800)
    assert page.evaluate('document.documentElement.scrollWidth<=innerWidth')
    page.screenshot(path=str(root/'shop-restored-mobile.png'))
    browser.close()
    print('PASS: 12 products, four desktop columns, filters, images, intro removed and mobile layout.')
