<?php
/**
 * @author    WP-Cloud <code@wp-cloud.org>
 * @copyright Copyright (c) 2014, WP-Cloud
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPL-2.0+
 * @package   WPC\Disable_Updates
 * @version   0.0.1
 */
/*
Plugin Name: Disable Updates
Description: Set the right environment for WP in our cloud
Version:     0.0.1
Author:      WP-Cloud
Author URI:  https://www.wp-cloud.org
License:     GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Network:     true

    Disable Updates
    Copyright (C) 2014 WP-Cloud (http://www.wp-cloud.org)

    This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace WPC;

//if ( defined( 'WPC_DEV' ) || WPC_DEV ) {
//	return;
//}

/**
 * Deactivate all (automatic) update functionality
 *
 * @since 0.0.1
 */
class Disable_Updates {

	/**
	 * wp-config.php
	 * define( 'AUTOMATIC_UPDATER_DISABLED', true );
	 * define( 'WP_AUTO_UPDATE_CORE', false );
	 */

	/**
	 * Holds a copy of the class object
	 *
	 * @since 0.0.1
	 * @var   object $instance
	 */
	protected static $_instance = null;

	/**
	 * Return the Instance object
	 *
	 * @since  0.0.1
	 * @return object
	 */
	public static function get_instance() {
		
		if ( !isset( self::$_instance ) && !( self::$_instance instanceof \WPC\Disable_Updates ) ) {

			self::$_instance = new \WPC\Disable_Updates;
			self::$_instance->includes();

			// Setup objects
			self::$_instance->gf = new \WPC\Disable_Updates\Gravity_Forms;

		}

		return self::$_instance;

	} // END get_instance()
	
	/**
	 * Throw error on object clone
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function __clone() {

		// Cloning instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0' );
		
	} // END __clone()

	/**
	 * Disable unserializing of the class
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function __wakeup() {
		
		// Unserializing instances of the class is forbidden
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0' );
		
	} // END __wakeup()

	/**
	 * Constructor. Hooks all interactions to initialize the class.
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function __construct() {

		$this->hide_ui_elements();

		$this->disable_core_updates();
		$this->disable_plugin_updates();
		$this->disable_theme_updates();
		$this->disable_language_updates();

	} // END __construct()
	
	/**
	 * Include extensions for custom/non-wp.org plugins/themes
	 *
	 * @since  0.0.1
	 * @return void
	 */
	private function includes() {

		require_once 'ext/gravity-forms.php';

	} // END includes()

	/**
	 * Hook removal of UI elements indicating available updates
	 *
	 * @since  0.0.1
	 * @uses   add_action()
	 * @return void
	 */
	private function hide_ui_elements() {

		add_action( 'admin_head',                 array( $this, 'remove_update_notices' ) );
		add_action( 'network_admin_menu',         array( $this, 'remove_update_page' ) );
		add_action( 'wp_before_admin_bar_render', array( $this, 'remove_admin_bar_notification' ) );

	} // END hide_ui_elements()

	/**
	 * Remove admin notices
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function remove_update_notices() {

		remove_action( 'admin_notices', 'update_nag', 3 );
		remove_action( 'admin_notices', 'maintenance_nag' );

	} // END remove_update_notices()

	/**
	 * Remove Admin Bar indicator for available updates
	 * 
	 * @since  0.0.1
	 * @global type $wp_admin_bar
	 * @return void
	 */
	public function remove_admin_bar_notification() {

		global $wp_admin_bar;

		$wp_admin_bar->remove_menu('updates');

	} // END remove_admin_bar_notification()

	public function remove_update_page() {

		// remove_submenu_page( 'index.php', 'update-core.php' ); // WP Single
		// remove_submenu_page( 'update-core.php', 'update-core.php' );

	} // END remove_update_page()

	/**
	 * Disable/Block all (automatic) updates and checks for core versions
	 *
	 * @since  0.0.1
	 * @see    http://codex.wordpress.org/Disabling_Automatic_Background_Updates
	 * @return void
	 */
	private function disable_core_updates() {

		add_filter( 'auto_update_core',           '__return_false' );
		add_filter( 'automatic_updater_disabled', '__return_true' );

		// Unhook core version checks
		remove_action( 'admin_init',                '_maybe_update_core' );
		remove_action( 'wp_maybe_auto_update',      'wp_maybe_auto_update' );
		remove_action( 'wp_version_check',          'wp_version_check' );
		remove_action( 'upgrader_process_complete', 'wp_version_check', 10, 0 );

		// Simulate Transient returning "up-to-date"
		add_filter( 'pre_site_transient_update_core', array( $this, '__return_empty_update' ) );

		// Prevent update cron creation + remove potential existing crons
		remove_action( 'init', 'wp_schedule_update_checks' );
		wp_clear_scheduled_hook( 'wp_version_check' );
		wp_clear_scheduled_hook( 'wp_maybe_auto_update' );

		// Disable email.
		// add_filter( 'auto_core_update_send_email', '__return_false' );

	} // END disable_core_updates()

	private function disable_plugin_updates() {

		add_filter( 'auto_update_plugin',        '__return_false' );
		add_filter( 'pre_option_update_plugins', '__return_null' );

		// Unhook plugin version checks
		remove_action( 'admin_init',                '_maybe_update_plugins' );
		remove_action( 'load-plugins.php',          'wp_update_plugins' );
		remove_action( 'load-update-core.php',      'wp_update_plugins' );
		remove_action( 'load-update.php',           'wp_update_plugins' );
		remove_action( 'upgrader_process_complete', 'wp_update_plugins', 10, 0 );
		remove_action( 'wp_update_plugins',         'wp_update_plugins' );

		// Simulate Transient returning "up-to-date"
		add_filter( 'pre_site_transient_update_plugins', array( $this, '__return_empty_update' ) );

		// Prevent update cron creation + remove potential existing crons
		wp_clear_scheduled_hook( 'wp_update_plugins' );

	} // END disable_plugin_updates()

	private function disable_theme_updates() {

		add_filter( 'auto_update_theme', '__return_false' );

		// Unhook theme version checks
		remove_action( 'admin_init',                '_maybe_update_themes' );
		remove_action( 'load-themes.php',           'wp_update_themes' );
		remove_action( 'load-update-core.php',      'wp_update_themes' );
		remove_action( 'load-update.php',           'wp_update_themes' );
		remove_action( 'upgrader_process_complete', 'wp_update_themes', 10, 0 );
		remove_action( 'wp_update_themes',          'wp_update_themes' );

		// Simulate Transient returning "up-to-date"
		add_filter( 'pre_site_transient_update_themes', array( $this, '__return_empty_update' ) );

		// @todo
		// add_filter( 'site_transient_update_themes', '__return_null' );

		// Prevent update cron creation + remove potential existing crons
		wp_clear_scheduled_hook( 'wp_update_themes' );

	} // END disable_theme_updates()

	private function disable_language_updates() {

		add_filter( 'auto_update_translation', '__return_false' );

	} // END disable_language_updates()

	/**
	 * Return object suggesting everything is 'up-to-date'
	 *
	 * @since  0.0.1
	 * @return object
	 */
	public function __return_empty_update() {

		global $wp_version;

		return (object) array(
			'updates'         => array(),
			'version_checked' => $wp_version,
			'last_checked'    => time(),
		);

	} // END __return_empty_update()

} // class Disable_Updates

/**
 * Returns the main instance object
 *
 * @since  0.0.1
 * @return object Disable_Updates
 */
function Disable_Updates() {
	return \WPC\Disable_Updates::get_instance();
}

Disable_Updates();
