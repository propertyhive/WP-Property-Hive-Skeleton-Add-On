<?php
/**
 * Plugin Name: Property Hive Skeleton Add On
 * Plugin Uri: http://add-on-url.com/
 * Description: Add on description
 * Version: 1.0.0
 * Author: PropertyHive
 * Author URI: http://wp-property-hive.com
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'PH_Skeleton_Addon' ) ) :

final class PH_Skeleton_Addon {

    /**
     * @var string
     */
    public $version = '1.0.0';

    /**
     * @var Property Hive The single instance of the class
     */
    protected static $_instance = null;
    
    /**
     * Main Instance
     *
     * Ensures only one instance of add on is loaded or can be loaded.
     *
     * @static
     * @return Main instance
     */
    public static function instance() 
    {
        if ( is_null( self::$_instance ) ) 
        {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor.
     */
    public function __construct() {

    	$this->id    = 'skeletonaddon';
        $this->label = __( 'Skeleton Addon', 'propertyhive' );

        // Define constants
        $this->define_constants();

        // Include required files
        $this->includes(); 

        add_action( 'admin_notices', array( $this, 'error_notices') );

        add_filter( "plugin_action_links_" . plugin_basename( __FILE__ ), array( $this, 'add_settings_link_to_plugins_page' ) );
        add_filter( 'propertyhive_settings_tabs_array', array( $this, 'add_settings_tab' ), 19 );
        add_action( 'propertyhive_settings_' . $this->id, array( $this, 'output' ) );
        add_action( 'propertyhive_settings_save_' . $this->id, array( $this, 'save' ) );

        add_action( 'propertyhive_skeleton_addon_cron_hook', array( $this, 'skeleton_addon_cron_hook' ) );
    }

    private function includes()
    {
        include_once( 'includes/class-ph-skeleton-addon-install.php' );
    }

    /**
     * Define Constants
     */
    private function define_constants() 
    {
        define( 'PH_SKELETON_ADDON_PLUGIN_FILE', __FILE__ );
        define( 'PH_SKELETON_ADDON_VERSION', $this->version );
    }

    /**
     * Method executed if a cron job is required
     */
    public function skeleton_addon_cron_hook() 
    {
        // Do cron/automated tasks here
    }

    /**
     * Output error message if core Property Hive plugin isn't active
     */
    public function error_notices() 
    {
        if (!is_plugin_active('propertyhive/propertyhive.php'))
        {
            $message = "The Property Hive plugin must be installed and activated before you can use the Property Hive Skeleton Addon";
            echo"<div class=\"error\"> <p>$message</p></div>";
        }
    }

    /**
     * Add a new settings link to the Plugins page.
     */
    public function add_settings_link_to_plugins_page( $links )
    {
        $settings_link = '<a href="' . admin_url('admin.php?page=ph-settings&tab=' . $this->id) . '">' . __( 'Settings', 'propertyhive' ) . '</a>';
        array_push( $links, $settings_link );
        return $links;
    }

    /**
     * Add a new settings tab to the Property Hive settings tabs array.
     */
    public function add_settings_tab( $settings_tabs ) {
        $settings_tabs[$this->id] = $this->label;
        return $settings_tabs;
    }

    /**
     * Uses the Property Hive options API to save settings.
     *
     * @uses propertyhive_update_options()
     * @uses self::get_settings()
     */
    public function save() {
        
        $existing_option = get_option( 'propertyhive_skeleton_addon', array() );

        $new_option = array(
            'my_first_setting' => ( isset($_POST['my_first_setting']) ? sanitize_text_field($_POST['my_first_setting']) : '' ),
        );

        $new_option = array_merge( $existing_option, $new_option );

        update_option( 'propertyhive_skeleton_addon', $new_option );

    }

    /**
     * Uses the Property Hive admin fields API to output settings.
     *
     * @uses propertyhive_admin_fields()
     * @uses self::get_settings()
     */
    public function output() {

    	global $current_section;
        
        propertyhive_admin_fields( self::get_skeleton_addon_settings() );
	}

	/**
     * Get all the main settings for this plugin
     *
     * @return array Array of settings
     */
	public function get_skeleton_addon_settings() {

        $current_settings = get_option( 'propertyhive_skeleton_addon', array() );

        $settings = array(

            array( 'title' => __( 'Skeleton Addon Settings', 'propertyhive' ), 'type' => 'title', 'desc' => '', 'id' => 'skeleton_addon_settings' )

        );

        $settings[] = array(
            'title'     => __( 'My First Setting', 'propertyhive' ),
            'id'        => 'my_first_setting',
            'type'      => 'radio', // text, number, select etc. See output_fields() method of /includes/admin/class-ph-admin-settings.php in core Property Hive plugin
            'default'   => ( isset($current_settings['my_first_setting']) ? $current_settings['my_first_setting'] : ''),
            'options'   => array(
                'option1' => __( 'Option One', 'propertyhive' ),
                'option2' => __( 'Option Two', 'propertyhive' ),
            ),
        );

        $settings[] = array( 'type' => 'sectionend', 'id' => 'skeleton_addon_settings');

	    return apply_filters( 'ph_skeleton_addon_settings', $settings );
	}
}

endif;

/**
 * Returns the main instance of PH_Skeleton_Addon to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return PH_Skeleton_Addon
 */
function PH_SKELETON_ADDON() {
    return PH_Skeleton_Addon::instance();
}

PH_SKELETON_ADDON();