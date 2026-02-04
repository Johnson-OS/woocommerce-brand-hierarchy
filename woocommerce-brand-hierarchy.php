<?php

/**
 * Plugin Name: WooCommerce Brand Hierarchy
 * Plugin URI:  https://pixadv.com/wc-brand-hierarchy
 * Description: Brand → Category → Subcategory navigation for WooCommerce
 * Version:     1.0.0
 * Author:      PixADV
 * Author URI:  https://pixadv.com
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wcbh
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.1
 */

defined('ABSPATH') || exit;
define('WCBH_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('WCBH_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/Helpers/brand-banner.php';



use WCBH\Plugin;

register_activation_hook(__FILE__, [Plugin::class, 'activate']);
register_deactivation_hook(__FILE__, [Plugin::class, 'deactivate']);

Plugin::init();
