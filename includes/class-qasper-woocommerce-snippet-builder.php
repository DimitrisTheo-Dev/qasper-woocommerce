<?php

if (!defined('ABSPATH')) {
    exit;
}

final class Qasper_WooCommerce_Snippet_Builder
{
    private Qasper_WooCommerce_Settings $settings;
    private Qasper_WooCommerce_Page_Context $page_context;

    public function __construct(
        Qasper_WooCommerce_Settings $settings,
        Qasper_WooCommerce_Page_Context $page_context
    ) {
        $this->settings = $settings;
        $this->page_context = $page_context;
    }

    public function build_bootstrap_script(): string
    {
        $config = [
            'slug' => $this->settings->business_slug(),
            'mode' => 'floating',
            'position' => 'right',
            'channel' => 'woocommerce',
            'storeConnectionId' => $this->settings->store_connection_id(),
            'cartBridgeNonce' => $this->settings->cart_bridge_nonce(),
            'pageContext' => $this->page_context->build(),
        ];

        $json = wp_json_encode($config, JSON_UNESCAPED_SLASHES);
        if (!is_string($json)) {
            return '';
        }

        return '<script>window.QasperWidget=window.QasperWidget||{q:[],init:function(c){this.q.push(c)}};window.QasperWidget.init(' . $json . ');</script>';
    }
}
