<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 19/01/16
 * Time: 15:24
 */

// If uninstall is not called from WordPress, exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}
// Nothing required at present

if ( 0 ) {
	$option_name = 'plugin_option_name';
	delete_option( $option_name );

	// For site options in Multisite
	delete_site_option( $option_name );
}
