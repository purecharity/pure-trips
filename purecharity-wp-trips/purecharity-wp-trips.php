<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://purecharity.com
 * @since             1.0.0
 * @package           Purecharity_Wp_Trips
 *
 * @wordpress-plugin
 * Plugin Name:       Pure Charity Trips
 * Plugin URI:        http://purecharity.com/purecharity-wp-trips-uri/
 * Description:       A shortcode to list Trips from the Pure Charity App with links to sign up for it
 * Version:           1.0.5
 * Author:            Rafael Dalprá / Pure Charity
 * Author URI:        http://purecharity.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       purecharity-wp-trips
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The Shortcodes handler class.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/shortcode.class.php';

/**
 * The template tags for trips.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/template_tags.php';

/**
 * The Widgets.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/widget_region.class.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/widget_country.class.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/widget_months.class.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/widget_tags.class.php';

/**
 * The code that runs during plugin activation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/activator.class.php';

/**
 * The paginator.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/paginator.class.php';

/**
 * The code that runs during plugin deactivation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/deactivator.class.php';

/** This action is documented in includes/purecharity-wp-trips-activator.class.php */
register_activation_hook( __FILE__, array( 'Purecharity_Wp_Trips_Activator', 'activate' ) );

/** This action is documented in includes/purecharity-wp-trips-deactivator.class.php */
register_deactivation_hook( __FILE__, array( 'Purecharity_Wp_Trips_Deactivator', 'deactivate' ) );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/purecharity-wp-trips.class.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_purecharity_wp_trips() {

	$plugin = new Purecharity_Wp_Trips();
	$plugin->run();

}
run_purecharity_wp_trips();
register_activation_hook( __FILE__, array( 'Purecharity_Wp_Trips', 'activation_check' ) );


/**
 * Template tags
 *
 * @since 1.0.0
 */
function purecharity_trips(){
  $base_plugin = new Purecharity_Wp_Base();

  $opts = array(
    'limit'     => 10,
    'country'   => get_query_var('country'),
    'region'    => get_query_var('region'),
    'query'     => get_query_var('q'),
    'cause'     => get_query_var('cause'),
    'date'      => get_query_var('date'),
    'upcoming'  => get_query_var('upcoming'),
    'trip_tag'  => get_query_var('trip_tag'),
    'tag'       => get_query_var('tag'),
    'page'      => get_query_var('_page'),
    'sort'      => get_query_var('sort'),
    'dir'       => get_query_var('dir')
  );

  return $base_plugin->api_call('events/?' . http_build_query(Purecharity_Wp_Trips_Shortcode::filtered_opts($opts)))->events;
}


/**
 * Sets the meta tags for the facebook sharing.
 *
 * @since    1.0.6
 */
add_action( 'wp_head', 'set_meta_tags' );
function set_meta_tags(){
	if(isset($_GET['event_id'])){
		$base_plugin = new Purecharity_Wp_Base();
		$event = $base_plugin->api_call('events/'. $_GET['event_id']);
		echo '
			<meta property="og:title" content="'.$event->name.'">
			<meta property="og:image" content="'.$event->images->small.'">
			<meta property="og:description" content="'.$event->about.'">
		' . "\n";
	}
}

/**
 * Force the use of a specific template
 *
 * @since    1.0.2
 */
function gt_force_template() {
  $options = get_option( 'purecharity_trips_settings' );
  include(TEMPLATEPATH . '/' . $options['single_view_template']);
  exit;
}

/*
 * Plugin updater using GitHub
 *
 * Auto Updates through GitHub
 *
 * @since   1.0.4
 */
add_action( 'init', 'purecharity_wp_trips_updater' );
function purecharity_wp_trips_updater() {
  if ( is_admin() ) {
    $tr_config = array(
      'slug' => plugin_basename( __FILE__ ),
      'proper_folder_name' => 'purecharity-wp-trips',
      'api_url' => 'https://api.github.com/repos/purecharity/pure-trips',
      'raw_url' => 'https://raw.githubusercontent.com/purecharity/pure-trips/master/purecharity-wp-trips/',
      'github_url' => 'https://github.com/purecharity/pure-trips',
      'zip_url' => 'https://github.com/purecharity/pure-trips/archive/master.zip',
      'sslverify' => true,
      'requires' => '3.0',
      'tested' => '3.3',
      'readme' => 'README.md',
      'access_token' => '',
    );
    new WP_GitHub_Updater( $tr_config );
  }
}
