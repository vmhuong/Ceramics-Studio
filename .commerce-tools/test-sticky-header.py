import sys
from pathlib import Path
root=Path(__file__).parent
sys.path.insert(0,str(root/'vendor'))
from playwright.sync_api import sync_playwright,expect
with sync_playwright() as p:
    browser=p.chromium.launch(channel='chrome',headless=True)
    page=browser.new_page(viewport={'width':1440,'height':900})
    page.route('**/*',lambda r:r.continue_() if r.request.url.startswith('http://localhost/') else r.abort())
    page.goto('http://localhost/bookverse/san-pham-gom/',wait_until='domcontentloaded')
    expect(page.locator('.bv-sticky-header')).to_be_visible()
    for width in [1440,768,390,320]:
        page.set_viewport_size({'width':width,'height':900})
        page.evaluate('scrollTo(0,0)')
        page.wait_for_timeout(500)
        header=page.locator('.bv-sticky-header').bounding_box()
        spacer=page.locator('.bv-header-spacer').bounding_box()
        assert abs(header['height']-spacer['height'])<2,(header,spacer)
        expanded=header['height']
        page.evaluate('scrollTo(0,700)')
        page.wait_for_timeout(500)
        header=page.locator('.bv-sticky-header').bounding_box()
        assert abs(header['y'])<2,header
        if header['height'] >= expanded - 8:
            print(page.locator('.bv-site-header *').evaluate_all('(es)=>es.filter(e=>e.getBoundingClientRect().height>55).map(e=>({cls:e.className,h:e.getBoundingClientRect().height,p:getComputedStyle(e).padding,min:getComputedStyle(e).minHeight}))'),flush=True)
        assert header['height'] < expanded - 8, (width, expanded, header)
        assert abs(page.locator('.bv-header-spacer').bounding_box()['height'] - expanded)<2
        assert abs(page.evaluate('scrollY')-700)<2
        assert page.evaluate('document.documentElement.scrollWidth<=innerWidth'),width
        page.screenshot(path=str(root/('sticky-'+str(width)+'.png')))
        page.evaluate('scrollTo(0,0)')
        page.wait_for_timeout(500)
        assert abs(page.locator('.bv-sticky-header').bounding_box()['height'] - expanded)<2
    page.evaluate('scrollTo(0,700)')
    page.wait_for_timeout(500)
    page.locator('.wpr-mobile-toggle').click()
    menu=page.locator('.wpr-mobile-nav-menu').bounding_box()
    header=page.locator('.bv-sticky-header').bounding_box()
    assert menu['y']>=header['y']+header['height']-2,(menu,header)
    page.locator('.wpr-mobile-toggle').click()
    page.locator('.bv-header-account').click()
    expect(page.locator('#bv-account-panel')).to_be_visible()
    page.keyboard.press('Escape')
    browser.close()
    print('PASS: fixed header, reserved height, desktop/mobile scrolling, mobile menu and account panel.')
