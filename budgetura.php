<?php
/**
 * Plugin Name: Budgetura
 * Plugin URI: https://yoursite.com/budgetura
 * Description: Comprehensive debt management and financial tracking plugin for WordPress. Track credit cards, loans, mortgages, bills, and goals over multiple years.
 * Version: 1.0.0
 * Author: Oxford Pierpont
 * Author URI: https://yoursite.com
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: budgetura
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Elementor tested up to: 3.20
 * Elementor Pro tested up to: 3.20
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Plugin version
define( 'BUDGETURA_VERSION', '1.0.0' );
define( 'BUDGETURA_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BUDGETURA_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BUDGETURA_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_budgetura() {
    require_once BUDGETURA_PLUGIN_DIR . 'includes/class-budgetura-activator.php';
    Budgetura_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_budgetura() {
    require_once BUDGETURA_PLUGIN_DIR . 'includes/class-budgetura-deactivator.php';
    Budgetura_Deactivator::deactivate();
}

// Activation and deactivation hooks
register_activation_hook( __FILE__, 'activate_budgetura' );
register_deactivation_hook( __FILE__, 'deactivate_budgetura' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require_once BUDGETURA_PLUGIN_DIR . 'includes/class-budgetura.php';

/**
 * Begins execution of the plugin.
 */
function run_budgetura() {
    $plugin = new Budgetura();
    $plugin->run();
}
run_budgetura();
