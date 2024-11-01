<?php
/*
  Plugin initialization file

  @link              https://tinerin.de
  @since             1.0.0
  @package           Wp_virtualtour

  @wordpress-plugin
  Plugin Name:       WP Virtual Tour
  Description:       Create virtual 360° tours.
Version:           1.0.12
  Author:            Martin Blaas
  License:           GPL-2.0+
  License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
  Text Domain:       wp_virtualtour
  Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
  die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('WPVR_TOUR_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp_virtualtour-activator.php
 */
function activate_wp_virtualtour()
{
  require_once plugin_dir_path(__FILE__) . 'includes/class-wp_virtualtour-activator.php';
  Wp_virtualtour_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp_virtualtour-deactivator.php
 */
function deactivate_wp_virtualtour()
{
  require_once plugin_dir_path(__FILE__) . 'includes/class-wp_virtualtour-deactivator.php';
  Wp_virtualtour_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_wp_virtualtour');
register_deactivation_hook(__FILE__, 'deactivate_wp_virtualtour');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-wp_virtualtour.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wp_virtualtour()
{

  $plugin = new Wp_virtualtour();
  $plugin->run();

}

run_wp_virtualtour();
