<?php
/**
 * @author    WP-Cloud <code@wp-cloud.org>
 * @copyright Copyright (c) 2014-2015, WP-Cloud
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPL-2.0+
 * @package   WPC\DisableUpdates
 * @version   0.1.0
 */
/*
Plugin Name: Disable Updates
Description: Set the right environment for WP in our cloud
Version:     0.1.0
Author:      WP-Cloud
Author URI:  https://www.wp-cloud.eu
License:     GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Network:     true

    Disable Updates
    Copyright (C) 2014-2015 WP-Cloud (http://www.wp-cloud.org)

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

/**
 * Deactivate all (automatic) update functionality
 *
 * @since 0.0.1
 */
class DisableUpdates {

	/**
	 * wp-config.php
	 * define( 'AUTOMATIC_UPDATER_DISABLED', true );
	 * define( 'WP_AUTO_UPDATE_CORE', false );
	 * define( 'DISALLOW_FILE_EDIT', true );
	 * define( 'DISALLOW_FILE_MODS', true );
	 */

	/**
	 * Constructor. Hooks all interactions to initialize the class.
	 *
	 * @since  0.0.1
	 */
	public function __construct() {
		$this->includes( dirname( __FILE__ ) );
	} // END __construct()

	/**
	 * Include extensions for custom/non-wp.org plugins/themes
	 *
	 * @since  0.0.1
	 */
	private function includes( $path ) {
		foreach ( glob( $path . '/extensions/*.php' ) as $filename ) {
			include_once $filename;
		}
	} // END includes()

	/**
	 * Return object suggesting everything is 'up-to-date'
	 *
	 * @since  0.0.1
	 * @return object
	 */
	public static function return_empty_update() {

		global $wp_version;

		return (object) array(
			'updates'         => array(),
			'version_checked' => $wp_version,
			'last_checked'    => time(),
		);

	} // END __return_empty_update()

} // class DisableUpdates

new DisableUpdates();
