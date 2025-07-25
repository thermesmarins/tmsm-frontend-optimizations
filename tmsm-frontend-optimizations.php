<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/aflamentTM
 * @since             1.0.0
 * @package           Tmsm_Frontend_Optimizations
 *
 * @wordpress-plugin
 * Plugin Name:       TMSM Frontend Optimizations
 * Plugin URI:        https://github.com/thermesmarins/tmsm-frontend-optimizations
 * Description:       Frontend Optimizations for Thermes Marins de Saint-Malo
 * Version:           1.8.2
 * Author:            Arnaud Flament
 * Author URI:        https://github.com/aflamentTM
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       tmsm-frontend-optimizations
 * Domain Path:       /languages
 * Github Plugin URI: https://github.com/thermesmarins/tmsm-frontend-optimizations
 * Github Branch:     master
 * Requires PHP:      8.0
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('TMSM_FRONTEND_OPTIMIZATIONS_VERSION', '1.8.2');
defined('TMSM_FRONTEND_OPTIMIZATIONS_BASE_URL') || define('TMSM_FRONTEND_OPTIMIZATIONS_BASE_URL', plugin_dir_url(__FILE__));

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tmsm-frontend-optimizations-activator.php
 */
function activate_tmsm_frontend_optimizations()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-tmsm-frontend-optimizations-activator.php';
	Tmsm_Frontend_Optimizations_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tmsm-frontend-optimizations-deactivator.php
 */
function deactivate_tmsm_frontend_optimizations()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-tmsm-frontend-optimizations-deactivator.php';
	Tmsm_Frontend_Optimizations_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_tmsm_frontend_optimizations');
register_deactivation_hook(__FILE__, 'deactivate_tmsm_frontend_optimizations');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-tmsm-frontend-optimizations.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tmsm_frontend_optimizations()
{

	$plugin = new Tmsm_Frontend_Optimizations();
	$plugin->run();
}
run_tmsm_frontend_optimizations();
