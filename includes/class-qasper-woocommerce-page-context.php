<?php

if (!defined('ABSPATH')) {
    exit;
}

final class Qasper_WooCommerce_Page_Context
{
    public function build(): array
    {
        $context = [
            'pageType' => $this->page_type(),
            'locale' => substr(get_locale(), 0, 2),
        ];

        if (is_product()) {
            $product = wc_get_product(get_the_ID());
            if ($product instanceof WC_Product) {
                $context['productId'] = (string) $product->get_id();
                $context['productSlug'] = (string) get_post_field('post_name', $product->get_id());
                $context['url'] = $this->strip_query_and_fragment(get_permalink($product->get_id()));
            }
        }

        return array_filter($context, static fn($value): bool => is_string($value) && trim($value) !== '');
    }

    private function page_type(): string
    {
        if (is_product()) {
            return 'product';
        }
        if (is_cart()) {
            return 'cart';
        }
        if (is_checkout()) {
            return 'checkout';
        }
        if (is_shop() || is_product_category() || is_product_tag()) {
            return 'catalog';
        }

        return 'page';
    }

    private function strip_query_and_fragment(string $url): string
    {
        $parts = wp_parse_url($url);
        if (!is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            return '';
        }

        $path = $parts['path'] ?? '/';
        return $parts['scheme'] . '://' . $parts['host'] . $path;
    }
}
