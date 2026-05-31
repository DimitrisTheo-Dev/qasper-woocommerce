=== Qasper WooCommerce ===
Contributors:      qasperai
Tags:              woocommerce, ecommerce, ai, chat, cart
Requires at least: 6.4
Tested up to:      7.0
Requires PHP:      7.4
Stable tag:        0.1.3
License:           GPLv3 or later
License URI:       https://www.gnu.org/licenses/gpl-3.0.html

Embed the Qasper storefront assistant in WooCommerce and securely bridge cart actions.

== Description ==

Qasper WooCommerce adds the Qasper storefront assistant to a WooCommerce store. It passes sanitized storefront context to Qasper, supports product-aware chat, and lets verified Qasper cart intents add products to the local WooCommerce cart.

= Features =

* Site-wide Qasper storefront assistant for WooCommerce.
* Product, catalog, cart, checkout, and page context with query strings and fragments stripped from URLs.
* Secure cart bridge that verifies each cart intent with Qasper before changing the WooCommerce cart.
* WooCommerce-side product checks before add-to-cart: purchasability, stock, backorder, variation, and quantity state.
* HTTPS-only Qasper endpoint configuration, with HTTP allowed only for localhost or loopback development URLs.

== External services ==

This plugin relies on Qasper, an external service operated by Qasper at https://qasper.ai. Qasper is the AI storefront assistant platform that powers the widget this plugin embeds. A Qasper account and WooCommerce store connection are required to use it.

The service is contacted when the assistant is enabled and a storefront page is viewed.

What is sent, and when:

* When a page with the assistant enabled is loaded, the visitor's browser requests the widget script from the configured Qasper base URL, which defaults to `https://qasper.ai/embed/qasper-widget.js`. As with any externally hosted script, this request transmits the visitor's IP address and user agent to Qasper.
* The configured business slug, WooCommerce store connection ID, widget channel, cart bridge nonce, and sanitized page context are passed to the widget so Qasper can open the correct assistant for the store.
* Product page context includes the WooCommerce product ID, product slug, locale, page type, and product permalink with query strings and fragments removed.
* When the assistant asks to add an item to the cart, the plugin sends the one-time cart intent token, the cart bridge nonce, and the store connection ID to Qasper for validation before updating the WooCommerce cart.
* Chat messages are sent directly between the visitor and Qasper after the visitor uses the assistant.

Use of the Qasper service is subject to its terms and privacy policy:

* Terms of Service: https://qasper.ai/terms
* Privacy Policy: https://qasper.ai/privacy

Because the widget contacts Qasper, add Qasper to your site's privacy policy and, if you use a cookie or consent banner, list it under the categories your visitors must consent to.

== Installation ==

1. Upload `qasper-woocommerce` to `/wp-content/plugins/`, or install the ZIP through Plugins -> Add New -> Upload Plugin.
2. Activate WooCommerce and Qasper WooCommerce.
3. In Qasper, connect the WooCommerce store and copy the business slug and store connection ID.
4. In WordPress, go to WooCommerce -> Qasper.
5. Enter the business slug, store connection ID, and Qasper base URL.
6. Enable the assistant.

== Frequently Asked Questions ==

= Does this plugin require WooCommerce? =

Yes. The plugin only registers when WooCommerce is active.

= Does this plugin require a Qasper account? =

Yes. You need a Qasper business profile and WooCommerce store connection.

= Does the plugin store a Qasper signing secret in WordPress? =

No. The plugin verifies cart actions by calling Qasper's cart-intent introspection endpoint. Private signing material remains on Qasper's backend.

= Can I use an HTTP Qasper base URL? =

Only for localhost or loopback development. Production Qasper URLs must use HTTPS.

= Does the plugin send full page URLs to Qasper? =

No. Product page context strips query strings and fragments before the URL is sent.

== Changelog ==

= 0.1.3 =
* Declare WooCommerce HPOS and Cart/Checkout Blocks compatibility for Marketplace review.

= 0.1.2 =
* Woo Marketplace submission package: PHP 7.4-compatible syntax, WordPress.org-format readme metadata, external-service disclosure, and release metadata alignment.

= 0.1.1 =
* Restrict Qasper base URLs to HTTPS except localhost or loopback development URLs.
* Preserve configured ports when validating the Qasper iframe origin.

= 0.1.0 =
* Initial public plugin release.
