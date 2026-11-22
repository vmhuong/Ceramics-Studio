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
    expect(page.locator('.wpr-grid-item')).to_have_count(12)
    item=page.locator('.wpr-grid-item').first
    img=item.locator('.wpr-grid-image-wrap img').first
    button=item.locator('a.ajax_add_to_cart').first
    page.wait_for_timeout(1000)
    before=item.bounding_box()
    item.locator('.wpr-grid-media-hover-bg').hover()
    page.wait_for_timeout(550)
    scale=img.evaluate('(e)=>new DOMMatrix(getComputedStyle(e).transform).a')
    assert abs(scale-1.035)<0.003,scale
    button.hover()
    page.wait_for_timeout(300)
    assert button.evaluate('(e)=>getComputedStyle(e).backgroundColor')=='rgb(124, 41, 45)'
    assert button.evaluate('(e)=>getComputedStyle(e).color')=='rgb(255, 255, 255)'
    after=item.bounding_box()
    assert abs(before['width']-after['width'])<1 and abs(before['height']-after['height'])<1
    page.screenshot(path=str(root/'product-hover-desktop.png'))
    page.emulate_media(reduced_motion='reduce')
    assert img.evaluate('(e)=>getComputedStyle(e).transform')=='none'
    assert img.evaluate('(e)=>getComputedStyle(e).transitionDuration')=='0s'
    page.emulate_media(reduced_motion='no-preference')
    button.click()
    expect(page.locator('#bv-cart-panel')).to_be_visible(timeout=20000)
    expect(page.locator('.bv-cart-count')).to_have_text('1',timeout=20000)
    page.keyboard.press('Escape')
    page.set_viewport_size({'width':390,'height':844})
    page.wait_for_timeout(700)
    assert page.evaluate('document.documentElement.scrollWidth<=innerWidth')
    browser.close()
    print('PASS: image zoom, button hover, stable grid, reduced motion, cart add and mobile overflow.')
