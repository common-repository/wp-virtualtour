<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       aaa
 * @since      1.0.0
 *
 * @package    Wp_virtualtour
 * @subpackage Wp_virtualtour/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Wp_virtualtour
 * @subpackage Wp_virtualtour/public
 * @author     Martin Blaas <info@form-fabrik.de>
 */
class Wp_virtualtour_Public
{

  /**
   * The ID of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string $plugin_name The ID of this plugin.
   */
  private $plugin_name;

  /**
   * The version of this plugin.
   *
   * @since    1.0.0
   * @access   private
   * @var      string $version The current version of this plugin.
   */
  private $version;

  /**
   * Initialize the class and set its properties.
   *
   * @param string $plugin_name The name of the plugin.
   * @param string $version The version of this plugin.
   *
   * @since    1.0.0
   */
  public function __construct(string $plugin_name, string $version)
  {
    $this->plugin_name = $plugin_name;
    $this->version = $version;
  }

  /**
   * Register the stylesheets for the public-facing side of the site.
   *
   * @since    1.0.0
   */
  public function enqueue_styles()
  {
    wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/wp_virtualtour-public.css', array(), $this->version, 'all');
    wp_enqueue_style($this->plugin_name . '-pannellum', plugin_dir_url(__FILE__) . 'css/pannellum@2.5.6.css', array(), $this->version, 'all');
  }

  /**
   * Register the JavaScript for the public-facing side of the site.
   *
   * @since    1.0.0
   */
  public function enqueue_scripts()
  {
    wp_register_script($this->plugin_name . '_script', plugin_dir_url(__FILE__) . 'js/wp_virtualtour-public.js', array(), rand(0, 99999), true);
    wp_register_script($this->plugin_name . '_pannellum', plugin_dir_url(__FILE__) . 'js/pannellum@2.5.6.js', array('jquery'), rand(0, 99999), true);
  }

  /**
   * Register the JavaScript for the public-facing side of the site.
   *
   * @since    1.0.0
   */
  public function enqueue_block_assets()
  {

    $allTourData = array();
    $tours = get_option('wp_virtualtours_tours');

    if (!count($tours)) {
      return;
    }

    wp_enqueue_script($this->plugin_name . '_pannellum', plugin_dir_url(__FILE__) . 'js/pannellum@2.5.6.js', array('jquery'), rand(0, 99999), true);
    wp_enqueue_script($this->plugin_name . '_script', plugin_dir_url(__FILE__) . 'js/wp_virtualtour-public.js', array(), rand(0, 99999), true);

    foreach ($tours as $tour) {
      if (is_object($tour)) {
        $completeTourData[] = get_option('wp_virtualtours_tour_' . $tour->id);
      }
    }

    wp_localize_script($this->plugin_name . '_script', 'wp_virtualtour_block', array(
      'data' => $allTourData
    ));
  }

  /**
   * Add shortcode
   *
   * @param $attrs : Shortcode attributes
   * @return string
   * @since    1.0.0
   */
  public function add_shortcode($attrs): string
  {
    static $count = 0;

    if (!$attrs['id']) {
      return "Please set a ID attribute";
    }

    $tour = get_option('wp_virtualtours_tour_' . $attrs['id']);

    if (!$tour) {
      return "Tour not found :-(";
    }

    if (!$tour->default->firstScene) {
      return "No first Scene defined";
    }

    wp_enqueue_script($this->plugin_name . '_pannellum');
    wp_enqueue_script($this->plugin_name . '_script');
    wp_localize_script($this->plugin_name . '_script', 'wp_virtualtour_' . $count, array(
      'data' => $tour
    ));

    $count++;
    return "<div id='panorama_{$tour->id}' style='height: 400px;'></div>";
  }

}
