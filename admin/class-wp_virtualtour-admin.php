<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @since      1.0.0
 *
 * @package    Wp_virtualtour
 * @subpackage Wp_virtualtour/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wp_virtualtour
 * @subpackage Wp_virtualtour/admin
 * @author     Martin Blaas <info@form-fabrik.de>
 */
class Wp_virtualtour_Admin
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
   * @param string $plugin_name The name of this plugin.
   * @param string $version The version of this plugin.
   *
   * @since    1.0.0
   */
  public function __construct($plugin_name, $version)
  {
    $this->plugin_name = $plugin_name;
    $this->version = $version;

    require 'class-wp_virtualtour-ajax-actions.php';
    new Wp_virtualtour_Ajax_Actions();
  }

  public function load_gutenberg_block()
  {
    $tours = get_option('wp_virtualtours_tours');
    $completeTourData = array();

    if (!$tours) {
      return;
    }

    foreach ($tours as $tour) {
      if (is_object($tour)) {
        $completeTourData[] = get_option('wp_virtualtours_tour_' . $tour->id);
      }
    }

    wp_enqueue_style($this->plugin_name . '-pannellum', plugin_dir_url(__DIR__) . 'public/css/pannellum@2.5.6.css', array(), $this->version, 'all');
    wp_enqueue_script($this->plugin_name . '-pannellum', plugin_dir_url(__DIR__) . 'public/js/pannellum@2.5.6.js', array(), $this->version, true);

    wp_enqueue_script(
      'wp_virtualtour_block',
      plugin_dir_url(__FILE__) . 'block/build/index.js',
      array('wp-blocks', 'wp-editor', 'wp-i18n'),
      rand(0, 9999),
      true
    );
    wp_localize_script('wp_virtualtour_block', 'wp_virtualtour',
      array(
        'tours' => $completeTourData
      )
    );
    register_block_type('wp-virtualtour/wp-virtualtour-block', array(
      'apiVersion' => 2,
      'editor_script' => 'wp_virtualtour_block',
    ));
  }

  public function wp_virtualtour_block_set_script_translations()
  {
    wp_set_script_translations('wp_virtualtour_block', 'wp_virtualtour', plugin_dir_path(dirname(__FILE__)) . 'languages');
  }

  public function add_admin_menu()
  {
    add_menu_page(
      'WP Virtual Tour',
      'WP Virtual Tour',
      'manage_options',
      'wp_virtualtour/wp_virtualtour-admin.php',
      array($this, 'wpse_91693_render'),
      'dashicons-images-alt',
      99
    );
  }

  public function wpse_91693_render()
  {
    print '<div id="wpvtTour" class="wrap">';
    print '<h1>' . __('WP Virtual Tour') . '</h1>';
    print '<div id="wpvtApp"></div>';
    print '</div>';
  }

  /**
   * Register the stylesheets for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_styles()
  {
    global $current_screen;

    if ($current_screen->id === 'toplevel_page_wp_virtualtour/wp_virtualtour-admin') {
      wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'vue-app/dist/style.css', array(), $this->version, 'all');
    }
    wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'assets/block-editor.css', array(), $this->version, 'all');
  }

  /**
   * Register the JavaScript for the admin area.
   *
   * @since    1.0.0
   */
  public function enqueue_scripts()
  {
    global $current_screen;

    if ($current_screen->id === 'toplevel_page_wp_virtualtour/wp_virtualtour-admin') {
      wp_enqueue_media();
      wp_enqueue_script('wp_virtualtour', plugin_dir_url(__FILE__) . 'vue-app/dist/main.js', array('wp-i18n'), $this->version, true);
      wp_set_script_translations('wp_virtualtour', 'wp_virtualtour', plugin_dir_path(dirname(__FILE__)) . 'languages');

      wp_localize_script('wp_virtualtour', 'wp_virtualtour', array(
        'pluginsUrl' => plugins_url(),
      ));

    }
  }
}
