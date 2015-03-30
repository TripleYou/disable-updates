<?php
/**
 * @author    WP-Cloud <code@wp-cloud.org>
 * @copyright Copyright (c) 2014-2015, WP-Cloud
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPL-2.0+
 * @package   WPC\DisableUpdates
 * @version   0.0.1
 */

namespace WPC\DisableUpdates;

/**
 * Deactivate update and extension functions of Gravity Forms
 *
 * @since 0.0.1
 */
class Core {

	/**
	 * Constructor. Hooks all interactions to initialize the class.
	 *
	 * @since 0.0.1
	 */
	public function __construct() {

		$this->hide_ui_elements();
		$this->disable_core_updates();
		$this->disable_language_updates();

	} // END __construct()

	/**
	 * Disable/Block all (automatic) updates and checks for core versions
	 *
	 * @see    http://codex.wordpress.org/Disabling_Automatic_Background_Updates
	 * @since  0.0.1
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
		add_filter( 'pre_site_transient_update_core', array( '\\WPC\\DisableUpdates', 'return_empty_update' ) );

		// Prevent update cron creation + remove potential existing crons
		remove_action( 'init', 'wp_schedule_update_checks' );
		wp_clear_scheduled_hook( 'wp_version_check' );
		wp_clear_scheduled_hook( 'wp_maybe_auto_update' );

		// Disable email.
		// add_filter( 'auto_core_update_send_email', '__return_false' );

	} // END disable_core_updates()

	/**
	 * Hook removal of UI elements indicating available updates
	 *
	 * @since  0.0.1
	 * @uses   add_action()
	 * @return void
	 */
	private function hide_ui_elements() {

		add_action( 'admin_head',                 array( $this, 'remove_update_notices'         ) );
		add_action( 'network_admin_menu',         array( $this, 'remove_update_page'            ) );
		add_action( 'wp_before_admin_bar_render', array( $this, 'remove_admin_bar_notification' ) );

	} // END hide_ui_elements()

	/**
	 * Remove admin notices
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function remove_update_notices() {

		remove_action( 'admin_notices', 'update_nag', 3   );
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

	/**
	 * @todo desc
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function remove_update_page() {

		remove_submenu_page( 'index.php',       'update-core.php' ); // WP Single
		remove_submenu_page( 'update-core.php', 'update-core.php' );

	} // END remove_update_page()

	/**
	 * @todo desc
	 *
	 * @since  0.0.1
	 * @return void
	 */
	private function disable_language_updates() {
		add_filter( 'auto_update_translation', '__return_false' );
	} // END disable_language_updates()

} // class Core

new Core();
