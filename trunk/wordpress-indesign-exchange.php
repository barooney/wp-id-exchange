<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://barooney.de/meine-projekte/wordpress-indesign-exchange/
 * @since             1.0.0
 * @package           Wordpress_Indesign_Exchange
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress InDesign Exchange
 * Plugin URI:        http://barooney.de/meine-projekte/wordpress-indesign-exchange/
 * Description:       A plugin that will help users to get their WordPress articles exported and easily re-imported into InDesign.
 * Version:           1.0.1
 * Author:            Baron IT Consulting
 * Author URI:        http://barooney.de/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wordpress-indesign-exchange
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/wordpress-indesign-exchange-activator.php
 */
function activate_Wordpress_Indesign_Exchange() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/wordpress-indesign-exchange-activator.php';
	Wordpress_Indesign_Exchange_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/wordpress-indesign-exchange-deactivator.php
 */
function deactivate_Wordpress_Indesign_Exchange() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/wordpress-indesign-exchange-deactivator.php';
	Wordpress_Indesign_Exchange_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_Wordpress_Indesign_Exchange' );
register_deactivation_hook( __FILE__, 'deactivate_Wordpress_Indesign_Exchange' );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/wordpress-indesign-exchange.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_Wordpress_Indesign_Exchange() {

	$plugin = new Wordpress_Indesign_Exchange();
	$plugin->run();

}
run_Wordpress_Indesign_Exchange();
