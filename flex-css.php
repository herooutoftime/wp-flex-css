<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://www.herooutoftime.com
 * @since             1.0.0
 * @package           Flex_Css
 *
 * @wordpress-plugin
 * Plugin Name:       Flex CSS
 * Plugin URI:        https://github.com/herooutoftime/wp-flex-css
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Andreas Bilz
 * Author URI:        http://www.herooutoftime.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       flex-css
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-flex-css-activator.php
 */
function activate_flex_css() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-flex-css-activator.php';
	Flex_Css_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-flex-css-deactivator.php
 */
function deactivate_flex_css() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-flex-css-deactivator.php';
	Flex_Css_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_flex_css' );
register_deactivation_hook( __FILE__, 'deactivate_flex_css' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-flex-css.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_flex_css() {

	$plugin = new Flex_Css();
	$plugin->run();

}
run_flex_css();
