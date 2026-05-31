<?php

if (!defined('ABSPATH')) {
    exit;
}

final class Qasper_WooCommerce_Plugin
{
    private Qasper_WooCommerce_Settings $settings;
    private Qasper_WooCommerce_Page_Context $page_context;
    private Qasper_WooCommerce_Snippet_Builder $snippet_builder;
    private Qasper_WooCommerce_Rest_Controller $rest_controller;

    public function __construct()
    {
        $this->settings = new Qasper_WooCommerce_Settings();
        $this->page_context = new Qasper_WooCommerce_Page_Context();
        $this->snippet_builder = new Qasper_WooCommerce_Snippet_Builder($this->settings, $this->page_context);
        $this->rest_controller = new Qasper_WooCommerce_Rest_Controller($this->settings);
    }

    public function register(): void
    {
        $this->settings->register();
        $this->rest_controller->register();
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_footer', [$this, 'print_widget_bootstrap'], 5);
    }

    public function enqueue_assets(): void
    {
        if (!$this->settings->is_enabled() || !$this->settings->has_required_widget_settings()) {
            return;
        }

        wp_enqueue_script(
            'qasper-widget',
            $this->settings->widget_script_url(),
            [],
            QASPER_WOOCOMMERCE_VERSION,
            true
        );

        wp_enqueue_script(
            'qasper-woocommerce-cart-bridge',
            QASPER_WOOCOMMERCE_URL . 'assets/qasper-woocommerce-cart-bridge.js',
            ['qasper-widget'],
            QASPER_WOOCOMMERCE_VERSION,
            true
        );

        wp_localize_script('qasper-woocommerce-cart-bridge', 'QasperWooCommerceBridge', [
            'iframeOrigin' => $this->settings->qasper_origin(),
            'restUrl' => esc_url_raw(rest_url('qasper-woocommerce/v1/cart/add')),
            'nonce' => wp_create_nonce('wp_rest'),
            'cartBridgeNonce' => $this->settings->cart_bridge_nonce(),
        ]);
    }

    public function print_widget_bootstrap(): void
    {
        if (!$this->settings->is_enabled() || !$this->settings->has_required_widget_settings()) {
            return;
        }

        echo $this->snippet_builder->build_bootstrap_script();
    }
}
