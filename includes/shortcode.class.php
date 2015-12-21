<?php

/**
 * Used on public display of the Trips
 *
 * @link       http://purecharity.com
 * @since      1.0.0
 *
 * @package    Purecharity_Wp_Trips
 * @subpackage Purecharity_Wp_Trips/includes
 */

/**
 * Used on public display of the Trips.
 *
 * This class defines all the shortcodes necessary.
 *
 * @since      1.0.0
 * @package    Purecharity_Wp_Trips
 * @subpackage Purecharity_Wp_Trips/includes
 * @author     Pure Charity <dev@purecharity.com>
 */
class Purecharity_Wp_Trips_Shortcode {


  /**
   * The Base Plugin.
   *
   * @since    1.0.0
   * @access   public
   * @var      Object    $base_plugin    The Base Plugin.
   */
  public static $base_plugin;

  /**
   * Initialize the class and Base Plugin functionality.
   *
   * @since    1.0.0
   */
  public function __construct() {
    $this->actions = array();
    $this->filters = array();
  }

  /**
   * Initialize the shortcodes to make them available on page runtime.
   *
   * @since    1.0.0
   */
  public static function init()
  {

    if(Purecharity_Wp_Trips::base_present()){
      add_shortcode('trips', array('Purecharity_Wp_Trips_Shortcode', 'trips_shortcode'));
      add_shortcode('trip', array('Purecharity_Wp_Trips_Shortcode', 'trip_shortcode'));

      self::$base_plugin = new Purecharity_Wp_Base();
    }
  }

  /**
   * Initialize the Trips Listing shortcode.
   *
   * TODO: Document possible options.
   *
   * @since    1.0.0
   */
  public static function trips_shortcode($atts)
  {
    $opts = shortcode_atts( array(
      'limit'     => 10,
      'country'   => get_query_var('country'),
      'region'    => get_query_var('region'),
      'cause'     => get_query_var('cause'),
      'starts_at' => get_query_var('starts_at'),
      'ends_at'   => get_query_var('ends_at'),
      'past'      => get_query_var('past'),
      'upcoming'  => get_query_var('upcoming'),
      'grid'      => get_query_var('grid'),
      'tag'       => get_query_var('tag'),
      'sort'      => get_query_var('sort'),
      'dir'       => get_query_var('dir'),
      'trip_tag'  => (isset($_GET['trip_tag']) ? $_GET['trip_tag'] : get_query_var('trip_tag')),
      'query'     => (isset($_GET['query']) ? $_GET['query'] : get_query_var('query')),
      'page'      => (isset($_GET['_page']) ? $_GET['_page'] : get_query_var('_page'))
    ), $atts );

    $opts['ends_at'] = self::is_past($opts['past']);

    if($opts['upcoming'] != 'false'){
      $opts['upcoming'] = 'true';
    }

    if(isset($_GET['trip'])){
      $options = array();
      $options['trip'] = $_GET['trip'];
      return self::trip_shortcode($options);
    }else{
      $events = self::$base_plugin->api_call('events/?' . http_build_query(self::filtered_opts($opts)));

      if ($events && count($events->events) > 0) {
        Purecharity_Wp_Trips_Public::$events = $events;
        if($opts['grid']){
          return Purecharity_Wp_Trips_Public::listing_grid();
        }else{
          return Purecharity_Wp_Trips_Public::listing();
        }
      }else{
        return Purecharity_Wp_Trips_Public::list_not_found();
      };
    }

  }

  /**
   * Adds parameter to show only past trips.
   *
   * @since    1.0.5
   */
  public static function is_past($past){
    if($past == "true"){
      return date('d/m/Y');
    }else{
      return "";
    }

  }

  /**
   * Initialize the Trips Single View shortcode.
   *
   * @since    1.0.0
   */
  public static function trip_shortcode($atts){
    $options = shortcode_atts( array(
      'trip' => false
    ), $atts );

    if ($options['trip']) {
      $event = self::$base_plugin->api_call('events/'. $options['trip']);
      if ($event) {
        $event = $event->event;
        Purecharity_Wp_Trips_Public::$event = $event;
        return Purecharity_Wp_Trips_Public::show();
      }else{
        return Purecharity_Wp_Trips_Public::not_found();
      }

    }

  }

  /**
   * Internal: Filters out empy values from an associative array.
   *
   * Returns an Array.
   *
   * @since    1.0.0
   */
  public static function filtered_opts($opts = array()) {

    $filtered_opts = array();
    foreach($opts as $k => $v) {
      if($v != null && $v != '') {
        $filtered_opts[$k] = $v;
      }
    }
    return $filtered_opts;
  }

}
