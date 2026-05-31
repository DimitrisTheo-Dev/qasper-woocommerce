<?php

if (!defined('ABSPATH')) {
    exit;
}

final class Qasper_WooCommerce_Settings
{
    private const OPTION_ENABLED = 'qasper_woocommerce_enabled';
    private const OPTION_SLUG = 'qasper_woocommerce_slug';
    private const OPTION_STORE_CONNECTION_ID = 'qasper_woocommerce_store_connection_id';
    private const OPTION_QASPER_BASE_URL = 'qasper_woocommerce_qasper_base_url';

    public function register(): void
    {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_settings_page(): void
    {
        add_submenu_page(
            'woocommerce',
            __('Qasper', 'qasper-woocommerce'),
            __('Qasper', 'qasper-woocommerce'),
            'manage_woocommerce',
            'qasper-woocommerce',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings(): void
    {
        register_setting('qasper_woocommerce', self::OPTION_ENABLED, [
            'type' => 'boolean',
            'sanitize_callback' => static fn($value): bool => (bool) $value,
            'default' => false,
        ]);
        register_setting('qasper_woocommerce', self::OPTION_SLUG, [
            'type' => 'string',
            'sanitize_callback' => [$this, 'sanitize_slug'],
            'default' => '',
        ]);
        register_setting('qasper_woocommerce', self::OPTION_STORE_CONNECTION_ID, [
            'type' => 'string',
            'sanitize_callback' => [$this, 'sanitize_uuid'],
            'default' => '',
        ]);
        register_setting('qasper_woocommerce', self::OPTION_QASPER_BASE_URL, [
            'type' => 'string',
            'sanitize_callback' => [$this, 'sanitize_base_url'],
            'default' => 'https://qasper.ai',
        ]);
    }

    public function render_settings_page(): void
    {
        if (!current_user_can('manage_woocommerce')) {
            wp_die(esc_html__('You do not have permission to manage Qasper settings.', 'qasper-woocommerce'));
        }

        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('Qasper WooCommerce', 'qasper-woocommerce'); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('qasper_woocommerce'); ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><?php echo esc_html__('Enable assistant', 'qasper-woocommerce'); ?></th>
                        <td><label><input type="checkbox" name="<?php echo esc_attr(self::OPTION_ENABLED); ?>" value="1" <?php checked($this->is_enabled()); ?>> <?php echo esc_html__('Show Qasper on this store', 'qasper-woocommerce'); ?></label></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="qasper_woocommerce_slug"><?php echo esc_html__('Business slug', 'qasper-woocommerce'); ?></label></th>
                        <td><input id="qasper_woocommerce_slug" class="regular-text" name="<?php echo esc_attr(self::OPTION_SLUG); ?>" value="<?php echo esc_attr($this->business_slug()); ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="qasper_woocommerce_store_connection_id"><?php echo esc_html__('Store connection ID', 'qasper-woocommerce'); ?></label></th>
                        <td><input id="qasper_woocommerce_store_connection_id" class="regular-text" name="<?php echo esc_attr(self::OPTION_STORE_CONNECTION_ID); ?>" value="<?php echo esc_attr($this->store_connection_id()); ?>"></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="qasper_woocommerce_qasper_base_url"><?php echo esc_html__('Qasper base URL', 'qasper-woocommerce'); ?></label></th>
                        <td><input id="qasper_woocommerce_qasper_base_url" class="regular-text" name="<?php echo esc_attr(self::OPTION_QASPER_BASE_URL); ?>" value="<?php echo esc_attr($this->qasper_base_url()); ?>"></td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    public function is_enabled(): bool
    {
        return (bool) get_option(self::OPTION_ENABLED, false);
    }

    public function has_required_widget_settings(): bool
    {
        return $this->business_slug() !== '' && $this->store_connection_id() !== '';
    }

    public function business_slug(): string
    {
        return (string) get_option(self::OPTION_SLUG, '');
    }

    public function store_connection_id(): string
    {
        return (string) get_option(self::OPTION_STORE_CONNECTION_ID, '');
    }

    public function qasper_base_url(): string
    {
        return rtrim((string) get_option(self::OPTION_QASPER_BASE_URL, 'https://qasper.ai'), '/');
    }

    public function qasper_origin(): string
    {
        $parts = wp_parse_url($this->qasper_base_url());
        if (!is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            return 'https://qasper.ai';
        }

        return $parts['scheme'] . '://' . $parts['host'];
    }

    public function widget_script_url(): string
    {
        return esc_url_raw($this->qasper_base_url() . '/embed/qasper-widget.js');
    }

    public function cart_introspection_url(): string
    {
        return esc_url_raw($this->qasper_base_url() . '/api/integrations/woocommerce/cart-intents/introspect');
    }

    public function cart_bridge_nonce(): string
    {
        return wp_create_nonce('qasper_woocommerce_cart_bridge_' . $this->store_connection_id());
    }

    public function sanitize_slug(string $value): string
    {
        $value = sanitize_text_field($value);
        return preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value) === 1 ? $value : '';
    }

    public function sanitize_uuid(string $value): string
    {
        $value = strtolower(sanitize_text_field($value));
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $value) === 1 ? $value : '';
    }

    public function sanitize_base_url(string $value): string
    {
        $value = esc_url_raw(rtrim($value, '/'));
        $parts = wp_parse_url($value);
        if (!is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            return 'https://qasper.ai';
        }

        return in_array($parts['scheme'], ['https', 'http'], true) ? $value : 'https://qasper.ai';
    }
}
