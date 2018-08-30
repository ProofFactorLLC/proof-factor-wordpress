<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://prooffactor.com
 * @since             1.0.0
 * @package           Proof_Factor
 *
 * @wordpress-plugin
 * Plugin Name:       Proof Factor - Social Proof Notifications for WooCommerce
 * Description:       Proof Factor displays recent orders and purchases on your WooCommerce storefront!
 * Version:           1.0.0
 * Author:            Proof Factor LLC
 * Author URI:        https://prooffactor.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       proof-factor
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {

    /**
     * Currently plugin version.
     * Start at version 1.0.0 and use SemVer - https://semver.org
     * Rename this for your plugin and update it as you release new versions.
     */
    define('PROOF_FACTOR_PLUGIN_VERSION', '1.0.0');

    /**
     * The code that runs during plugin activation.
     * This action is documented in includes/class-proof-factor-activator.php
     */
    function activate_proof_factor()
    {
        require_once plugin_dir_path(__FILE__) . 'includes/class-proof-factor-activator.php';
        Proof_Factor_Activator::activate();
    }

    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-proof-factor-deactivator.php
     */
    function deactivate_proof_factor()
    {
        require_once plugin_dir_path(__FILE__) . 'includes/class-proof-factor-deactivator.php';
        Proof_Factor_Deactivator::deactivate();
    }

    register_activation_hook(__FILE__, 'activate_proof_factor');
    register_deactivation_hook(__FILE__, 'deactivate_proof_factor');

    /**
     * The core plugin class that is used to define internationalization,
     * admin-specific hooks, and public-facing site hooks.
     */
    require plugin_dir_path(__FILE__) . 'includes/class-proof-factor.php';

    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.0
     */
    function run_proof_factor()
    {

        $plugin = new Proof_Factor();
        $plugin->run();

    }

    run_proof_factor();
}
