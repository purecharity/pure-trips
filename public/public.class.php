<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://purecharity.com
 * @since      1.0.0
 *
 * @package    Purecharity_Wp_Trips
 * @subpackage Purecharity_Wp_Trips/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Purecharity_Wp_Trips
 * @subpackage Purecharity_Wp_Trips/public
 * @author     Pure Charity <dev@purecharity.com>
 */
class Purecharity_Wp_Trips_Public {

  const DATE_FORMAT = "M j, Y";
  const MONTH_FORMAT = "M Y";

	/**
	 * The Trip.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $event    The Trip.
	 */
	public static $event;

	/**
	 * The Trips collection.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $events    The Trips collection.
	 */
	public static $events;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/public.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Custom CSS in case the user has chosen to use another color.
	 *
	 * @since    1.0.0
	 */
	public static function custom_css()
	{
		$base_settings = get_option( 'pure_base_settings' );
		$pt_settings = get_option( 'purecharity_trips_settings' );

		// Default theme color
		if(empty($pt_settings['plugin_color'])){
			if($base_settings['main_color'] == NULL || $base_settings['main_color'] == ''){
				$color = '#CA663A';
			}else{
				$color = $base_settings['main_color'];
			}
		}else{
			$color = $pt_settings['plugin_color'];
		}


		$scripts = '
			<style>
				a.pctrip-pure-button { background: '. $color .'; }
				.fr-filtering button { background: '.$color.' }
			</style>
		';

		return $scripts;
	}

	/**
	 * Not found layout for listing display.
	 *
	 * @since    1.0.0
	 */
	public static function list_not_found(){
		$html = self::live_search();
		$html .= "<p>No Trips Found.</p>" . Purecharity_Wp_Base_Public::powered_by();;
		return $html;
	}

	/**
	 * Not found layout for single display.
	 *
	 * @since    1.0.0
	 */
	public static function not_found(){
		return "<p>Trip Not Found.</p>" . Purecharity_Wp_Base_Public::powered_by();;
	}

	/**
	 * Live filter template.
	 *
	 * @since    1.0.1
	 */
	public static function live_search(){

		$options = get_option( 'purecharity_trips_settings' );
		if(isset($options["live_filter"])){

			$html = '
				<div class="fr-filtering">
					<form method="get">
				 		<fieldset class="livefilter fr-livefilter">
				 			<legend>
				 				<label for="livefilter-input">
				 					<strong>Search Trips:</strong>
				 				</label>
				 			</legend>
				 			<input id="livefilter-input" class="fr-livefilter-input" value="'.@$_GET['query'].'" name="query" type="text">
				 			<button class="fr-filtering-button" type="submit">Filter</button>
				 			'. (@$_GET['query'] != '' ? '<a href="#" onclick="$(this).prev().prev().val(\'\'); $(this).parents(\'form\').submit(); return false;">Clear filter</a>' : '') .'
				 		</fieldset>
			 		</form>
			 	</div>
			';
		}else{
			$html = '';
		}
		return $html;
	}

	/**
	 * Listing HTML for Trips
	 *
	 * @since    1.0.0
	 */
	public static function listing(){

		$html = self::custom_css();
		$html .= '<div class="pctrip-list-container">';
		$html .= self::live_search();

		foreach(self::$events->events as $event){
			$truncated = (strlen($event->about) > 100) ? substr($event->about, 0, 100) . '...' : $event->about;
			$html .= '
			 	<div class="pctrip-list-item pure_col pure_span_24">
			 		<div class="pctrip-list-content pure_col pure_span_24">
				 		<div class="pctrip-listing-avatar-container pure_col pure_span_4">
							<a href="?trip='.$event->id.'"><div class="pctrip-listing-avatar" style="background-image: url('.$event->images->small.')"></div></a>
						</div>
						<div class="pctrip-list-body-container pure_col pure_span_20">
							<h3 class="pctrip-title"><a href="?trip='.$event->id.'">'.$event->name.'</a></h3>
							<p class="pctrip-date">'.self::get_date_range($event->starts_at, $event->ends_at).'</p>
							<p class="pctrip-grid-intro">'.$truncated.'</h4>
						</div>
					</div>
			 	</div>
			';

		}

		// Paginator
		if(self::$events->meta->num_pages > 1) {
      $html .= Purecharity_Wp_Trips_Paginator::page_links(self::$events->meta);
    }

		$html .= '</div>';
		return $html;
	}

	/**
	 * Listing HTML for Trips
	 *
	 * @since    1.0.0
	 */
	public static function listing_grid(){
		$html = self::custom_css();
		$html .= '<div class="pctrip-list-container is-grid pure_row">';
		$html .= self::live_search();

		foreach(self::$events->events as $event){
			$truncated = (strlen($event->description) > 100) ? substr($event->description, 0, 100) . '...' : $event->description;
			$html .= '
			 	<div class="pctrip-grid-list-item pure_col pure_span_6">
			 		<div class="pctrip-grid-list-content pure_col pure_span_24">
				 		<div class="pctrip-listing-avatar-container pure_col pure_span_24">
								<a href="?trip='.$event->id.'"><div class="pctrip-grid-listing-avatar pure_col pure_span_24" style="background-image: url('.$event->images->medium.')"></div></a>
							</div>
						<div class="pctrip-grid-lower-content pure_col pure_span_24">
							<p class="pctrip-grid-title">'.$event->name.'</h4>
							<p class="pctrip-date">'.self::get_date_range($event->starts_at, $event->ends_at).'</p>

							<p class="pctrip-grid-intro">'.$truncated.'</h4>
					</div>
					<ul class="pctrip-list-actions pure_col pure_span_24">
						<li><a href="?trip='.$event->id.'">More Info</a></li>
					</ul>
					</div>
			 	</div>
		 	';
		}

		// Paginator
		if(self::$events->meta->num_pages > 1) {
      $html .= Purecharity_Wp_Trips_Paginator::page_links(self::$events->meta);
    }

		$html .= '</div>';
		return $html;
	}


	/**
	 * Single HTML for a Trip
	 *
	 * @since    1.0.0
	 */
	public static function show(){
		return self::custom_css().'
			<div class="pctrip-container">

				<div class="pctrip-header pure_col pure_span_24">
					<img src="'.self::$event->images->large.'">
				</div>
				<div class="pctrip-avatar-container pure_col pure_span_4">
					<div class="pctrip-avatar" href="#" style="background-image: url('.self::$event->images->small.')"></div>
				</div>

				<div class="pctrip-name pure_col pure_span_14">
					<h3>'.self::$event->name.'</h3>
					<p class="pctrip-date">'.self::get_date_range(self::$event->starts_at, self::$event->ends_at).'</p>
				</div>
				<div class="pure_col pure_span_6 pctrip-register">
				'.self::print_register_button().'
				</div>


				<div class="pctrip-content pure_col pure_span_24">


					<div class="pctrip-body pure_col pure_span_18">
						<p>'.self::$event->about.'</p>
					</div>

					<div class="pctrip-sidebar pure_col pure_span_6">

						<div class="pctrip-sidebarsection">
              <h4>Share</h4>
              '.Purecharity_Wp_Base_Public::sharing_links(array(), self::$event->name).'
              <a target="_blank" href="'.Purecharity_Wp_Base_Public::pc_url().'/'.self::$event->slug.'">
			          <img src="' . plugins_url( '../img/share-purecharity.png', __FILE__ ) . '" >
			        </a>
            </div>

						<div class="pctrip-sidebarsection">
							<h4>Trip Costs</h4>
							'.self::print_trip_tickets().'
						</div>

						<div class="pctrip-sidebarsection">
							<h4>Trip Information</h4>
							<p><strong>Trip Type:</strong> '.self::print_trip_types().'</p>
							'.self::print_trip_location().'
							'.self::print_trip_tags().'
						</div>

					</div>

				</div>

			</div>

		';
	}

	/**
	 * Print the country/location
	 *
	 * @since    1.0.0
	 */
	public static function print_trip_location(){
    $html = '';
    if(self::$event->region != ""){
      $html .= "<p><strong>Region:</strong> ".self::$event->region."</p>";
    }

    if(self::$event->location == ""){
      $html .= "<p><strong>Country:</strong> ".self::$event->country."</p>";
    }else{
      $html .= "<p><strong>Location:</strong> ".self::$event->location."</p>";
    }

    return $html;
  }

	/**
	 * Print the register button
	 *
	 * @since    1.0.0
	 */
	public static function print_register_button(){
		if(self::$event->registrations_state == 'open'){
			return '
				<a class="pctrip-pure-button" href="'.self::$event->public_url.'">Register</a>
			';
		}else{
			return '';
		}
	}

	/**
	 * Print the trip leaders
	 *
	 * @since    1.0.0
	 */
	public static function print_trip_leaders(){
		$html = '';
		foreach(self::$event->leaders as $leader){
			$html .= '<p><a href="'.$leader->public_url.'">'.$leader->name.'</a></p>';
		}
		return $html;
	}

	/**
	 * Print the trip tags
	 *
	 * @since    1.0.0
	 */
	public static function print_trip_tags(){
		$tags = array();
		foreach(self::$event->trip_tags as $tag){
			$tags[] = '<a href="?trip_tag='.$tag.'">'.$tag.'</a>';
		}
		if(count($tags) > 0){
			return '<p><strong>Tags:</strong> '.join(', ', $tags);
		}else{
			return '';
		}
	}

	/**
	 * Print the trip types
	 *
	 * @since    1.0.0
	 */
	public static function print_trip_types(){
		$types = array();
		foreach(self::$event->types as $type){
			$types[] = $type;
		}
		return join(', ', array_unique($types));
	}

	/**
	 * Print the trip tickets
	 *
	 * @since    1.0.0
	 */
	public static function print_trip_tickets(){
		$tickets = '';
		foreach(self::$event->tickets as $ticket){
			$tickets .= '
				<p class="pctrip-ticket">
					<strong>'.$ticket->name.'</strong><br /><br />
					<span class="pctrip-ticket-price">'.money_format('$%i', $ticket->price).'</span><br /><br />
					'.$ticket->description.'</br>
					<a class="pctrip-pure-button" href="'.$ticket->public_url.'">Register</a>
				</p>
			';
		}
		return $tickets;
	}


	/**
	 * Calculate date range for a trip
	 *
	 * @since    1.0.0
	 */
  public static function get_date_range($start, $end) {
    $start = strtotime($start);
    $end = strtotime($end);
    $days = ($end - $start) / 3600 / 24;
    if (($days > 31) || date('M',$start) != date('M',$end)) {
      return date(self::DATE_FORMAT, $start) . ' - ' . date(self::DATE_FORMAT, $end);
    } else {
      $parts = preg_split('/([dj])/', self::DATE_FORMAT, -1, PREG_SPLIT_DELIM_CAPTURE);
      $date = '';
      foreach($parts as $part) {
        if ($part == 'd' || $part == 'j') {
          $date .= date($part, $start) . '-' . date($part, $end);
        } else {
          $date .= date($part, $start);
        }
      }
      return $date;
    }
  }

}
