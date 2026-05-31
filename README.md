# Qasper WooCommerce

WordPress plugin for the Qasper WooCommerce storefront integration.

Requires WordPress 6.4+, WooCommerce, and PHP 7.4+.

## What it does

- Injects the hosted Qasper widget with `channel: "woocommerce"` and the configured `storeConnectionId`.
- Sends only sanitized page context. Product URLs are generated from WordPress permalinks, so query strings and fragments are not forwarded.
- Bridges add-to-cart requests through a WordPress REST endpoint.
- Verifies cart requests by calling Qasper cart-intent introspection. No Qasper signing secret is stored in WordPress or exposed to the browser.
- Re-checks WooCommerce product purchasability, stock, backorder, and quantity state immediately before adding to cart.

## External services

This plugin connects to Qasper at `https://qasper.ai` by default. A Qasper account and WooCommerce store connection are required.

When enabled, the visitor's browser loads the Qasper widget script and chat iframe from Qasper. The widget receives the configured business slug, WooCommerce store connection ID, sanitized page context, and a cart bridge nonce. Product page context strips query strings and fragments before it is sent.

When a shopper asks Qasper to add a product to the cart, the plugin posts a one-time intent token to Qasper for validation, then re-checks WooCommerce stock, purchasability, variation, and quantity rules before updating the local WooCommerce cart.

Use of Qasper is subject to:

- Terms of Service: https://qasper.ai/terms
- Privacy Policy: https://qasper.ai/privacy

Because the widget contacts Qasper, add Qasper to your site's privacy policy and consent tooling as appropriate.

## Settings

Open WooCommerce > Qasper and configure:

- Business slug
- Store connection ID from the Qasper WooCommerce connection
- Qasper base URL. HTTPS is required except for localhost development URLs.
- Enable assistant

## License

GPL-3.0-or-later.
