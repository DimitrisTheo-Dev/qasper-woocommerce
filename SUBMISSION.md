# Woo Marketplace Submission Notes

## Product

- Product type: Extension
- Product name: Qasper WooCommerce
- Short description: Add the Qasper storefront assistant to WooCommerce and let verified AI cart intents update the local cart safely.
- Suggested highlight color: `#111827`
- Product icon: upload a 160x160 PNG or JPG product icon in the Woo vendor dashboard. Keep the main mark inside the central 112x112 safe area.

## Business Details

Qasper WooCommerce is a first-party integration for Qasper, an externally billed AI storefront assistant service. The plugin itself is distributed under GPLv3 or later and connects WooCommerce stores to a Qasper account.

Because this is an integration with an external SaaS, Woo's monetization rules require either the Woo SaaS Billing API or a Marketplace partnership agreement for externally billed services.

## Testing Instructions

1. Install and activate WooCommerce.
2. Install and activate Qasper WooCommerce.
3. In Qasper, create or open a WooCommerce store connection and copy the business slug and store connection ID.
4. In WordPress admin, go to WooCommerce -> Qasper.
5. Enter the business slug and store connection ID. Leave Qasper base URL as `https://qasper.ai` unless testing against a local Qasper development server.
6. Enable the assistant and save settings.
7. Visit a public product page and confirm the Qasper widget loads.
8. Ask the assistant to add a product to the cart.
9. Confirm WooCommerce receives the cart action only after Qasper validates the one-time cart intent.
10. Confirm the cart contains the selected product and quantity.

Expected security behavior:

- Non-HTTPS Qasper base URLs are rejected unless the host is localhost, 127.0.0.1, or ::1.
- Cart actions require a valid WordPress REST nonce, Qasper cart bridge nonce, and Qasper-validated intent token.
- Product URLs sent in page context do not include query strings or fragments.

## Submission Checklist

- Upload ZIP: `qasper-woocommerce-0.1.2.zip`
- Product upload: plugin ZIP.
- Business details: explain first-party Qasper SaaS integration and chosen Woo monetization path.
- Testing instructions: use the steps above.
- Product assets: 160x160 product icon, gallery screenshots at 896x550 or larger, and optional video.
