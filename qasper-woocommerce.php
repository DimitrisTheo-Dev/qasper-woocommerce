<?php
/**
 * Plugin Name:       Qasper WooCommerce
 * Plugin URI:        https://qasper.ai/woocommerce
 * Description:       Embed the Qasper storefront assistant in WooCommerce and securely bridge cart actions.
 * Version:           0.1.2
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Qasper
 * Author URI:        https://qasper.ai
 * License:           GPL v3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Requires Plugins:  woocommerce
 * Text Domain:       qasper-woocommerce
 *
 * @package Qasper_WooCommerce
 */

if (!defined('ABSPATH')) {
    exit;
}

define('QASPER_WOOCOMMERCE_VERSION', '0.1.2');
define('QASPER_WOOCOMMERCE_FILE', __FILE__);
define('QASPER_WOOCOMMERCE_DIR', plugin_dir_path(__FILE__));
define('QASPER_WOOCOMMERCE_URL', plugin_dir_url(__FILE__));

require_once QASPER_WOOCOMMERCE_DIR . 'includes/class-qasper-woocommerce-settings.php';
require_once QASPER_WOOCOMMERCE_DIR . 'includes/class-qasper-woocommerce-page-context.php';
require_once QASPER_WOOCOMMERCE_DIR . 'includes/class-qasper-woocommerce-snippet-builder.php';
require_once QASPER_WOOCOMMERCE_DIR . 'includes/class-qasper-woocommerce-rest-controller.php';
require_once QASPER_WOOCOMMERCE_DIR . 'includes/class-qasper-woocommerce-plugin.php';

add_action('plugins_loaded', static function (): void {
    if (!class_exists('WooCommerce')) {
        return;
    }

    $plugin = new Qasper_WooCommerce_Plugin();
    $plugin->register();
});
