<?php

if (!defined('ABSPATH')) {
    exit;
}

final class Qasper_WooCommerce_Rest_Controller
{
    private Qasper_WooCommerce_Settings $settings;

    public function __construct(Qasper_WooCommerce_Settings $settings)
    {
        $this->settings = $settings;
    }

    public function register(): void
    {
        add_action('rest_api_init', function (): void {
            register_rest_route('qasper-woocommerce/v1', '/cart/add', [
                'methods' => WP_REST_Server::CREATABLE,
                'callback' => [$this, 'add_to_cart'],
                'permission_callback' => '__return_true',
            ]);
        });
    }

    public function add_to_cart(WP_REST_Request $request): WP_REST_Response|WP_Error
    {
        if (!function_exists('WC') || WC()->cart === null) {
            return new WP_Error('qasper_woocommerce_cart_unavailable', 'WooCommerce cart is unavailable.', ['status' => 503]);
        }

        $payload = $request->get_json_params();
        if (!is_array($payload)) {
            return new WP_Error('qasper_woocommerce_invalid_json', 'Request body must be JSON.', ['status' => 400]);
        }

        if (!$this->is_valid_bridge_nonce($payload['nonce'] ?? null)) {
            return new WP_Error('qasper_woocommerce_nonce_rejected', 'Cart bridge nonce was rejected.', ['status' => 403]);
        }

        $intent = $this->introspect_intent($payload);
        if (is_wp_error($intent)) {
            return $intent;
        }

        $product_id = absint($intent['productId'] ?? 0);
        $variation_id = absint($intent['variationId'] ?? 0);
        $quantity = max(1, absint($intent['quantity'] ?? 1));
        $variation_attributes = is_array($intent['variationAttributes'] ?? null) ? $intent['variationAttributes'] : [];

        $product = wc_get_product($variation_id > 0 ? $variation_id : $product_id);
        if (!$product instanceof WC_Product || !$product->exists() || !$product->is_purchasable()) {
            return new WP_Error('qasper_woocommerce_product_not_purchasable', 'Product is not purchasable.', ['status' => 409]);
        }

        if (!$product->has_enough_stock($quantity) && !$product->backorders_allowed()) {
            return new WP_Error('qasper_woocommerce_stock_unavailable', 'Requested quantity is not available.', ['status' => 409]);
        }

        $parent_product_id = $variation_id > 0 ? $product_id : $product->get_id();
        $cart_item_key = WC()->cart->add_to_cart($parent_product_id, $quantity, $variation_id, $variation_attributes);
        if (!$cart_item_key) {
            return new WP_Error('qasper_woocommerce_add_to_cart_failed', 'Could not add item to cart.', ['status' => 409]);
        }

        return new WP_REST_Response([
            'success' => true,
            'cartItemKey' => $cart_item_key,
            'cartUrl' => wc_get_cart_url(),
        ]);
    }

    private function is_valid_bridge_nonce(mixed $nonce): bool
    {
        return is_string($nonce)
            && wp_verify_nonce($nonce, 'qasper_woocommerce_cart_bridge_' . $this->settings->store_connection_id()) !== false;
    }

    private function introspect_intent(array $payload): array|WP_Error
    {
        $intent_token = isset($payload['intentToken']) ? sanitize_text_field((string) $payload['intentToken']) : '';
        if ($intent_token === '') {
            return new WP_Error('qasper_woocommerce_intent_required', 'Cart intent token is required.', ['status' => 400]);
        }

        $response = wp_remote_post($this->settings->cart_introspection_url(), [
            'timeout' => 8,
            'headers' => ['Content-Type' => 'application/json'],
            'body' => wp_json_encode([
                'intentToken' => $intent_token,
                'nonce' => sanitize_text_field((string) ($payload['nonce'] ?? '')),
                'storeConnectionId' => $this->settings->store_connection_id(),
            ]),
        ]);

        if (is_wp_error($response)) {
            return new WP_Error('qasper_woocommerce_introspection_failed', 'Could not verify cart intent.', ['status' => 502]);
        }

        $status = wp_remote_retrieve_response_code($response);
        $body = json_decode(wp_remote_retrieve_body($response), true);
        if ($status !== 200 || !is_array($body) || empty($body['active'])) {
            return new WP_Error('qasper_woocommerce_intent_rejected', 'Cart intent was rejected.', ['status' => 403]);
        }

        return $body;
    }
}
