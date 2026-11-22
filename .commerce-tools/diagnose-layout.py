import sys
import json
from pathlib import Path
sys.stdout.reconfigure(encoding='utf-8')
sys.path.insert(0, str(Path(__file__).parent / 'vendor'))
from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(channel='chrome', headless=True)
    page = browser.new_page(viewport={'width': 320, 'height': 844})
    page.goto('http://localhost/bookverse/san-pham-gom/', wait_until='domcontentloaded')
    page.wait_for_timeout(1800)
    print('Shop:', page.locator('main').inner_text()[:1600])
    print('Overflow:', page.evaluate('Array.from(document.querySelectorAll("body *")).filter(e => e.getBoundingClientRect().right > innerWidth + 1 && getComputedStyle(e).display !== "none").slice(0,35).map(e => [e.tagName, e.className, Math.round(e.getBoundingClientRect().right)])'))
    page.screenshot(path=str(Path(__file__).parent / 'shop-mobile.png'), full_page=True)
    page.set_viewport_size({'width': 1440, 'height': 1000})
    page.screenshot(path=str(Path(__file__).parent / 'shop-desktop.png'), full_page=True)
    browser.close()
