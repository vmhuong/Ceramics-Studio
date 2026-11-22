import hashlib
import json
import sys
import time
import urllib.request
from html.parser import HTMLParser
from pathlib import Path
from urllib.parse import urljoin, urlparse

sys.stdout.reconfigure(encoding='utf-8')
ROOT = Path(__file__).parent
OUT = ROOT / 'pottery-catalog'
OUT.mkdir(exist_ok=True)
BASE = 'https://shop.anhceramics.vn'
REFERENCE = '/products/vase-02-medium-binh-ruou-trung-vs02-m'

def fetch(url):
    request = urllib.request.Request(url, headers={'User-Agent': 'Mozilla/5.0'})
    with urllib.request.urlopen(request, timeout=45) as response:
        return response.read()

class Links(HTMLParser):
    def __init__(self):
        super().__init__()
        self.links = []
    def handle_starttag(self, tag, attrs):
        href = dict(attrs).get('href', '').split('?')[0]
        if tag == 'a' and href.startswith('/products/') and href not in self.links:
            self.links.append(href)

links = [REFERENCE]
for page in [2, 3, 1]:
    parser = Links()
    parser.feed(fetch(BASE + '/collections/all?page=' + str(page)).decode('utf-8'))
    links.extend(href for href in parser.links if href not in links)

catalog = []
seen_images = set()
for href in links:
    if len(catalog) == 20:
        break
    try:
        data = json.loads(fetch(BASE + href + '.js'))
        if not data.get('images') or not data.get('price'):
            continue
        urls = list(dict.fromkeys(data['images']))[:3]
        if urls[0] in seen_images:
            continue
        assets = []
        for url in urls:
            url = urljoin(BASE, url)
            if urlparse(url).hostname != 'product.hstatic.net':
                continue
            extension = Path(urlparse(url).path).suffix.lower()
            if extension not in ['.jpg', '.jpeg', '.png', '.webp']:
                continue
            filename = hashlib.sha256(url.encode()).hexdigest()[:20] + extension
            target = OUT / filename
            if not target.exists():
                content = fetch(url)
                if len(content) > 15_000_000:
                    raise ValueError('Image is unexpectedly large')
                target.write_bytes(content)
            assets.append({'url': url, 'file': filename})
        if not assets:
            continue
        seen_images.add(urls[0])
        catalog.append({'source': BASE + href, 'handle': data['handle'], 'title': data['title'], 'price': int(data['price']) // 100, 'assets': assets})
        print(len(catalog), data['title'], len(assets), 'images', flush=True)
        time.sleep(0.15)
    except Exception as error:
        print('SKIP', href, str(error), flush=True)
assert len(catalog) == 20, 'Expected 20 distinct products'
(OUT / 'manifest.json').write_text(json.dumps(catalog, ensure_ascii=False, indent=2), encoding='utf-8')
print('Saved 20 products and source attribution manifest.', flush=True)
