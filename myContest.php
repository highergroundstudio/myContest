<?php
/*
Plugin Name: myContest
Plugin URI: https://github.com/highergroundstudio/myContest
Plugin Docs: http://highergroundstudio.github.io/myContest/
Description: A Premium Wordpress Contest plugin
Version: 2.0.11
Author: Higher Ground Studio
Author Email: kyle.king@highergroundstudio.com 
Text Domain: mycontest
*/

/*
 * metaKey = '_myContest'
 * textdomain = 'mycontest'
 * slug = 'my_contest'
 */

/*
 * Updates
 * =======
 * 1.1.3 - Fix minor bug
 * 1.2.0 - Added logged in users can only vote setting
 * 1.2.1 - Anonymous function error for PHP 5.2.17 fix
 * 1.2.2 - Added Author URL for entries
 * 1.2.3 - Fixed js naming mistake
 * 1.2.4 - Fixed logged in users can only vote bug
 * 1.2.5 - data-entry-id missing closing tag
 * 1.2.6 - Entry Image magnify hover overlay 
 * 1.2.6 - Fixed sticky contest
 * 1.2.7 - Added page support for the shortcode
 * 1.2.8 - Added settings
 * 1.3.0 - Added update notification
 * 		   Spin.js fix for IOS
 * 1.3.1 - Social share added
 * 1.4.0 - Shortcode fix
 *         JS naming change and plugin split
 *         Performance updates
 *         Content no included in mycontest.php rather than separate file
 * 2.0.0 - Wordpress SEO conflict fix (no printing out of content)
 *		   Voting time to custom solution
 * 2.0.2 - Shortcode bug fix (return instead of echo)
 * 2.0.3 - mycontestbox double entry bug fixed
 * 2.0.4 - window.open bug fix
 * 2.0.5 - multiple shortcodes js fix
 *         Image SSL fix
 * 2.0.6 - Vote button under mycontestbox fix
 * 2.0.7 - Italian translation update
 * 2.0.8 - Undo feature instead of delete dialog
 * 2.0.9 - Hover overlay from php to js 
 *         Remove advanced tab
 *         JS to min folder
 * 2.0.10 -Ribbons feature added to general as well
 *         Few fixes
 * 2.0.11 - Taxonomies added
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Make sure we are not running more than one instance
if ( ! class_exists( 'myContest' ) ) {

/**
 * Main myContest Class
 *
 * Contains the main functions for myContests
 *
 * @class myContest
 * @since 1.0
 * @package	myContest
 * @author Higher Ground Studio
 */
class myContest {

	/*
	 * the current version
	 */
	var $version = '2.0.11';
	/*
	 * Plugin PATH var setup
	 */
	var $pluginPath;
	/*
	 * Plugin URL var setup
	 */ 
    var $pluginUrl;
    /*
	 * Javascript minified file on (setting)
	 */
    var $js_min = true;
    /*
	 * Stylesheet minified file on (setting)
	 */
    var $css_min = true;
    /*
	 * Debug off (setting)
	 */
    var $debug = false;
    /*
	 * Shortcode on (setting)
	 */
    var $shortcode_support = true;
    /*
	 * Add to query on (setting)
	 */
    var $query_support = true;
    /*
	 * Powered by myContest (setting)
	 */
    var $mycontest_powered = true;
    /*
	 * Beta tester (setting)
	 */
    var $beta_tester = false;
    /*
	 * Purchase code (setting)
	 */
    var $purchase_code = false;
    /*
	 * Disable updates (setting)
	 */
    var $update_disable = false;

    /**
	 * myContest settings object
	*/
    var $settings;

    /**
	 * myContest content object
	*/
    var $content;    
    /**
	 * myContest update check object
	*/
    var $updateCheck;
    /**
	 * aqua-resizer object
	*/
    var $aquaResize;
	
	/**
	 * Initialization function
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 * @since 1.0.0
	 * @access public
	 * @return void
	 */
	public function __construct() {

		//register an activation and de-activation hook for the plugin
		register_activation_hook( __FILE__, array( $this, 'install_my_contest' ) );
		register_deactivation_hook( __FILE__, array( $this, 'uninstall_my_contest' ) );

		// Set Plugin Path  
        $this->pluginPath = plugin_dir_path( __FILE__ ); 

        // Set Plugin URL and global plugin url
        $this->pluginUrl = plugin_dir_url( __FILE__ );
        global $pluginUrl;
        $pluginUrl = $this->pluginUrl;

        // Hooks
        // -----------------------------------------------------------------------
        
        // Create your custom post type (my_contest)
		add_action( 'init', array( $this, 'create_post_type_contest' ) );

		// Initialize
        add_action( 'init', array( $this, 'init' ), 0 );

		// Hook ajax callbacks
		add_action('wp_ajax_my-contest', array($this, 'ajax_callback') );
		add_action('wp_ajax_nopriv_my-contest', array($this, 'ajax_callback') );
	}

	/**
	 * Init myContest when WordPress Initialises.
	 *
	 * @access public
	 * @since 1.2.7
	 * @return void
	 */
	public function init() {
		// Setup localization
		load_plugin_textdomain( 'mycontest', false, dirname( plugin_basename( __FILE__ ) ) . '/inc/lang' );

		// Setup our settings
		$this->setup_settings();

		// If we are in admin else we are in the frontend
        if ( is_admin() ) {

			// Run in admin only!
			//-------------------------------------------------
			
			// Include and setup our settings class
			require( $this->pluginPath . '/inc/classes/settings.php' );
			$this->settings = new myContest_settings($this->purchase_code);

			// Don't load this if update disable is enabled
			if(!$this->update_disable):
				// Include and setup our updateCheck class
				require( $this->pluginPath . '/inc/classes/updateCheck.php' );
				$this->updateCheck = new myContest_updateCheck(
					$this->version,
					// 'http://127.0.0.1/myContest%20update%20server/update.php', // local testing
					'http://mycontest.highergroundstudio.com/update-api/update.php', // production
					plugin_basename(__FILE__),
					$this->purchase_code,
					$this->beta_tester
				);
			endif; // end update  disable

			// Setup notices if in debug mode
			if($this->debug) include( $this->pluginPath . '/inc/admin/id-admin-notices.php' );

        	// Load JavaScript and stylesheets
			add_action('admin_enqueue_scripts', array($this, 'register_scripts_and_styles_admin'));

			// Setup the metaboxes for the custom post type
			add_action( 'add_meta_boxes', array( $this, 'add_custom_meta_boxes' ) );

			// Init plugin header
			add_action('admin_head', array( $this, 'plugin_header' ) );

			// Save our post meta when post saves
			add_action('save_post', array($this, 'save_custom_meta') );

			// Add our settings menu
			add_action('admin_menu', array($this->settings, 'add_settings_page') );
			add_action('admin_init', array($this->settings, 'settings_init') );
			add_filter( 'plugin_action_links',  array($this,'mycontest_plugin_action_links'), 10, 2 );

		} else {

			// Run in frontend only!
			//-------------------------------------------------

			// Load JavaScript and stylesheets
			add_action('wp_enqueue_scripts', array($this, 'register_scripts_and_styles'));

			// Hook into the content so that you can add in the contest on myContest pages
			add_filter('the_content', array($this, 'mc_content_display'), 8);

			// Check if shortcode support is enabled
			if($this->shortcode_support){
				// Add shortcode to use if you want or if there is a problem
				add_shortcode('my_contest_shortcode', array($this, 'shortcode_router'));
			}
			// Check if add to query support is enabled
			if($this->query_support){
				// add my post type to the main query
				add_action( 'pre_get_posts', array($this, 'add_to_query' ) );
			}
			add_action( 'pre_get_posts', array($this, 'sticky_support' ) );

		}
	}

	/**
	 * Add plugin action links
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 *
	 * @since 1.2.8
	 */
	function mycontest_plugin_action_links( $links, $file ) {
		// Make sure this is ours
		if ( $file == plugin_basename( dirname(__FILE__).'/myContest.php' ) ) {
			// Add our link on the end of the array
			$links[] = '<a href="' . admin_url( 'edit.php?post_type=my_contest&page=my_contest_settings' ) . '">'.__( 'Settings' ).'</a>';
		}

		// Return the links
		return $links;
	}

	/**
	 * Get and Setup our settings
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 *
	 * @since 1.2.8
	 */
	function setup_settings(){
		// Get our settings
		$this->settings = get_option('mycontest_settings');

		// Set the settings
		if(isset($this->settings['minify_js'])) $this->js_min = false;
		if(isset($this->settings['minify_css'])) $this->css_min = false;
		if(isset($this->settings['debug_on'])) $this->debug = true;

		if(isset($this->settings['shortcode_support'])) $this->shortcode_support = false;
		if(isset($this->settings['query_support'])) $this->query_support = false;
		if(isset($this->settings['mycontest_powered'])) $this->mycontest_powered = false;

		if(isset($this->settings['beta_tester'])) $this->beta_tester = true;
		if(isset($this->settings['update_disable'])) $this->update_disable = true;
		if(isset($this->settings['purchase_code'])) $this->purchase_code = $this->settings['purchase_code'];
	}

	/**
	 * Create post type
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 *
	 * @since 1.0.0
	 */
	function create_post_type_contest() {
		$labels = array(
		    'name' => 'myContest',
		    'singular_name' => 'Contest',
		    'add_new' => __('Add New Contest', 'mycontest'),
		    'add_new_item' => __('Add New Contest', 'mycontest'),
		    'edit_item' => __('Edit Contest', 'mycontest'),
		    'new_item' => __('New Contest', 'mycontest'),
		    'all_items' => __('All Contests', 'mycontest'),
		    'view_item' => __('View Contest', 'mycontest'),
		    'search_items' => __('Search Contests', 'mycontest'),
		    'not_found' =>  __('No Contests found', 'mycontest'),
		    'not_found_in_trash' => __('No Contests found in Trash', 'mycontest'), 
		    'parent_item_colon' => '',
		    'menu_name' => 'myContest'
		  );

		  $args = array(
		    'labels' => $labels,
		    'public' => true,
		    'publicly_queryable' => true,
		    'show_ui' => true, 
		    'show_in_menu' => true, 
		    'query_var' => true,
		    'rewrite' => array( 'slug' => 'contest' ),
		    'capability_type' => 'post',
		    'has_archive' => true, 
		    'hierarchical' => false,
		    'menu_position' => 109,
		    'menu_icon' => $this->pluginUrl . '/inc/images/contest-icon.png',
		    'taxonomies' => array('category', 'post_tag'),
		    'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'comments', 'meta_box', 'post-formats', 'revisions', 'trackbacks' )
		  ); 

		  register_post_type( 'my_contest', $args );
	}

	/**
	 * Add the meta boxes
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */ 
	function add_custom_meta_boxes() {  
	    add_meta_box(  
	        '_entries', // $id  
	        __('myContest', 'mycontest'), // $title   
	        array($this, 'entries_meta_boxes_render'), // $callback  
	        'my_contest', // $page  
	        'advanced', // $context
	        'high' // $priority
	    );
	}

	/**
	 * Add to main loop query (show on front page)
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 *
	 * @since 1.0.0
	 *
	 * @param object $query The wp post query object.
	 * @return object $query Not necessary but good practice.
	 */
	function add_to_query( $query ) {
		// Make sure this is the main query and on the home
		if ( $query->is_main_query() && $query->is_home() ):

			// Get supported post types
			 $supported = $query->get( 'post_type' );
        	
        	// If there are no supported post types or if it is just post
	        if ( !$supported || $supported == 'post' ){
	        	// Add our post type
	            $supported = array( 'post', 'my_contest' );
	        }elseif ( is_array( $supported ) ){
	        	// Add our post type to the end of the supported post types
	            array_push( $supported, 'my_contest' );
	        }
	        // set our post type in the supported post types
	        $query->set( 'post_type', $supported );

		endif; // End query main loop ad front page

		//	always return the query
		return $query;
	}

	/**
	 * Stick the contest to the front page (optional)
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 *
	 * @since 1.0.1
	 *
	 * @param object $query The wp post query object.
	 * @return object $query Not necessary but good practice.
	 */
	function sticky_support( $query ) {
		// Only run on the main page
		if ( is_admin() || 
             is_single() || 
             is_page() || 
             $query->get('suppress_filters') ) {
            return $query; //exit out early
        }

        // if the query is the main query and on home
		if ( $query->is_main_query() && $query->is_home() ):
            // Add support for stick posts
			$post_types = array();

			// Get our post types
			$query_post_type = $query->get( 'post_type' );

			// Add to query type
			if ( empty( $query_post_type ) ) {
				$post_types[] = 'post';
			} elseif ( is_string( $query_post_type ) ) {
				$post_types[] = $query_post_type;
			} elseif ( is_array( $query_post_type ) ) {
				$post_types = $query_post_type;
			} else {
				return; // Unexpected value
			}

		endif; // End query main loop ad front page

		//	always return the query
		return $query;
	}

	/**
	 * Save our contest data
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 *
	 * @since 1.0.0
	 * @return void
	 */
	function save_custom_meta(){

		//Make sure stuff is being posted
		if( empty($_POST) || !isset($_POST['post_ID'])) return;

		// Grab our global $post var
		global $post;

		// Set our post id
		$post_ID = $_POST['post_ID'];

		// verify if this is an auto save routine. 
		// If it is our form has not been submitted, so we dont want to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
			//if ($this->debug) $this->add_admin_notice("myContest Error N1000: Doing Autosave");
			return;
		}

		// verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if ( !isset($_POST['mycontest-nonce']) || !wp_verify_nonce( $_POST['mycontest-nonce'], 'save_entries' ) ){
			// if ($this->debug) $this->add_admin_notice("myContest Error N1001");
			return;
		}

		// Check user permissions
		if ( 'page' == get_post_type( $post ) ) {
			// Can our user edit pages?
	    	if ( !current_user_can( 'edit_page', $post_ID ) ){
	    		if ($this->debug) $this->add_admin_notice("myContest Error N1002: Current user cannot edit page");
	        	return; //exit out
	    	}
	  	}else{
	    	if ( !current_user_can( 'edit_post', $post_ID ) ){
	    		if ($this->debug) $this->add_admin_notice("myContest Error N1003: Current user cannot edit post");
	        	return; //exit out
	    	}
	  	}

	  	//----------------------------------------------------------------
	  	// OK, we're authenticated: we need to sanitize and save the data
	  	//----------------------------------------------------------------

	  	// Loop through and sanitize the setting fields
		foreach($_POST['settings'] as $field => $value){
			// Check what type of field and sanitize accordingly
			switch ($field):
				case "e_txt":
					// sanitize textarea, allow html
					$sanitized_field = wp_kses_post( $value );
					break;
				case "s_txt":
					// sanitize textarea, allow html
					$sanitized_field = wp_kses_post( $value );
					break;
				case "regvoteonlyhtml":
					// sanitize textarea, allow html
					$sanitized_field = wp_kses_post( $value );
					break;
				case "sharedesc":
					// sanitize textarea, allow html
					$sanitized_field = wp_kses_post( $value );
					break;
				default:
					$sanitized_field = sanitize_text_field( $value );
					break;
			endswitch;
			//sanitize user input
			$mydata['settings'][$field] = $sanitized_field;
		}

		if(isset($_POST['settings']['votenoshow'])){
			$mydata['settings']['votenoshow'] = true;
		}else{
			$mydata['settings']['votenoshow'] = false;
		}

		// Loop through the post entries
		foreach($_POST['myContest'] as $entry){
			// Don't save the starter
			if($entry['entryID'] == '{changeStarterID}'){
				continue;//skip
			}
			// Loop though each field of the entry
			foreach($entry as $field => $value){
				// Check what type of field and sanitize accordingly
				switch ($field):
					case "email":
						//sanitize user input
						$sanitized_field = sanitize_email( $value );
						break;
					case "author":
						//sanitize user input
						$sanitized_field = sanitize_user( $value );
						break;
					case "img_url":
						//sanitize user input
						if($value === "{starterImage}") $value = "";
						$sanitized_field = filter_var($value, FILTER_SANITIZE_URL);
						break;
					case "url":
						//sanitize user input
						$sanitized_field = filter_var($value, FILTER_SANITIZE_URL);
						break;
					case "authorurl":
						//sanitize user input
						$sanitized_field = filter_var($value, FILTER_SANITIZE_URL);
						break;
					case "votes":
						// If votes are not set then it must be 0
						if(	!isset($value) || empty($value) ){ $value = intval(0); }
						//sanitize user input
						$sanitized_field = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
						$sanitized_field = intval($sanitized_field);
						break;
					case "descr":
						// sanitize textarea, allow html
						$sanitized_field = wp_kses_post( $value );
						break;
					default:
						//sanitize anything else
						$sanitized_field = sanitize_text_field( $value );
				endswitch;

				$mydata['entries'][$entry['entryID']][$field] = $sanitized_field;
			}
		}

		// Save the meta data (but delete first so there are no problems)
		delete_post_meta($post->ID, '_myContest');
		add_post_meta($post->ID, '_myContest', $mydata);
	}

	/**
	 * Render the entries meta box
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 *
	 * @since 1.0.0
	 *
	 * @param object $post Current post.
	 */
	function entries_meta_boxes_render($post){
		include( $this->pluginPath . '/inc/views/entries_meta_boxes.php' );
	}

	/**
	 * Render the contest on the page using shortcode
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 *
	 * @since 1.1.0
	 *
	 * @return string $out Contest output buffer data.
	 */
	function shortcode_router( $atts ){

		// Don't show if is home, archive, or category page just exit out
		if( is_home() || is_archive() || is_category() ){ 
			return; //exit out
		}

		extract(shortcode_atts(array(
	      'id' => false,
	   ), $atts));

		// If there is no postid 
		if($id && is_int($id)){
			// If user can manage options then show the shortcode error
			if ( current_user_can('manage_options')) {
				echo "<h1 style='background-color:red;color:white;padding:10px;'>" . __( "Error: Add an ID to the myContest shortcode", "mycontest" ) . "</h1>";
			}
			return; // exit out
		}

		// Makes it work for some reason
		global $post;
		$post->ID = $id;

		// Get our frontend scripts and styles
		$this->register_scripts_and_styles(true, $id);

		// Get the contest
		return $this->get_content($post->ID);
	}
	
	/**
	 * Render the contest on the page
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 *
	 * @since 1.0.0
	 *
	 * @param string $content  Post content.
	 * @return string $content With our contest added.
	 */
	function mc_content_display($content = null){
		//Remove our filter so there is no problems
		remove_filter( 'the_content', array($this, 'mc_content_display'));

		// Only show on single page
		// If this is not our post type or if is home,archive, or category page just exit out
		//  return out content
		if( !is_single() || get_post_type() !== 'my_contest' || is_home() || is_archive() || is_category() ){ 
			// Add our filter back in
			add_filter('the_content', array($this, 'mc_content_display'), 8);
			return $content; //exit out
		}

		// just grabbing the global variable for use
		global $post;

		

		// echo $content;

		$contest_content = $this->get_content($post->ID);

		if($contest_content == false){
			// Exit out
			return $content;
		}else{
			// Add to our content
			$content .= $contest_content;
		}

		if($this->mycontest_powered){ 
			$content .= '<a href="http://goo.gl/ty3UM" id="mycontestpowered" ><img src="' . $this->pluginUrl . "/inc/images/contest-icon32.png" . '" alt="Powered by myContest"></a>';
		}

		

	    // Add our filter back in
	    add_filter('the_content', array($this, 'mc_content_display'), 8);

	    return $content;
	    // return;
	}

	/**
	 * Get our contest content
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 *
	 * @since 1.3.1 (moved from include file)
	 *
	 * @param string $id  Post (contest) id.
	 * @return string Contest html.
	 */
	function get_content($id){

		// Setup our contest content vars
		$contest_content = "";

		// Get our contest data
		$meta = get_post_meta($id, '_myContest', TRUE);

		// If the setting registered user can vote only is true and user is not logged in
		// Do not go any further and show the html
		// @since 1.2.0
		if ( isset($meta['settings']['regvoteonly']) & !is_user_logged_in() ){
			$contest_content .= $meta['settings']['regvoteonlyhtml']; // add to contest content
			if (isset($meta['settings']['regvoteonlyshow'])) return false; //exit out
		}

		// exit out if no entries
		if( empty($meta['entries']) ) return false;//exit out! 

		// Setup our start and end dates
		if(isset( $meta['settings']['s_date'] )  & !empty($meta['settings']['s_date'])){
			list($month, $day, $year) = explode('/', $meta['settings']['s_date']);
			// check if hours are set if not set to 00
			if(!isset($meta['settings']['s_h']) || empty($meta['settings']['s_h'])) $meta['settings']['s_h'] = '00';
			// check if minutes are set if not set to 00
			if(!isset($meta['settings']['s_mn']) || empty($meta['settings']['s_mn'])) $meta['settings']['s_mn'] = '00';
			$start_date = mktime( $meta['settings']['s_h'], $meta['settings']['s_mn'], '00', $month, $day, $year );
		}
		if( isset( $meta['settings']['e_date'] ) & !empty($meta['settings']['e_date']) ){
			list($month, $day, $year) = explode('/', $meta['settings']['e_date']);
			// check if hours are set if not set to 00
			if(!isset($meta['settings']['e_h']) || empty($meta['settings']['e_h'])) $meta['settings']['e_h'] = "00";
			// check if minutes are set if not set to 00
			if(!isset($meta['settings']['e_mn']) || empty($meta['settings']['e_mn'])) $meta['settings']['e_mn'] = "00";
			$exp_date = mktime( $meta['settings']['e_h'], $meta['settings']['e_mn'], '00', $month, $day, $year );
		}

		//get todays date
		$todays_date = date("Y-m-d h:i");
		// convert to time
		$todays_date = strtotime($todays_date);

		//setup voting ended var
		$votingend = false;
		$votingstart = true;

		// compare if before expiration date
		if(isset($start_date)){
			if ($start_date > $todays_date) {
				// send start message to contest_content if it is set
				if( isset( $meta['settings']['s_txt'] ) & !empty($meta['settings']['s_txt']) ) $contest_content .= $meta['settings']['s_txt'];
				// just exit out of here if not showing entries
				if( isset( $meta['settings']['s_entries'] ) ) return $content_content;
				$votingstart = false;
			}
		}

		// compare if past expiration date
		if(isset($exp_date)){
			if ($exp_date < $todays_date) {
				// Sort to high
				$meta['settings']['sort'] = "high";
				// send end message if it is set
				if( isset( $meta['settings']['e_txt'] ) & !empty($meta['settings']['e_txt']) ) $contest_content .= $meta['settings']['e_txt'];
				// just exit out of here if not showing entries
				if( isset( $meta['settings']['e_entries'] ) ) return $contest_content;
				$votingend = true;
			}
		}

		// Sort the entries
		$meta = $this->entry_sort($meta);

		global $post;

		$contest_content .= '<div class="myContest-entries postid' . $post->ID . ' ' . (isset($meta['settings']['votenoshow']) & $meta['settings']['votenoshow']  ? "votenoshow":"voteshow") . '" data-postid="' . $post->ID . '" >';

		//number of entries
		$entriesnumb = 0;

		// lets loop through the entries
		foreach($meta['entries'] as $entry):

			// add one
			$entriesnumb++;

			// Break (end) the foreach if greater than the number of entries to show
			if(!empty($meta['settings']['e_showentries']) & $entriesnumb > $meta['settings']['e_showentries']) break;

			// Don't do this if there is no img_url
			if(!empty($entry['img_url'])){
				// change image size
				$image = $this->aq_process( $url = $entry['img_url'], $width = 250, $height = null, $crop = true, $single = false );
				$retinaImage = $this->aq_process( $url = $entry['img_url'], $width = 500, $height = null, $crop = true, $single = false );
			}

			// Votes are 0 if not set
			if(empty($entry['votes']) || !isset($entry['votes'])) $entry['votes'] = 0;

			$contest_content .= '<div class="myContest-entry" style="width:250px;">';

				// Show winner ribbons
				if($votingend & isset($meta['settings']['entry_ribbons']) & $entriesnumb <= 3 ) {
					$contest_content .=  '<div class="ribbon-wrapper-green"><div class="ribbon-green">';
					switch ($entriesnumb){
						case 1:
						$contest_content .=  '1st';
						break;
						case 2:
						$contest_content .=  '2nd';
						break;
						case 3:
						$contest_content .=  '3rd';
						break;
					}
					$contest_content .=  '</div></div>'; // Ending of .ribbon-wrapper-green and .ribbon-green
				}
				if( isset($meta['settings']['a_entry_ribbons']) & $entriesnumb <= 3 ) {
					$contest_content .=  '<div class="ribbon-wrapper-green"><div class="ribbon-green">';
					switch ($entriesnumb){
						case 1:
						$contest_content .=  '1st';
						break;
						case 2:
						$contest_content .=  '2nd';
						break;
						case 3:
						$contest_content .=  '3rd';
						break;
					}
					$contest_content .=  '</div></div>'; // Ending of .ribbon-wrapper-green and .ribbon-green
				}

				// Don't do this if there is no img_url
				if(!empty($entry['img_url']) || !isset($entry['img_url'])){
					

					$contest_content .= '<a href="' . 
											$entry['img_url'] . 
											'" rel="myContest-gallery" class="mycontestbox" data-title="' . 
											$entry['entryTitle'] . 
											'" data-title-id="caption' . 
											$entry['entryID'] . 
										'" style="cursor:zoom;">';
					

					$contest_content .= '<img src="' .
											$image[0] .
											'" data-retina="' .
											$retinaImage[0] . 
											'" alt="' . 
											$entry['entryTitle'] . 
											'" class="entryImg retina" width="' .
											$image[1] .
											'px" height="' .
											$image[2] . 
											'px" />';



					$contest_content .= '</a>'; // closing of .mycontestbox

					unset($image);

				} // End if there is img_url


			// if the entry title OR author name are not empty
			if(!empty($entry['entryTitle']) || !empty($entry['author']) || !empty($entry['descr'])): 

				$contest_content .= '<div class="caption">';

					// if the title is not empty
					if(!empty($entry['entryTitle'])){

						$contest_content .= '<h3 class="entrytitle">';

						if(!empty($entry['url'])){
							$contest_content .= '<a target="_blank" href="' .
													$entry['url'] .
													'">' .
													$entry['entryTitle'] .
													'</a>';
				    	}else{
				    		$contest_content .= $entry['entryTitle'];
				    	}
				    	$contest_content .= '</h3>';
				    } // if the title is not empty

				    // if the author is not empty
				    if(!empty($entry['author'])){
				    	$contest_content .= '<h4 class="entryauthor">'; 
				    	$contest_content .= __('by','mycontest');
				    	// Check if the author url is set
				    	if(isset($entry['authorurl']) & !empty($entry['authorurl'])){
				    		$contest_content .= '<a target="_blank" href="'.$entry['authorurl'].'">';
				    		$contest_content .= $entry['author'];
				    		$contest_content .= '</a>';
				    	
				    	}else{
				    		// if the author url is not set
				    		$contest_content .= '&nbsp;' . $entry['author'];
				    	}
				    	$contest_content .= '</h4>';
				    } //end if the author is not empty 

				    // if the description is not empty
				    if(!empty($entry['descr'])){
				    	$contest_content .= '<p>';
				    	$contest_content .= $entry['descr'];
				    	$contest_content .= '</p>';
				    } //end if the descr is not empty 

				$contest_content .= '</div>';
			endif; // end if the entry title OR author name are not empty
			
			if ( isset($meta['settings']['regvoteonly']) & !is_user_logged_in() ){/* do nothing */}else{

				$contest_content .= $this->social_share($meta, $entry, $id);

				// Parameters for the link
				$linkparams = 'class="myContest-votes-button ' . ($votingend ? "disabled" : "active ".$entry['entryID']) . '" ' .
								($votingend ? "disabled='disabled'" : "data-entry-id='".$entry['entryID']."' ");

				$contest_content .= '<p class="vote-holder"><a href="#vote" ' . $linkparams . '>';
				$contest_content .= '<span class="myContest-count">';

				if($meta['settings']['votenoshow']){
					$contest_content .= __('Vote for this', 'mycontest');
				}else{
					$contest_content .=  $entry['votes'] . '&nbsp;' . __('votes','mycontest');
				}

				$contest_content .= '</span>';
				$contest_content .= '</a></p>';
			} // end else

			$contest_content .= '<div id="caption' . $entry['entryID'] . '" style="display:none;">';

			if ( isset($meta['settings']['regvoteonly']) & !is_user_logged_in() ){
				// Do not show to the user that is not logged in
				// $contest_content .= "Do not show";
			}else{
				// Show the voting button
				$contest_content .= '<a href="#vote" class="myContest-votes-button ' . ($votingend ? "disabled" : "active") . " " . $entry['entryID'] . '"';
				$contest_content .= ($votingend ? "disabled='disabled'" : "");
				$contest_content .= 'data-entry-id="' . $entry['entryID'] . '">';
				$contest_content .= '<span class="myContest-count">';
					if($meta['settings']['votenoshow']){
						$contest_content .= __('Vote for this', 'mycontest');
					}else{
						$contest_content .=  $entry['votes'] . '&nbsp;' . __('votes','mycontest');
					}
				$contest_content .= '</span>';
				$contest_content .= '<img src="' . $this->pluginUrl . '/inc/images/loading.gif' . '" class="myContestloading" style="display:none;"/>';
				$contest_content .= '</a>';
			} // End if registered voting only

			$contest_content .= '</div>';
			$contest_content .= '</div>';	        

		endforeach; // end looping through entries


		$contest_content .= '</div> <!--end .myContest-entries-->';
		
		return $contest_content;

	} // end get_content function

	/**
	 * Social Share
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 *
	 * @since 2.0.0
	 *
	 * @param array $meta, entry   Contest meta and entry data
	 * @return str $contest_content_holder Social Share HTML.
	 */
	function social_share($meta, $entry, $id) {

		$social_share_debug = false;

		// Social share setting
		if(isset($meta['settings']['socialshare'])):

			$contest_content_holder = ""; 

			// Setup 0 counts
			$count = array(
				'twitter' => 0,
				'facebook' => 0,
				'googleplus' => 0,
				'pinterest' => 0
			);

			// Build the url for the entry
			$entryURL = get_permalink( $id ) . "?entryid=" . $entry['entryID'];
			// $entryURL = 'http://google.com'; // testing
			//Encode url for use on some api's
			$entryURLencoded = urlencode($entryURL);
			$shortenedURL = $entryURL;

if(!$social_share_debug):

			// make sure that cURL is enabeled
			if  ( in_array('curl', get_loaded_extensions()) ):
				// ==================================================
				// ---------------- General cURL --------------------
				// ==================================================
				$curlObj = curl_init();
				curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
				curl_setopt($curlObj, CURLOPT_HEADER, 0);
				curl_setopt($curlObj, CURLOPT_HTTPHEADER, array('Content-type:application/json'));
				curl_setopt($curlObj, CURLOPT_POST, 1);
				curl_setopt($curlObj,CURLOPT_CONNECTTIMEOUT, 5);

				// ==================================================
				// ------------ Get shortened url -------------------
				// ==================================================
				if(isset($meta['settings']['ssshortenedlink'])):

					$apiKey = 'AIzaSyApBUGmbUj5tYojIzL-Nep7fS9JoDQOMLM';
					$postData = array('longUrl' => $entryURL, 'key' => $apiKey);
					$jsonData = json_encode($postData);
					curl_setopt($curlObj, CURLOPT_URL, 'https://www.googleapis.com/urlshortener/v1/url');
					curl_setopt($curlObj, CURLOPT_POSTFIELDS, $jsonData);

					$response = curl_exec($curlObj);
					//change the response json string to object
					$json = json_decode($response);
					// Set out variable
					if(isset($json->id)) $shortenedURL = $json->id;

				endif; // end shortened link

				// Setup for rest of requests
				curl_setopt($curlObj, CURLOPT_POSTFIELDS, "");
				curl_setopt($curlObj, CURLOPT_POST, 0);

				// ==================================================
				// ------------ Get twitter count -------------------
				// ==================================================
				if(isset($meta['settings']['sstwitter'])):
					
					curl_setopt($curlObj, CURLOPT_URL, 'http://urls.api.twitter.com/1/urls/count.json?url=' . $entryURLencoded);

					$response = curl_exec($curlObj);
					$json = json_decode($response);

					if(isset($json->count)) $count['twitter'] = $json->count;

				endif; // end twitter

				// ==================================================
				// ------------ Get facebook count ------------------
				// ==================================================
				if(isset($meta['settings']['ssfacebook'])):
					curl_setopt($curlObj, CURLOPT_URL, "http://graph.facebook.com/fql?q=SELECT%20url,%20total_count%20FROM%20link_stat%20WHERE%20url='".$entryURLencoded."'");

					$response = curl_exec($curlObj);
					$json = json_decode($response);

					if(isset($json->data[0]->total_count)) $count['facebook'] = $json->data[0]->total_count;
				endif; // end facebook

				// ==================================================
				// ------------ Get pinterest count -----------------
				// ==================================================
				if(isset($meta['settings']['sspinterest'])):
					curl_setopt($curlObj, CURLOPT_URL, 'http://api.pinterest.com/v1/urls/count.json?url='.$entryURLencoded);

					$response = curl_exec($curlObj);
					$json_string = preg_replace('/^receiveCount\((.*)\)$/', "\\1", $response);
					$json = json_decode($json_string);

					if(isset($json->count)) $count['pinterest'] = intval($json->count);
				endif; // end pinterest

				// ==================================================
				// ------------ Get google+ count ------------------
				// ==================================================
				if(isset($meta['settings']['ssgoogleplus'])):
					curl_setopt($curlObj, CURLOPT_URL, "https://clients6.google.com/rpc?key=AIzaSyCKSbrvQasunBoV16zDH9R33D88CeLr9gQ");
					curl_setopt($curlObj, CURLOPT_POST, 1);
					curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($curlObj, CURLOPT_POSTFIELDS, '[{"method":"pos.plusones.get","id":"p","params":{"nolog":true,"id":"' . $entryURL . '","source":"widget","userId":"@viewer","groupId":"@self"},"jsonrpc":"2.0","key":"p","apiVersion":"v1"}]');

					$response = curl_exec($curlObj);
					$json = json_decode($response, true);

					if(isset($json[0]['result']['metadata']['globalCounts']['count'])) $count['googleplus'] = intval($json[0]['result']['metadata']['globalCounts']['count']);

				endif; // end google+

				// Close cURL
				curl_close($curlObj);

			endif; // end cURL check

			// Format numbers
			foreach($count as $s => $n){
				if ($n < 1000) {
					   // Anything less than a million
					   $n_format = number_format($n);
				}else if ($n < 1000000){
					// Anthing less than a thousand
					if($n > 10000){
						$n_format = number_format($n / 10000, 0) . 'K';
					}else{
						$n_format = number_format($n / 10000, 1) . 'K';
					}
						
				} else if ($n < 1000000000) {
					   // Anything less than a billion
					   if($n < 10000000){
					    $n_format = number_format($n / 1000000, 1) . 'M';
					}else{
						$n_format = number_format($n / 1000000, 0) . 'M';
					}
				} else {
					   // At least a billion
					   $n_format = number_format($n / 1000000000, 0) . 'B';
				}
				$count[$s] = $n_format;
			}
endif;

			// Share vars
			if( !empty($entry['img_url']) ){
				$shareImage = urlencode($entry['img_url']);
			}elseif( is_numeric($thumbID = get_post_thumbnail_id($id)) ){
				$shareImage = urlencode(wp_get_attachment_url( $thumbID ));
			}else{
				$shareImage = "";
			}
			// Set the title
			if( !empty($entry['entryTitle']) ){
				$shareTitle = urlencode($entry['entryTitle']);
			}else{
				$shareTitle = urlencode(get_the_title($id));
			}
			// Share summary message
			if(isset($meta['settings']['sharedesc']) & !empty($meta['settings']['sharedesc'])){
				$shareSummary = urlencode($meta['settings']['sharedesc']);
			}else{
				$shareSummary = "";
			}

				
			$contest_content_holder .= '<div class="meta-act">';
				$contest_content_holder .= '<div class="meta-share">';
					$contest_content_holder .= '<a href="#" class="share-button"><span class="share-icons social-share-icon-share"></span>Share</a>';
				$contest_content_holder .= '</div>'; // end meta-share

				$contest_content_holder .= '<div class="share-links">';
					$contest_content_holder .= '<div class="group">';
					
					if(isset($meta['settings']['sstwitter'])):
						$twitterShareJS = "http://twitter.com/intent/tweet?url=".$entryURLencoded."&text=".$shareTitle;

						$dataholder = "data-url='{$twitterShareJS}' data-h='275' data-w='600' data-popname='Tweet'";
						
						$contest_content_holder .= '<a href="javascript:void(0)" ' . $dataholder . ' alt="Tweet" class="share-button-popup share-button-twitter meta-share-wrap">';
								$contest_content_holder .= '<span class="share-icons social-share-icon-twitter" aria-hidden="true"></span><span class="social-count">' . $count['twitter'] . '</span>';
						$contest_content_holder .= '</a>';
					endif; // End twitter


					if(isset($meta['settings']['ssfacebook'])):

						$fbShareJS = "http://www.facebook.com/sharer.php?s=100&amp;";
						$fbShareJS .= "p[title]={$shareTitle}&amp;";
						$fbShareJS .= "p[summary]={$shareSummary}&amp;";
						$fbShareJS .= "p[url]={$entryURLencoded}&amp;";
						$fbShareJS .= "p[images][0]={$shareImage}";

						$dataholder = "data-url='{$fbShareJS}' data-h='436' data-w='626' data-popname='sharer'";

						$contest_content_holder .= '<a href="javascript:void(0)" ' . $dataholder . ' alt="Share on Facebook" class="share-button-popup share-button-fb meta-share-wrap">';
							$contest_content_holder .= '<span class="share-icons social-share-icon-facebook"></span><span class="social-count">' . $count['facebook'] . '</span>';
						$contest_content_holder .= '</a>';
							
					endif; // End facebook
		

					if(isset($meta['settings']['ssgoogleplus'])):

						$gplusShareJS = "https://plus.google.com/share?url={$entryURLencoded}";

						$dataholder = "data-url='{$gplusShareJS}' data-h='500' data-w='700' data-popname='Plus One'";
					
						$contest_content_holder .= '<a href="javascript:void(0)" ' . $dataholder . ' alt="Share on Google+" class="share-button-popup share-button-gp meta-share-wrap">';
							$contest_content_holder .= '<span class="share-icons social-share-icon-google-plus"></span><span class="social-count">' . $count['googleplus'] . '</span>';
						$contest_content_holder .= '</a>';
					endif; // end google plus


					if(isset($meta['settings']['sspinterest'])):
								
						$pinShareJS = "http://pinterest.com/pin/create/button/?url={$entryURLencoded}&media={$shareImage}";

						$dataholder = "data-url='{$pinShareJS}' data-h='270' data-w='630' data-popname='Pinterest'";

						$contest_content_holder .= '<a href="javascript:void(0)" ' . $dataholder . ' alt="Pin it" class="share-button-popup share-button-pin meta-share-wrap">';
							$contest_content_holder .= '<span class="share-icons social-share-icon-pinterest"></span><span class="social-count">' . $count['pinterest'] . '</span>';
						$contest_content_holder .= '</a>';
					endif; // end pinterest
					

					if(isset($meta['settings']['ssshortenedlink'])):
						$contest_content_holder .= '<div class="meta-link" style="float: left;">';
							$contest_content_holder .= '<span class="share-icons social-share-icon-link"></span>';
							$contest_content_holder .= '<input type="url" class="share-form-url" value="' . $shortenedURL . '" readonly="">';
							$contest_content_holder .= '<a class="meta-short-url meta-link-text" href="' . $shortenedURL . '" >' . $shortenedURL . '</a>';
						$contest_content_holder .= '</div>';
					endif; // end shortened link

				$contest_content_holder .= '</div></div></div>';

				return $contest_content_holder;

			endif; // end social share setting

	}

	/**
	 * Entry sort
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 *
	 * @since 2.0.0
	 *
	 * @param array $meta  Contest meta
	 * @return array $meta Sorted meta.
	 */
	function entry_sort($meta) {

		// lets loop through the entries one time to make sure votes are 0 if not set for some reason
		foreach($meta['entries'] as $entry){
			// Votes are 0 if not set
			if(empty($entry['votes']) || !isset($entry['votes'])) $entry['votes'] = 0;
		}

		// Sort highest first if there is entry ribbons set
		if(isset($meta['settings']['a_entry_ribbons'])) $meta['settings']['sort'] = 'high';

		// Sorting the votes
		switch ($meta['settings']['sort']):
			case "high":
				// Sort by votes (higher first)
				$newfunc = create_function('$a,$b', 'return $a["votes"] < $b["votes"];');
				uasort($meta['entries'], $newfunc);
			break;
			case "low":
				// Sort by votes (lower first)
				$newfunc = create_function('$a,$b', 'return $a["votes"] > $b["votes"];');
				uasort($meta['entries'], $newfunc);
			break;
			case "aztitle":
				// Sort title a to z
				$newfunc = create_function('$a,$b', 'return $a["entryTitle"] > $b["entryTitle"];');
				uasort($meta['entries'], $newfunc);
			break;
			case "zatitle":
				// Sort title z to a
				$newfunc = create_function('$a,$b', 'return $a["entryTitle"] < $b["entryTitle"];');
				uasort($meta['entries'], $newfunc);
			break;
			case "azauthor":
				// Sort author a to z
				$newfunc = create_function('$a,$b', 'return $a["author"] > $b["author"];');
				uasort($meta['entries'], $newfunc);
			break;
			case "zaauthor":
				// Sort author z to a
				$newfunc = create_function('$a,$b', 'return $a["author"] < $b["author"];');
				uasort($meta['entries'], $newfunc);
			break;
			case "rand":
				// Random

				// Get the array keys
				$keys = array_keys($meta['entries']);

				// Shuffle the keys
		        shuffle($keys);

		        // Reattach to the entries
		        foreach($keys as $key) {
		            $new[$key] = $meta['entries'][$key];
		        }
		        $meta['entries'] = $new;
			break;
			default:
			// do nothing
			break;
		endswitch;

		// Return our meta
		return $meta;
	}



	/**
	 * Ajax callback for voting
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 *
	 * @since 1.0.0
	 *
	 * @param int $post_id  The current post id.
	 * @return int $votes_number The number of votes.
	 * @throws bool False on error
	 */
	function ajax_callback() {

		// Exit out early if no post data
		if(!isset($_POST)) die();

		 //check to see if the submitted nonce matches with the generated nonce we created earlier
		if( !isset( $_POST['myContestNonce'] ) || !wp_verify_nonce($_POST['myContestNonce'], 'my-contest-nonce') ){
			//Permisssions failed
			header('HTTP/1.1 500 Permission Check Failed');
        	header('Content-Type: application/json');
			die(json_encode(array('message' => 'ERROR', 'code' => __('Permissions check failed', 'mycontest'))));
		}

		// Make sure there is a entryID and postid
		if(!isset($_POST['entry_id']) || !isset($_POST['post_id'])) die();

		//Set our post and entry id
		// $post_id = $_POST['post_id'];
		$post_id = (int) $_POST['post_id'];
		$entryID = $_POST['entry_id'];
		// $entryID = (string) $_POST['entry_id'];

		//Get our post meta
		$meta = get_post_meta($post_id, '_myContest', TRUE);

		//get votes number
		if( !empty($meta) ){
			$votes_number = $meta['entries'][$entryID]['votes'];
		}else{
			header('HTTP/1.1 500 Get Meta Failed');
        	header('Content-Type: application/json');
			die(json_encode(array('message' => 'ERROR', 'code' => __('Get Meta Failed', 'mycontest'))));
		}

		// Get the expiration date and today's date
		// $start_date = $meta['settings']['s_date'].' '.$meta['settings']['s_h'].':'.$meta['settings']['s_mn'];
		// $exp_date = $meta['settings']['e_date'].' '.$meta['settings']['e_h'].':'.$meta['settings']['e_mn'];
		if(isset( $meta['settings']['s_date'] )  & !empty($meta['settings']['s_date'])){
			list($month, $day, $year) = explode('/', $meta['settings']['s_date']);
			// check if hours are set if not set to 00
			if(!isset($meta['settings']['s_h']) || empty($meta['settings']['s_h'])) $meta['settings']['s_h'] = '00';
			// check if minutes are set if not set to 00
			if(!isset($meta['settings']['s_mn']) || empty($meta['settings']['s_mn'])) $meta['settings']['s_mn'] = '00';
			$start_date = mktime( $meta['settings']['s_h'], $meta['settings']['s_mn'], '00', $month, $day, $year );
		}
		if( isset( $meta['settings']['e_date'] ) & !empty($meta['settings']['e_date']) ){
			list($month, $day, $year) = explode('/', $meta['settings']['e_date']);
			// check if hours are set if not set to 00
			if(!isset($meta['settings']['e_h']) || empty($meta['settings']['e_h'])) $meta['settings']['e_h'] = "00";
			// check if minutes are set if not set to 00
			if(!isset($meta['settings']['e_mn']) || empty($meta['settings']['e_mn'])) $meta['settings']['e_mn'] = "00";
			$exp_date = mktime( $meta['settings']['e_h'], $meta['settings']['e_mn'], '00', $month, $day, $year );
		}

		//get todays date
		$todays_date = date("Y-m-d h:i");
		// convert to time
		$todays_date = strtotime($todays_date);

		// compare if past expiration date
		if(isset($exp_date)){
			if ($exp_date < $todays_date) {
				echo $votes_number;
			}
		}


		//get votes number
		// $votes_number = $meta['entries'][$entryID]['votes'];
		
		// if the votes number is set
		if( isset($votes_number) & !empty($votes_number) ){
			$votes_number++; //add one vote
		}else{
			$votes_number = 1; // It must be 1
		}
		$votes_number = (string) $votes_number;

		// Setup to update meta
		$meta['entries'][$entryID]['votes'] = $votes_number;

		// Sleep for testing
		// sleep(10);

		//update our post meta with vote number added
		if( update_post_meta($post_id, '_myContest', $meta) ) {
			// Success!
			header('HTTP/1.1 200 ' . __('Vote successful','mycontest'));
			die($votes_number);
		}else{
			// Error!
			header('HTTP/1.1 500 ' . __('Vote unsuccessful','mycontest'));
			die('ERROR');
		}
		
		die(); // this is required to return a proper result
	}

	/**
	 * Runs only when the plugin is activated
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 *
	 * @since 1.0.0
	 *
	 */
	function install_my_contest() {
		// do not generate any output here

		// First, we "add" the custom post type via the above written function.
	    // Note: "add" is written with quotes, as CPTs don't get added to the DB,
	    // They are only referenced in the post_type column with a post entry, 
	    // when you add a post of this CPT.
	    $this->create_post_type_contest();

	    // ATTENTION: This is *only* done during plugin activation hook in this example!
	    // You should *NEVER EVER* do this on every page load!!
	    flush_rewrite_rules();
	}

	/**
	 * Runs only when the plugin is deactivated
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 *
	 * @since 2.7.1
	 *
	 */
	function uninstall_my_contest() {
	    flush_rewrite_rules();
	}

	/**
	 * Add icon to the plugin header
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 *
	 * @since 1.0.0
	 */
	function plugin_header() {
		?>
	    <style>
			.icon32-posts-<?php echo 'my_contest'; ?> { background:transparent url('<?php echo $this->pluginUrl . "/inc/images/contest-icon32.png";?>') no-repeat !important; }
  		</style>
		<?php
	}

	/**
	 * Registers and enqueues stylesheets for the administration panel
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 *
	 * @since 1.0.0
	 */
	function register_scripts_and_styles_admin() {
		global $post;

		// Only for our pages, not any other pages laying around
		if(isset($post->post_type) && 'my_contest' == $post->post_type):

		wp_enqueue_script( 'jquery' );

		if($this->css_min){
			$this->load_file( 'mycontest-style', '/inc/stylesheets/myContest-admin.min.css' );
		}else{
			$this->load_file( 'mycontest-style', '/inc/stylesheets/myContest-admin.css' );
		}

		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-sortable');

		if($this->js_min){
			$this->load_file( 'mycontest-admin-script', '/inc/javascripts/min/mycontest-admin.min.js', true );
		}else{
			$this->load_file( 'mycontest-admin-script', '/inc/javascripts/mycontest-admin.js', true );	
		}

		// Set js variables through localize
		wp_localize_script( 'myContest-admin-script', 'myContest_helper', array(
			'plugin_url' => $this->pluginUrl
		));

		endif;

	}
  
	/**
	 * Registers and enqueues stylesheets for the public facing site
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 *
	 * @since 1.0.0
	 */
	function register_scripts_and_styles($isShortcode = false, $postid = null) {
		global $post;
		// Only for our pages, not any other pages laying around
		if(is_single() && isset($post->post_type) && 'my_contest' == $post->post_type || $isShortcode):

			// If not from shortcode
			if(!$isShortcode){
				$postid = $post->ID;
			}

			// Get our contest data
			$meta = get_post_meta($postid, '_myContest', TRUE);

			// Make sure jquery is there to use (still only calls one jquery)
			wp_enqueue_script( 'jquery' );

			$customCSS = $this->pluginPath . "/inc/stylesheets/myContest.custom.css";

			// See if we have custom css
			if(file_exists($customCSS)){
				if($this->css_min){
					$this->load_file( 'myContest-style', '/inc/stylesheets/myContest.custom.min.css' );
				}else{
					$this->load_file( 'myContest-style', '/inc/stylesheets/myContest.custom.css' );
				}
			}else{
				if($this->css_min){
				$this->load_file( 'myContest-style', '/inc/stylesheets/myContest.min.css' );
				}else{
					$this->load_file( 'myContest-style', '/inc/stylesheets/myContest.css' );
				}
			}

			if($this->js_min){
				$this->load_file('mycontest-plugins', '/inc/javascripts/min/mycontest-plugins.min.js', true );
				$this->load_file('mycontest', '/inc/javascripts/min/mycontest.min.js', true, true, array('mycontest-plugins') );
			}else{
				$this->load_file('mycontest-plugins', '/inc/javascripts/mycontest-plugins.js', true );
				$this->load_file('mycontest', '/inc/javascripts/mycontest.js', true, true, array('mycontest-plugins') );
			}

			// Get our vote timing ( Number in microseconds between votes (1440 = 1 day) )
			/* & !empty($meta['settings']['votetime']) */
			if(isset($meta['settings']['votetime']) ){
				// Set our time
				$expire = $meta['settings']['votetime'];
			}else{
				// Default (1 day)
				$expire = 1440;
			}
			

			// Set js variables through localize
			wp_localize_script( 'mycontest', 'my_contest', array(
				'ajaxurl' => admin_url('admin-ajax.php'),

				//generate a nonce with a unique ID "myajax-post-comment-nonce"
				// so that you can check it later when an AJAX request is sent
				'myContestNonce' => wp_create_nonce( 'my-contest-nonce' ),

				//Get the post id and add
				// 'post_id' => $post->ID,

				//Number in microseconds between votes (86400000 = 1 day)
				'expire'=>$expire, 

				// Actual localization below
				'alreadyVoted' => __('You have already voted','mycontest'),
				'voteUnsuccessful' => __('Vote unsuccessful. Please Try Again.','mycontest'),
				'votes' => __('votes', 'mycontest'),
				'votedforthis' => 'Voted for this'
			));

		endif;

	}
	/**
	 * Helper function for registering and enqueueing scripts and styles.
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 *
	 * @since 1.0.0
	 *
	 * @param str  $name  		The ID to register with WordPress
	 * @param str  $file_path 	The path to the actual file
	 * @param bool $is_script 	Optional argument for if the incoming file_path is a JavaScript source file.
	 * @param int  $version The version number for caching (defaults to plugin version)
	 */
	private function load_file( $name, $file_path, $is_script = false, $version = true, $depends = false ) {
		if($version) $version = $this->version;

		$url = plugins_url($file_path, __FILE__);
		$file = plugin_dir_path(__FILE__) . $file_path;

		// Setup our dependencies
		$dependencies = array('jquery');
		if($depends !== false) $dependencies = array_merge($dependencies, $depends);

		if( file_exists( $file ) ) {
			if( $is_script ) {
				wp_register_script( $name, $url, $dependencies, $version );
				wp_enqueue_script( $name );
			} else {
				wp_register_style( $name, $url, array(), $version  );
				wp_enqueue_style( $name );
			} // end if
		} // end if

	}

	/**
	 * Admin notices
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 *
	 * @since 1.0.0
	 * @param  str $message 	Message to display for error
	 * @param  bool $error Whether it is a error or warning
	 */
	function add_admin_notice($message = "Unknown myContest error", $error = true){

		$notices = IDAdminNotices::getSingleton();

        $notices->enqueue( $message, ($error ? 'error' : '') );
	}

	/**
	 * Title         : Aqua Resizer
	 * Description   : Resizes WordPress images on the fly
	 * Version       : 1.1.7
	 * Author        : Syamil MJ
	 * Author URI    : http://aquagraphite.com
	 * License       : WTFPL - http://sam.zoy.org/wtfpl/
	 * Documentation : https://github.com/sy4mil/Aqua-Resizer/
	 *
	 * @param string  $url    - (required) must be uploaded using wp media uploader
	 * @param int     $width  - (required)
	 * @param int     $height - (optional)
	 * @param bool    $crop   - (optional) default to soft crop
	 * @param bool    $single - (optional) returns an array if false
	 * @uses  wp_upload_dir()
	 * @uses  image_resize_dimensions() | image_resize()
	 * @uses  wp_get_image_editor()
	 *
	 * @return str|array
	 */
	public function aq_process( $url, $width = null, $height = null, $crop = null, $single = true, $upscale = false ) {
		// Validate inputs.
		if ( ! $url || ( ! $width && ! $height ) ) return false;

		// Caipt'n, ready to hook.
		if ( true === $upscale ) add_filter( 'image_resize_dimensions', array($this, 'aq_upscale'), 10, 6 );

		// Define upload path & dir.
		$upload_info = wp_upload_dir();
		$upload_dir = $upload_info['basedir'];
		$upload_url = $upload_info['baseurl'];
			
		$http_prefix = "http://";
		$https_prefix = "https://";
			
		/* if the $url scheme differs from $upload_url scheme, make them match 
		   if the schemes differe, images don't show up. */
		if(!strncmp($url,$https_prefix,strlen($https_prefix))){ //if url begins with https:// make $upload_url begin with https:// as well
			$upload_url = str_replace($http_prefix,$https_prefix,$upload_url);
		}
		elseif(!strncmp($url,$http_prefix,strlen($http_prefix))){ //if url begins with http:// make $upload_url begin with http:// as well
				$upload_url = str_replace($https_prefix,$http_prefix,$upload_url);		
		}
			

		// Check if $img_url is local.
		if ( false === strpos( $url, $upload_url ) ) return false;

		// Define path of image.
		$rel_path = str_replace( $upload_url, '', $url );
		$img_path = $upload_dir . $rel_path;

		// Check if img path exists, and is an image indeed.
		if ( ! file_exists( $img_path ) or ! getimagesize( $img_path ) ) return false;

		// Get image info.
		$info = pathinfo( $img_path );
		$ext = $info['extension'];
		list( $orig_w, $orig_h ) = getimagesize( $img_path );

		// Get image size after cropping.
		$dims = image_resize_dimensions( $orig_w, $orig_h, $width, $height, $crop );
		$dst_w = $dims[4];
		$dst_h = $dims[5];

		// Return the original image only if it exactly fits the needed measures.
		if ( ! $dims && ( ( ( null === $height && $orig_w == $width ) xor ( null === $width && $orig_h == $height ) ) xor ( $height == $orig_h && $width == $orig_w ) ) ) {
			$img_url = $url;
			$dst_w = $orig_w;
			$dst_h = $orig_h;
		} else {
			
			// Use this to check if cropped image already exists, so we can return that instead.
			$suffix = "{$dst_w}x{$dst_h}";
			$dst_rel_path = str_replace( '.' . $ext, '', $rel_path );
			$destfilename = "{$upload_dir}{$dst_rel_path}-{$suffix}.{$ext}";

			if ( ! $dims || ( true == $crop && false == $upscale && ( $dst_w < $width || $dst_h < $height ) ) ) {
				// Can't resize, so return false saying that the action to do could not be processed as planned.
				return false;
			}
			// Else check if cache exists.
			elseif ( file_exists( $destfilename ) && getimagesize( $destfilename ) ) {
				$img_url = "{$upload_url}{$dst_rel_path}-{$suffix}.{$ext}";
			}
			// Else, we resize the image and return the new resized image url.
			else {
				$resized_img_path = image_resize( $img_path, $width, $height, $crop ); // Fallback foo.
				if ( ! is_wp_error( $resized_img_path ) ) {
					$resized_rel_path = str_replace( $upload_dir, '', $resized_img_path );
					$img_url = $upload_url . $resized_rel_path;
				} else {
					return false;
				}

			}
		}

		// Okay, leave the ship.
		if ( true === $upscale ) remove_filter( 'image_resize_dimensions', array( $this, 'aq_upscale' ) );

		// Return the output.
		if ( $single ) {
			// str return.
			$image = $img_url;
		} else {
			// array return.
			$image = array (
				0 => $img_url,
				1 => $dst_w,
				2 => $dst_h
			);
		}

		return $image;
	} // end aq_process

	/**
	 * Callback to overwrite WP computing of thumbnail measures
	 */
	function aq_upscale( $default, $orig_w, $orig_h, $dest_w, $dest_h, $crop ) {
		if ( ! $crop ) return null; // Let the wordpress default function handle this.

		// Here is the point we allow to use larger image size than the original one.
		$aspect_ratio = $orig_w / $orig_h;
		$new_w = $dest_w;
		$new_h = $dest_h;

		if ( ! $new_w ) {
			$new_w = intval( $new_h * $aspect_ratio );
		}

		if ( ! $new_h ) {
			$new_h = intval( $new_w / $aspect_ratio );
		}

		$size_ratio = max( $new_w / $orig_w, $new_h / $orig_h );

		$crop_w = round( $new_w / $size_ratio );
		$crop_h = round( $new_h / $size_ratio );

		$s_x = floor( ( $orig_w - $crop_w ) / 2 );
		$s_y = floor( ( $orig_h - $crop_h ) / 2 );

		return array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );
	} // end aq_upscale

	
  
} // end class

/**
 * Init mycontest class
 */
$mycontest = new myContest();

} // class_exists check

?>