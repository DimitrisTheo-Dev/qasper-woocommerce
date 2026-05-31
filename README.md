# Qasper WooCommerce

WordPress plugin for the Qasper WooCommerce storefront integration.

## What it does

- Injects the hosted Qasper widget with `channel: "woocommerce"` and the configured `storeConnectionId`.
- Sends only sanitized page context. Product URLs are generated from WordPress permalinks, so query strings and fragments are not forwarded.
- Bridges add-to-cart requests through a WordPress REST endpoint.
- Verifies cart requests by calling Qasper cart-intent introspection. No Qasper signing secret is stored in WordPress or exposed to the browser.
- Re-checks WooCommerce product purchasability, stock, backorder, and quantity state immediately before adding to cart.

## Settings

Open WooCommerce > Qasper and configure:

- Business slug
- Store connection ID from the Qasper WooCommerce connection
- Qasper base URL. HTTPS is required except for localhost development URLs.
- Enable assistant

## License

GPL-3.0-or-later.
