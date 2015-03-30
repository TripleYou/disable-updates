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
class Plugins {

	/**
	 * Constructor. Hooks all interactions to initialize the class.
	 *
	 * @since 0.0.1
	 */
	public function __construct() {

		add_filter( 'auto_update_plugin',        '__return_false' );
		add_filter( 'pre_option_update_plugins', '__return_null' );

		// Unhook plugin version checks
		remove_action( 'load-plugins.php',          'wp_update_plugins' );
		remove_action( 'load-update-core.php',      'wp_update_plugins' );
		remove_action( 'load-update.php',           'wp_update_plugins' );
		remove_action( 'wp_update_plugins',         'wp_update_plugins' );
		remove_action( 'admin_init',                '_maybe_update_plugins' );
		remove_action( 'upgrader_process_complete', 'wp_update_plugins', 10, 0 );

		// Simulate Transient returning "up-to-date"
		add_filter( 'pre_site_transient_update_plugins', array( '\\WPC\\DisableUpdates', '__return_empty_update' ) );

		// Prevent update cron creation + remove potential existing crons
		wp_clear_scheduled_hook( 'wp_update_plugins' );

	} // END __construct()

} // class Plugins

new Plugins();
