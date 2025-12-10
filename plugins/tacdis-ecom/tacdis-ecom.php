<?php
/*
Plugin Name: TACDIS E-COM
Description: Adds TACDIS E-COM functionality to your Wordpress site.
Version: 20190225.1
Author: VCRS AB
Author URI: https://info-ecom.se.tacdis.com/
*/

/*
 *	No direct access
 */
defined( 'ABSPATH' ) or die( );


define( 'TACDIS__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

$includes = array(
	'tacdis-init',
	'tacdis-admin/settings-page'
);

/*
 *	Include required files
 */
foreach ($includes as $file) {
	require_once TACDIS__PLUGIN_DIR . $file . '.php';
}