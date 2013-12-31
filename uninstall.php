<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Coming Soon
 * @author    John Turner <john@seedprod.com>
 * @license   GPL-2.0+
 * @link      http://www.seedprod.com
 * @copyright 2013 SeedProd
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// @TODO: Define uninstall functionality here