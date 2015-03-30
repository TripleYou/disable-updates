<?php
/**
 * @author    WP-Cloud <code@wp-cloud.org>
 * @copyright Copyright (c) 2014-2015, WP-Cloud
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPL-2.0+
 * @package   WPC\DisableUpdates
 */

namespace WPC\DisableUpdates;

/**
 * Deactivate auto updates for themes from wordpress.org
 *
 * @since 0.0.1
 */
class Themes {

	/**
	 * Constructor. Hooks all interactions to initialize the class.
	 *
	 * @since 0.0.1
	 */
	public function __construct() {

		add_filter( 'auto_update_theme', '__return_false' );

		// Unhook theme version checks
		remove_action( 'load-themes.php',           'wp_update_themes' );
		remove_action( 'load-update-core.php',      'wp_update_themes' );
		remove_action( 'load-update.php',           'wp_update_themes' );
		remove_action( 'wp_update_themes',          'wp_update_themes' );
		remove_action( 'admin_init',                '_maybe_update_themes' );
		remove_action( 'upgrader_process_complete', 'wp_update_themes', 10, 0 );

		// Simulate Transient returning "up-to-date"
		add_filter( 'pre_site_transient_update_themes', array( '\\WPC\\DisableUpdates', 'return_empty_update' ) );

		// @todo
		// add_filter( 'site_transient_update_themes', '__return_null' );

		// Prevent update cron creation + remove potential existing crons
		wp_clear_scheduled_hook( 'wp_update_themes' );

	} // END __construct()

} // class Themes

new Themes();
