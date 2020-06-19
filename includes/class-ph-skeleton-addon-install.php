<?php
/**
 * Installation related functions and actions.
 *
 * @author 		PropertyHive
 * @category 	Admin
 * @package 	PropertyHive/Classes
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'PH_Skeleton_Addon_Install' ) ) :

class PH_Skeleton_Addon_Install {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		register_activation_hook( PH_SKELETON_ADDON_PLUGIN_FILE, array( $this, 'install' ) );
		register_deactivation_hook( PH_SKELETON_ADDON_PLUGIN_FILE, array( $this, 'deactivate' ) );
		register_uninstall_hook( PH_SKELETON_ADDON_PLUGIN_FILE, array( 'PH_Skeleton_Addon_Install', 'uninstall' ) );

		add_action( 'admin_init', array( $this, 'check_version' ), 5 );
	}

	/**
	 * check_version function.
	 *
	 * @access public
	 * @return void
	 */
	public function check_version() {
	    if ( 
	    	! defined( 'IFRAME_REQUEST' ) && 
	    	( get_option( 'propertyhive_skeleton_addon_version' ) != PH_SKELETON_ADDON()->version ) 
	    ) {
			$this->install();
		}
	}

	/**
	 * Deactivate Add On
	 */
	public function deactivate() {

		// Remove cron (if applicable)
		$timestamp = wp_next_scheduled( 'propertyhive_skeleton_addon_cron_hook' );
        wp_unschedule_event($timestamp, 'propertyhive_skeleton_addon_cron_hook' );
        wp_clear_scheduled_hook('propertyhive_skeleton_addon_cron_hook');

	}

	/**
	 * Uninstall Add On
	 */
	public function uninstall() {

		// Remove cron (if applicable)
		$timestamp = wp_next_scheduled( 'propertyhive_skeleton_addon_cron_hook' );
        wp_unschedule_event($timestamp, 'propertyhive_skeleton_addon_cron_hook' );
        wp_clear_scheduled_hook('propertyhive_skeleton_addon_cron_hook');

        // Delete option that stores all the add on related settings
        delete_option( 'propertyhive_skeleton_addon' );

	}

	/**
	 * Install Add On
	 */
	public function install() {
        
		$this->create_cron();

        update_option( 'propertyhive_skeleton_addon_version', PH_SKELETON_ADDON()->version );
	}

	/**
	 * Setup cron
	 *
	 * @access public
	 */
	public function create_cron() {
	    
	    // Create cron (if applicable)
        $timestamp = wp_next_scheduled( 'propertyhive_skeleton_addon_cron_hook' );
        wp_unschedule_event($timestamp, 'propertyhive_skeleton_addon_cron_hook' );
        wp_clear_scheduled_hook('propertyhive_skeleton_addon_cron_hook');
        
        $next_schedule = time() - 60;
        wp_schedule_event( $next_schedule, 'hourly', 'propertyhive_skeleton_addon_cron_hook' );

    }

}

endif;

return new PH_Skeleton_Addon_Install();