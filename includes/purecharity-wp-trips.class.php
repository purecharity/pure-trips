<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       http://purecharity.com
 * @since      1.0.0
 *
 * @package    Purecharity_Wp_Trips
 * @subpackage Purecharity_Wp_Trips/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Purecharity_Wp_Trips
 * @subpackage Purecharity_Wp_Trips/includes
 * @author     Pure Charity <dev@purecharity.com>
 */
class Purecharity_Wp_Trips {


	/**
	 * Formats used on the widgets.
	 *
	 * @since    1.0.0
	 */
	const DATE_FORMAT = "F j, Y";
  	const MONTH_FORMAT = "F Y";

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Purecharity_Wp_Trips_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'purecharity-wp-trips';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		// Initialize the shortcodes
		Purecharity_Wp_Trips_Shortcode::init();

		// Don't run anything else in the plugin, if the base plugin is not available or 
		// not active
    if ( ! self::base_present() ) { return; }

	}

	/**
	 * Check for the Base plugin presence and activation.
	 *
	 * Will check for the presence and status of the base plugin.
	 *
	 * @since    1.0.0
	 */
  static function base_present() {
		return in_array( 'purecharity-wp-base/purecharity-wp-base.php', (array) get_option( 'active_plugins', array() ) );
  }

	/**
	 * Disable plugin if base plugin is not present or not active.
	 *
	 * The primary sanity check, automatically disable the plugin on activation if it doesn't
   * meet minimum requirements.
	 *
	 * @since    1.0.0
	 */
  static function activation_check() {
    if ( ! self::base_present() ) {
      deactivate_plugins( plugin_basename( plugin_basename( __FILE__ ) ) );
      wp_die(
      	__( 
	      	'Pure Charity Trips requires Pure Charity Base to be installed and active!', 
	      	'purecharity-wp-trips' 
	      ) 
	    );
    }
  }

	/**
	 * Disable plugin if base plugin is not present or not active.
	 *
	 * The backup sanity check, in case the plugin is activated in a weird way,
   * or the versions change after activation.
	 *
	 * @since    1.0.0
	 */
  function check_version() {
    if ( ! self::base_present() ) {
      if ( is_plugin_active( plugin_basename( __FILE__ ) ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        add_action( 'admin_notices', array( $this, 'disabled_notice' ) );
        if ( isset( $_GET['activate'] ) ) {
          unset( $_GET['activate'] );
        }
      }
    }
  }

	/**
	 * Country list for use on widget.
	 *
	 * @since    1.0.0
	 */
  public static function widget_country_list(){
  	$base_plugin = new Purecharity_Wp_Base();
    $countries = $base_plugin->api_call('countries');
    if($countries != null) {
      sort($countries->countries);
      $current_country = get_query_var('country');
      $content = '<ul class="purecharity-trips-countries-widget">';
      foreach($countries->countries as $country) {
      	if($country == ''){ continue; }
        $content 	.= "<li><a href=\"?country=".urlencode($country)."\""
                  . ($country == $current_country ? ' class="selected"' : '')
                  . ">".$country."</a></li>";
      }
      $content .= '</ul>';
      return $content;
    }
  }
 
	/**
	 * Region list for use on widget.
	 *
	 * @since    1.0.0
	 */
  public static function widget_region_list(){
  	$base_plugin = new Purecharity_Wp_Base();
    $regions = $base_plugin->api_call('regions');
    if($regions != null) {
      sort($regions->regions);
      $current_region = get_query_var('region');
      $content = '<ul class="purecharity-trips-regions-widget">';
      foreach($regions->regions as $region) {
      	if($region == ''){ continue; }
        $content 	.= "<li><a href=\"?region=".$region->id."\""
                  . ($region->id == $current_region ? ' class="selected"' : '')
                  . ">".$region->name."</a></li>";
      }
      $content .= '</ul>';
      return $content;
    }
  }

	/**
	 * Months list for use on widget.
	 *
	 * @since    1.0.0
	 */
  public static function widget_month_list() {
  	$base_plugin = new Purecharity_Wp_Base();
    $all_dates = $base_plugin->api_call('events?scope=dates');
    $months = array();
    foreach($all_dates->events as $dates) {
      $start    = new DateTime($dates->starts_at);
      $start->modify('first day of this month');
      $end      = new DateTime($dates->ends_at);
      $end->modify('first day of next month');
      $interval = DateInterval::createFromDateString('1 month');
      $period   = new DatePeriod($start, $interval, $end);
      foreach ($period as $dt) {
        $key = $dt->format('Y-m');
        if(!array_key_exists($key, $months)) {
          $months[$key] = $dt->format(self::MONTH_FORMAT);
        }
      }
    }
    ksort($months);
    $current_month = get_query_var('date');
    $content = '<ul class="purecharity-trips-months-widget">';
    foreach($months as $date => $label) {
      $content .=  "<li><a href=\"?date=".urlencode($date)."\""
                . ($date == $current_month ? ' class="selected"' : '')
                . ">".$label."</a></li>";
    }
    $content .= '</ul>';
    return $content;
  }

	/**
	 * Months list for use on widget.
	 *
	 * @since    1.0.0
	 */
  public static function widget_tag_list() {
  	$base_plugin = new Purecharity_Wp_Base();
    $all_tags = $base_plugin->api_call('events/tags');

    $base_url = get_site_url();

    $tags = $all_tags->tags;
    asort($tags);
    $content = '<ul class="purecharity-trips-tags-widget">';
    foreach($tags as $event_tag) {
      $content .= '<li><a href="'.$base_url.'/missions/upcoming-missions?trip_tag='.$event_tag->name.'"'
                  . ((isset($_GET['trip_tag']) && $_GET['trip_tag'] == $event_tag->name) ? ' class="selected"' : '')
                  . ">".$event_tag->name."</a></li>";
    }
    $content .= '</ul>';
    return $content;
  }


	/**
	 * The disable notice in case the base plugin is not present or not active.
	 *
	 * The disable notice in case the base plugin is not present or not active.
	 *
	 * @since    1.0.0
	 */
	function disabled_notice() {
    echo '<strong>' . esc_html__( 'Pure Charity Trips requires Pure Charity Base plugin to be installed and active.', 'purecharity-wp-base' ) . '</strong>';
  }


	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Purecharity_Wp_Trips_Loader. Orchestrates the hooks of the plugin.
	 * - Purecharity_Wp_Trips_i18n. Defines internationalization functionality.
	 * - Purecharity_Wp_Trips_Admin. Defines all hooks for the dashboard.
	 * - Purecharity_Wp_Trips_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/loader.class.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/i18n.class.php';

		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/admin.class.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/public.class.php';

		$this->loader = new Purecharity_Wp_Trips_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Purecharity_Wp_Trips_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Purecharity_Wp_Trips_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Purecharity_Wp_Trips_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'settings_init' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Purecharity_Wp_Trips_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		$options = get_option( 'purecharity_trips_settings' );

		if(isset($_GET['event_id']) && isset($options['single_view_template']) && $options['single_view_template'] != ''){
			add_action('template_redirect', 'gt_force_template');
		}

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Purecharity_Wp_Trips_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
