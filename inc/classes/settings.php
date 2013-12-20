<?php
/**
 * myContest Settings Class
 *
 * Contains the main functions for myContests Settings
 *
 * @class myContest_settings
 * @since 1.2.8
 * @package	myContest
 * @author Higher Ground Studio
 */
if( !class_exists( 'myContest_settings' ) ):
class myContest_settings{

	var $settings;

	/**
	 * Add the settings page
	 * Called in myContest class
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 * @since 1.2.8
	 * @return void
	 */
	function add_settings_page()
	{
		add_submenu_page($parent_slug = 'edit.php?post_type=my_contest', $page_title = 'myContest Settings', $menu_title = 'Settings', $capability='manage_options', $menu_slug='my_contest_settings', $function = array($this,'settings_render'));
	}
	/**
	 * Initialization function
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 * @since 1.2.8
	 * @return void
	 */
	function settings_init()
	{

		$this->settings = get_option('mycontest_settings');
		register_setting( 'mycontest_settings', 'mycontest_settings', array($this,'mycontest_settings_validate') );

		// General Section
		$sectionid = 'mycontest_settings_general';
		add_settings_section('mycontest_general_settings', __('General Settings','mycontest'), array($this,'mycontest_general_section_text'), $sectionid);
		add_settings_field('mc_css_min', __('Disable minified CSS files?','mycontest'), array($this,'minify_css_render'), $sectionid, 'mycontest_general_settings');
		add_settings_field('mc_js_min', __('Disable minified JS files?','mycontest'), array($this,'minify_js_render'), $sectionid, 'mycontest_general_settings');
		add_settings_field('mc_debug_on', __('Enable debug?','mycontest'), array($this,'debug_on_render'), $sectionid, 'mycontest_general_settings');
		add_settings_field('mc_shortcode_support', __('Disable Shortcode?','mycontest'), array($this,'shortcode_support_render'), $sectionid, 'mycontest_general_settings');
		add_settings_field('mc_query_support', __('Disable Add to Query (will not show in the loop)?','mycontest'), array($this,'query_support_render'), $sectionid, 'mycontest_general_settings');
		add_settings_field('mc_mycontest_powered', __('Disable Powered by myContest icon?','mycontest'), array($this,'mycontest_powered_render'), $sectionid, 'mycontest_general_settings');
	}
	/**
	 * Settings page render
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 * @since 1.2.8
	 * @return void
	 */
	function settings_render(){
?>
		<div class="wrap">
			<div id="icon-edit" class="icon32 icon32-posts-my_contest"><br></div>
			<aside>
				<h2>myContest Settings</h2>
				<p>Global settings for myContest</p>
			</aside>
			<form action="options.php" method="post">
			<?php settings_fields('mycontest_settings'); ?>
			<?php do_settings_sections('mycontest_settings_general'); ?>
			<?php do_settings_sections('mycontest_settings_updates'); ?>
			 <br/>
			<input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" />
			</form>
		</div>
<?php
		// If debug is on then show the settings array
		if(!empty($this->settings['debug_on'])){
			?><h3>Settings Array Debug</h3><pre><?php
			print_r($this->settings);
			?></pre><?php
		}
	}
	/**
	 * Settings validation function
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 * @since 1.2.8
	 * @return void
	 */
	function mycontest_settings_validate($input) {
		// Go through all of our settings and validate
		foreach($input as $field => $value){
		// Check what type of field and sanitize accordingly
			switch ($input):
				case "html_allowed":
					// sanitize textarea, allow html
					$sanitized_field = wp_kses_post( $value );
					break;
				// Most goes through here, specific above
				default:
					$sanitized_field = sanitize_text_field( $value );
					break;
			endswitch;
			// Set sanitized field
			$input[$field] = $sanitized_field;
		}
		// Return to be saved
		return $input;
	}
	/**
	 * Description for general section
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 * @since 1.2.8
	 * @return void
	 */
	function mycontest_general_section_text(){
		
	}
	/**
	 * Field render function
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 * @since 1.2.8
	 * @return void
	 */
	function minify_css_render($args){
		?>
			<input id="minify_css" name="mycontest_settings[minify_css]" type="checkbox" value="1" 
			<?php if(!empty($this->settings['minify_css'])) checked( $this->settings['minify_css'] ); ?>>
		<?php
	}
	/**
	 * Field render function
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 * @since 1.2.8
	 * @return void
	 */
	function minify_js_render(){
		?>
			<input id="minify_js" name="mycontest_settings[minify_js]" type="checkbox" value="1" 
			<?php if(!empty($this->settings['minify_js'])) checked( $this->settings['minify_js'] ); ?>>
		<?php
	}
	/**
	 * Field render function
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 * @since 1.2.8
	 * @return void
	 */
	function debug_on_render(){
		?>
			<input id="debug_on" name="mycontest_settings[debug_on]" type="checkbox" value="1" 
			<?php if(!empty($this->settings['debug_on'])) checked( $this->settings['debug_on'] ); ?>>
		<?php
	}
	/**
	 * Field render function
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 * @since 1.2.8
	 * @return void
	 */
	function shortcode_support_render(){
		?>
			<input id="shortcode_support" name="mycontest_settings[shortcode_support]" type="checkbox" value="1" 
			<?php if(!empty($this->settings['shortcode_support'])) checked( $this->settings['shortcode_support'] ); ?>>
		<?php
	}
	/**
	 * Field render function
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 * @since 1.2.8
	 * @return void
	 */
	function query_support_render(){
		// Disable Add to query
		?>
			<input id="query_support" name="mycontest_settings[query_support]" type="checkbox" value="1" 
			<?php if(!empty($this->settings['query_support'])) checked( $this->settings['query_support'] ); ?>>
		<?php
	}	
	/**
	 * Field render function
	 *
	 * @author  Kyle King <kyle.king@highergroundstudio.com>
	 * @since 1.2.8
	 * @return void
	 */
	function mycontest_powered_render(){
		// Disable Powered by myContest
		?>
			<input id="mycontest_powered" name="mycontest_settings[mycontest_powered]" type="checkbox" value="1" 
			<?php if(!empty($this->settings['mycontest_powered'])) checked( $this->settings['mycontest_powered'] ); ?>>
		<?php
	}
} // end class
endif; // endif class doesn't exist

?>