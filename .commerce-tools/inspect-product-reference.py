import sys
from pathlib import Path
sys.stdout.reconfigure(encoding='utf-8')
root = Path(__file__).parent
sys.path.insert(0, str(root / 'vendor'))
from playwright.sync_api import sync_playwright
with sync_playwright() as p:
    browser = p.chromium.launch(channel='chrome', headless=True)
    page = browser.new_page(viewport={'width':1440,'height':1000})
    page.goto('https://shop.anhceramics.vn/products/vase-02-medium-binh-ruou-trung-vs02-m', wait_until='domcontentloaded', timeout=60000)
    page.wait_for_timeout(2000)
    page.screenshot(path=str(root / 'reference-vase-desktop.png'), full_page=True)
    page.set_viewport_size({'width':390,'height':844})
    page.screenshot(path=str(root / 'reference-vase-mobile.png'), full_page=True)
    browser.close()
