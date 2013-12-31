<?php
/**
 * The WordPress Plugin Boilerplate. < Thank you <3 the boiler plate!
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   Coming_Soon
 * @author    John Turner <john@seedprod.com>
 * @license   GPL-2.0+
 * @link      http://www.seedprod.com
 * @copyright 2013 SeedProd
 *
 * @wordpress-plugin
 * Plugin Name:       Coming Soon
 * Plugin URI:        http://www.seedprod.com
 * Description:       Coming Soon, Maintenance Mode & Landing Pages in minutes
 * Version:           1.0.0
 * Author:            SeedProd
 * Author URI:        http://www.seedprod.com
 * Text Domain:       seedprod-coming-soon
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/<owner>/<repo>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-seedcs.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'SeedCS', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'SeedCS', 'deactivate' ) );

/*
 * @TODO:
 *
 * - replace Plugin_Name with the name of the class defined in
 *   `class-plugin-name.php`
 */
add_action( 'plugins_loaded', array( 'SeedCS', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * - replace `class-plugin-admin.php` with the name of the plugin's admin file
 * - replace Plugin_Name_Admin with the name of the class defined in
 *   `class-plugin-name-admin.php`
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-seedcs-admin.php' );
	add_action( 'plugins_loaded', array( 'SeedCS_Admin', 'get_instance' ) );

}
