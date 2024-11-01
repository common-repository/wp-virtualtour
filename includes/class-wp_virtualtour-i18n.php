<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       aaa
 * @since      1.0.0
 *
 * @package    Wp_virtualtour
 * @subpackage Wp_virtualtour/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wp_virtualtour
 * @subpackage Wp_virtualtour/includes
 * @author     Martin Blaas <info@form-fabrik.de>
 */
class Wp_virtualtour_i18n
{
  /**
   * Load the plugin text domain for translation.
   *
   * @since    1.0.0
   */
  public function load_plugin_textdomain()
  {
    load_plugin_textdomain(
      'wp_virtualtour',
      false,
      dirname(dirname(plugin_basename(__FILE__))) . '/languages/'
    );
  }
}
