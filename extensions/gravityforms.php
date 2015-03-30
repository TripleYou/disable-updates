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
class GravityForms {

	/**
	 * Constructor. Hooks all interactions to initialize the class.
	 *
	 * @since 0.0.1
	 * @uses  add_action()
	 */
	public function __construct() {

		add_action( 'init',       array( $this, 'remove_update_check' ), 11 );
		add_action( 'admin_menu', array( $this, 'remove_admin_menus' ), 999 );

	} // END __construct()

	/**
	 * Remove Gravity Forms Update Check
	 *
	 * @uses  remove_action()
	 * @uses  remove_filter()
	 *
	 * @since  0.0.1
	 * @return void
	 */
	public function remove_update_check() {

		// Gravity Forms Core
		remove_action( 'after_plugin_row_gravityforms/gravityforms.php', array( 'RGForms', 'plugin_row' ) );
		remove_filter( 'transient_update_plugins',      array( 'RGForms', 'check_update' ) );
		remove_filter( 'site_transient_update_plugins', array( 'RGForms', 'check_update' ) );
		remove_filter( 'transient_update_plugins',      array( 'GFForms', 'check_update' ) );
		remove_filter( 'site_transient_update_plugins', array( 'GFForms', 'check_update' ) );

		// Zapier Addon
		remove_action( 'after_plugin_row_gravityformszapier/zapier.php', array( 'GFZapier', 'plugin_row' ) );
		remove_filter( 'transient_update_plugins',      array( 'GFZapier', 'check_update' ) );
		remove_filter( 'site_transient_update_plugins', array( 'GFZapier', 'check_update' ) );

		// Twilio Addon
		remove_action( 'after_plugin_row_gravityformstwilio/twilio.php', array( 'GFTwilio', 'plugin_row' ) );

		// Survey Addon
		remove_action( 'after_plugin_row_gravityformssurvey/survey.php', array( 'GFSurvey', 'plugin_row' ) );

		// Quiz Addon
		remove_action( 'after_plugin_row_gravityformsquiz/quiz.php',     array( 'GFQuiz', 'plugin_row' ) );

		// Polls Addon
		remove_action( 'after_plugin_row_gravityformspolls/polls.php',   array( 'GFPolls', 'plugin_row' ) );

		// User Registration Addon
		remove_action( 'after_plugin_row_gravityformsuserregistration/userregistration.php', array( 'GFUser', 'plugin_row' ) );

	} // END gform_remove_update_check()

	/**
	 * Remove admin pages for updates and extensions
	 *
	 * @since 0.0.1
	 * @uses  remove_submenu_page()
	 *
	 * @return void
	 */
	public function remove_admin_menus() {

		/** Remove Gravity Forms Update page */
		remove_submenu_page( 'gf_edit_forms', 'gf_update' );
		remove_submenu_page( 'gf_new_form',   'gf_update' );
		remove_submenu_page( 'gf_entries',    'gf_update' );

		/** Remove Gravity Forms Addons page */
		remove_submenu_page( 'gf_edit_forms', 'gf_addons' );
		remove_submenu_page( 'gf_new_form',   'gf_addons' );
		remove_submenu_page( 'gf_entries',    'gf_addons' );

		/** Remove Gravity Forms Addons page
		 * @todo Users can activate the help if they have their own GF license
		 */
		remove_submenu_page( 'gf_edit_forms', 'gf_help' );
		remove_submenu_page( 'gf_new_form',   'gf_help' );
		remove_submenu_page( 'gf_entries',    'gf_help' );

	} // END remove_admin_menus()

} // class GravityForms

new GravityForms();
