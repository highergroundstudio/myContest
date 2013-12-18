<?php
if( !class_exists( 'myContest_updateCheck' ) ):
class myContest_updateCheck
{
    /**
     * The plugin current version
     * @var string
     */
    private $current_version;

    /**
     * The plugin remote update path
     * @var string
     */
    private $update_path;

    /**
     * Plugin Slug (plugin_directory/plugin_file.php)
     * @var string
     */
    private $plugin_slug;

    /**
     * Plugin name (plugin_file)
     * @var string
     */
    private $slug;
    /**
     * Envato Purchase code
     * @var string
     */
    private $purchase_code;
    /**
     * Beta tester
     * @var bool
     */
    private $beta_tester;

    /**
     *  Hook only to the second check
     *  @var bool
     */
    var $steady = true;



    /**
     * Initialize a new instance of the WordPress Auto-Update class
     * @param string $current_version
     * @param string $update_path
     * @param string $plugin_slug
     */
    public function __construct($current_version, $update_path, $plugin_slug, $purchase_code, $beta_tester)
    {
        // Set the class public variables
        $this->current_version = $current_version;

        $this->purchase_code = $purchase_code;

        $this->beta_tester = $beta_tester;

        $this->update_path = $update_path;

        // Grab and slip our slug to get the base slug
        $this->plugin_slug = $plugin_slug;
        list ($t1, $t2) = explode('/', $plugin_slug);
        $this->slug = str_replace('.php', '', $t2);

        // define the alternative API for updating checking
        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_update'));

        // Define the alternative response for information checking
        add_filter('plugins_api', array($this, 'check_info'), 10, 3);

        // If there is no purchase code show the warning
        if(empty($this->purchase_code)):
            // Make sure we are in the update-core page to show purchase_code
            if(strpos($_SERVER['REQUEST_URI'],'update-core.php') !== false):
                $errorstyles = "padding: 8px 35px 8px 14px;margin-bottom: 20px;text-shadow: 0 1px 0 rgba(255, 255, 255, 0.5);background-color: #fcf8e3;border: 1px solid #fbeed5;-webkit-border-radius: 4px;-moz-border-radius: 4px;border-radius: 4px;color: #c09853;margin-bottom: 5px;";
                $errormessage = "<p style='{$errorstyles}'>" . "<strong>" . _('Warning!') . '&nbsp;' . "</strong>" . __('You must add your <a href="http://highergroundstudio.github.io/myContest/#!/receive_updates">Envato purchase code</a> to update myContest automatically.', 'mycontest') . '</p>';
                $error = new WP_Error('add_purchase_code', $errormessage);
                if ( is_wp_error($error) ) echo $error->get_error_message();
            endif;
        endif;
            
    }

    /**
     * Add our self-hosted autoupdate plugin to the filter transient
     *
     * @param $transient
     * @return object $ transient
     */
    public function check_update($transient)
    {
        // Make sure there is nothing there and is the first check
        if (!isset($transient->response) || !$this->steady) {
            $this->steady = true;
            return $transient;
        }
    
        // Don't need to go further if there is nothing to work with
        if (empty($transient->checked)) return $transient;

        // Get the remote version
        $remote_version = $this->getRemote_version();

        // Make suer the new version is there
        if(!isset($remote_version->new_version)) return $transient;

        // If a newer version is available, add the update
        if (version_compare($this->current_version, $remote_version->new_version, '<')) {
            $obj = new stdClass();
            $obj->slug = $this->slug;
            $obj->plugin_name = 'myContest.php';
            $obj->new_version = $remote_version->new_version;
            $obj->url = $remote_version->url;
            $obj->package = $remote_version->package;
            $transient->response[$this->plugin_slug] = $obj;
            return $transient;
        }
        // Just in case
        return $transient;
    }

    /**
     * Add our self-hosted description to the filter
     *
     * @param boolean $false
     * @param array $action
     * @param object $arg
     * @return bool|object
     */
    public function check_info($false, $action, $arg)
    {
        // No need to go further if nothing is there
        if(!isset($arg->slug)) return $false;

        // Make sure we are working with our slug
        if ($arg->slug === $this->slug) {
            // Add our information
            $information = new stdClass();
            $information = $this->getRemote_information();
            $information->slug = $this->slug;
            return $information;
        }
        return $false;
    }

    /**
     * Return the remote version
     * @return string $remote_version
     */
    private function getRemote_version()
    { 
        $request = wp_remote_post($this->update_path, array('body' => array('action' => 'version', 'p_code' => $this->purchase_code, 'beta_tester' => $this->beta_tester)));
        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
            return unserialize( $request['body'] );
            // return json_decode( $request['body'] );
        }
        return false;
    }

    /**
     * Get information about the remote version
     * @return bool|object
     */
    private function getRemote_information()
    {
        $request = wp_remote_post($this->update_path, array('body' => array('action' => 'info', 'p_code' => $this->purchase_code, 'beta_tester' => $this->beta_tester)));
        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
            return unserialize( $request['body'] );
            // return json_decode( $request['body'] );
        }
        return false;
    }

    /**
     * Return the status of the plugin licensing
     * @return boolean $remote_license
     */
    private function getRemote_license()
    {
        $request = wp_remote_post($this->update_path, array('body' => array('action' => 'license', 'p_code' => $this->purchase_code, 'beta_tester' => $this->beta_tester)));
        if (!is_wp_error($request) || wp_remote_retrieve_response_code($request) === 200) {
            // return json_decode($request['body']);
            return unserialize($request['body']);
        }
        return false;
    }
}
endif;