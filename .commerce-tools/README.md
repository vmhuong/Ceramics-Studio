# Commerce UI

The storefront uses the official Royal Addons Woo Grid v2 import, not a custom imitation:
https://royal-elementor-addons.com/library/premade-styles/woo-grid/v2.json

Elementor documents:
- Header: 92. `[bookverse_header_cart]` renders cart and account icons. Booking is a menu item.
- Footer: 102. Corrected the logo's 177% width and local logo URL.
- Store: 553, `/san-pham-gom/`. Royal's layout control can switch grid/carousel/slider.
- Cart: 554, `/gio-hang-gom/`.
- Checkout: 555, `/thanh-toan-gom/`.

Presentation lives in `wp-content/themes/hello-elementor-child/inc/commerce.php`,
`assets/commerce.css`, `assets/commerce.js`, `woocommerce.php` and `commerce-page.php`.
WooCommerce owns session state, quantities, stock checks, fees and checkout handling.
The count includes quantities and refreshes through native WooCommerce fragments.

The original affected documents/options/menu were backed up to the non-autoloaded
WordPress option `bv_commerce_design_backup_20260914` before modification.
The legacy duplicate flat-rate shipping method was disabled. The Vietnam shipping
zone's 30,000 VND rate remains enabled. Coming Soon was disabled on localhost only.

`setup-commerce.php` is a CLI migration; rerunning it reapplies the supplied design.
Do not rerun after making subsequent Elementor edits unless that is intended.

`test-commerce.py` checks AJAX adds, category filters, quantity changes, removal,
product detail, checkout address/payment selection and responsive layouts.
`verify-responsive.py` covers checkout and responsive views independently; it blocks
remote font/advertising requests during testing. Neither submits a real order.
Use the locally installed Python with Playwright in `vendor` to run these scripts.
This directory is denied over HTTP by `.htaccess`.

## Checkout Verification

- Cart, checkout and account URLs are excluded from WordPress speculative loading.
  Prerendering an empty cart on header hover previously reused stale cart content.
- Mobile order history retains its row-header order numbers.
- COD is the only enabled payment method, as requested. Bank transfer is disabled;
  payment settings were backed up in `bv_payment_settings_before_cod_only`.
- `test-shopping-panels.py` covers add/remove/re-add, actual cart navigation,
  quantities, saved notes, login/logout and mobile account panels.
- `test-checkout-order.py` submits isolated COD orders for a guest and a customer,
  checks required-field validation, totals, shipping, notes, stock and order history.
  It requires the temporary MU mail guard from `checkout-test-mu.php`, installed as
  `wp-content/mu-plugins/bv-checkout-test.php` for the test run only. It refuses to
  submit without the guard response header. Remove the MU guard after testing.
  The guard requires a random token, loopback client and unexpired test fixture.
  Test products, orders and customers are removed by the fixture cleanup.
- Run browser suites sequentially: the local 32-bit Apache/PHP process can exhaust
  memory with concurrent WooCommerce/Elementor browser tests.
- Live email delivery is not verified by these tests; test mail is suppressed.

## Product Catalog And Detail Page

The 2026-09-23 catalog adds 20 demo products (`DEMO-CERAMICS-01` through `20`),
without replacing the existing 12 products. Product 604 has nine real WooCommerce
variations: three sizes and three glazes, with variation-specific images and stock.
The shop remains the Royal Woo Grid v2 with four desktop columns and 12-item
load-more batches. Images use uncropped large media so whole objects remain visible.

`fetch-pottery-catalog.py` downloads public reference photos and records source URLs
in `pottery-catalog/manifest.json`. Imported attachments also store the source page,
source image and demo-use notice in metadata. This is not a license grant: obtain
permission or replace reference images before commercial publication.
`import-pottery-catalog.php INDEX` imports one product, skips existing SKUs, and
uses WordPress media APIs. `finish-pottery-catalog.php` verifies the local images,
sets pagination, and keeps art products in the decoration category.

The child theme's `inc/product-detail.php` and `assets/product-detail.*` arrange the
native gallery, summary and tabs. Variation buttons enhance the native selects;
WooCommerce continues to handle image selection, prices, validation and cart data.
`test-product-detail.py` checks gallery thumbnails/lightbox, variation selection,
320/390/768px layouts, add/remove, 32-product pagination and all loaded grid images.

The gallery and summary share `.bv-product-main`, which bounds sticky scrolling
above upsells and related products. Purchase controls share a fixed-height row.
Successful product-page adds become a dismissible, eight-second status toast;
hover/focus pauses dismissal, and errors keep WooCommerce's normal notices.
`test-product-polish.py` checks scroll boundaries, short tabs, purchase alignment
and mobile toasts for both simple and variable products at 320/390/768/1440px.
