<?php

/*
 *	Add settings page
 */
add_action('admin_menu', function(){
	add_options_page(
		'TACDIS Ecom Settings',
		'TACDIS Ecom',
		'manage_options',
		'tacdis-ecom.php',
		'tacdis_settings_template'
	);
});

/*
 *	Settings page callback
 */
function tacdis_settings_template () {

	if ( isset($_POST) && isset($_POST['tacdis-settings-submit']) ) {
		$save_result = tacdis_save_settings( $_POST );
	}

	$form_action = 'options-general.php?page=tacdis-ecom.php';

	$wheel_change_page = get_option('tacdis_wheel_change_page');

	$pages_dropdown_args = array(
		'name'             => 'tacdis_wheel_change_page',
		'selected'         => $wheel_change_page,
		'show_option_none' => __('(disabled)'),
		'sort_column'      => 'menu_order, post_title',
		'echo'             => 0,
	);

	$pages_dropdown_args = apply_filters( 'page_attributes_dropdown_pages_args', $pages_dropdown_args );
	
	$pages = wp_dropdown_pages( $pages_dropdown_args );

	// Set default values, use qa values
	if (false === $tacdis_remote_host = get_option('tacdis_remote_host')) {
		update_option('tacdis_remote_host', 'https://ecomm-qa.se.tacdis.com');
		$tacdis_remote_host = 'https://ecomm-qa.se.tacdis.com';
	}

	// Set default values, use qa values
	if (false === $tacdis_ecom_resource_url = get_option('tacdis_ecom_resource_url')) {
		update_option('tacdis_ecom_resource_url', 'https://cdn-qa.se.tacdis.com/plugin');
		$tacdis_ecom_resource_url = 'https://cdn-qa.se.tacdis.com/plugin';
	}
	
	$tacdis_api_key = get_option('tacdis_api_key');
	$tacdis_tenant_id = get_option('tacdis_tenant_id');
	$tacdis_tenant_id_2 = get_option('tacdis_tenant_id_2');
	$tacdis_tenant_id_3 = get_option('tacdis_tenant_id_3');
	
	// Set default values, use qa values
	if (false === $tacdis_version_cache_expiretime = get_option('tacdis_version_cache_expiretime')) {
		update_option('tacdis_version_cache_expiretime', 5);
		$tacdis_version_cache_expiretime = 5;
	}
	
	include plugin_dir_path( __FILE__ ) . 'settings-template.php';
}

function tacdis_save_settings( $settings ) {

	if ( isset($settings['tacdis_api_key']) ) {
		update_option( 'tacdis_api_key', $settings['tacdis_api_key'] );
	}

	if ( isset($settings['tacdis_tenant_id']) ) {
		update_option( 'tacdis_tenant_id', $settings['tacdis_tenant_id'] );
	}

	if ( isset($settings['tacdis_tenant_id_2']) ) {
		update_option( 'tacdis_tenant_id_2', $settings['tacdis_tenant_id_2'] );
	}

	if ( isset($settings['tacdis_tenant_id_3']) ) {
		update_option( 'tacdis_tenant_id_3', $settings['tacdis_tenant_id_3'] );
	}

	if ( isset($settings['tacdis_remote_host']) ) {
		update_option( 'tacdis_remote_host', $settings['tacdis_remote_host'] );
	}

	if ( isset($settings['tacdis_ecom_resource_url']) ) {
		update_option( 'tacdis_ecom_resource_url', $settings['tacdis_ecom_resource_url'] );
	}

	if ( isset($settings['tacdis_version_cache_expiretime']) ) {
		update_option( 'tacdis_version_cache_expiretime', $settings['tacdis_version_cache_expiretime'] );
	}

	set_transient('tacdis-module-versions', "", 0);

	return true;
}