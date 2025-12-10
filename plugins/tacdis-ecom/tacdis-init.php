<?php

require_once "tacdis-helper/tacdis-cache.php";
require_once "tacdis-helper/tacdis-shortcode.php";

$wheelchange_shortcode = 'tacdis_ecom_wheel_change';
$servicebooking_shortcode = 'tacdis_ecom_servicebooking';
$windowrepair_shortcode = 'tacdis_ecom_windowrepair';

// Get version information
$expire_time			= get_option('tacdis_version_cache_expiretime')*60;
$servicebooking_ver 	= tacdis_get_module_version('VirtoCommerce.VCRS.ServiceBooking', $expire_time);
$wheelchange_ver 		= tacdis_get_module_version('VirtoCommerce.VCRS.TimeBooking', $expire_time);
$windowrepair_ver 		= tacdis_get_module_version('VirtoCommerce.VCRS.ServiceBooking', $expire_time);

$resource_src 			= rtrim(get_option('tacdis_ecom_resource_url'), '/') . '/';

$has_excerpt = false;

add_shortcode($wheelchange_shortcode, 'tacdis_hook_wheelchange');

function tacdis_hook_wheelchange ($atts) {
	if (exclude_shortcode()) {
		return;
	}

	try {
		ob_start();
		
		$a = shortcode_atts( array(
			'workshop-filter' => ''
		), $atts);
		
		if (empty($a['workshop-filter']))
		{
			echo '<vcrs-wheelchange>Loading wheel change...</vcrs-wheelchange>';	
		}
		else
		{
			echo '<vcrs-wheelchange workshop-filter="' . esc_attr($a['workshop-filter']) . '">Loading wheel change...</vcrs-wheelchange>';
		}

		queue_tacdis_wheelchange_scripts();
	}
	finally {
		return ob_get_clean();
	}
}

add_shortcode($servicebooking_shortcode, 'tacdis_hook_servicebooking');

function tacdis_hook_servicebooking ($atts) {
	if (exclude_shortcode()) {
		return;
	}

	try {
		ob_start();
		
		$a = shortcode_atts( array(
			'workshop-filter' => ''
		), $atts);

		if (empty($a['workshop-filter']))
		{
			echo '<tacdis-servicebooking>Loading service booking...</tacdis-servicebooking>';
		}
		else
		{
			echo '<tacdis-servicebooking workshop-filter="' . esc_attr($a['workshop-filter']) . '">Loading service booking...</tacdis-servicebooking>';
		}
	
		queue_tacdis_servicebooking_scripts();
	}
	finally {
		return ob_get_clean();
	}
}

add_shortcode($windowrepair_shortcode, 'tacdis_hook_windowrepair');

function tacdis_hook_windowrepair ($atts) {
	if (exclude_shortcode()) {
		return;
	}

	try {
		ob_start();

		$a = shortcode_atts( array(
			'workshop-filter' => ''
		), $atts);
		
		if (empty($a['workshop-filter']))
		{
			echo '<tacdis-windowrepair>Loading window repair...</tacdis-windowrepair>';	
		}
		else
		{
			echo '<tacdis-windowrepair workshop-filter="' . esc_attr($a['workshop-filter']) . '">Loading window repair...</tacdis-windowrepair>';
		}

		queue_tacdis_windowrepair_scripts();
	}
	finally {
		return ob_get_clean();
	}
}

function queue_tacdis_wheelchange_scripts() {
	global $wheelchange_ver, $resource_src;
	$ver_slug = 'wc-' . str_replace('.', '-', $wheelchange_ver);
	
	$scripts = array(		
		"vendor_src" 			=> $resource_src . $ver_slug . "/js/vendor.js",
		"polyfills_src" 		=> $resource_src . $ver_slug . "/js/polyfills.js",
		"core_src" 				=> $resource_src . $ver_slug . "/js/tacdis.core.js",
		"wheelchange_src" 		=> $resource_src . $ver_slug . "/js/tacdis.wheelchange.js",
		"translation_src" 		=> $resource_src . $ver_slug . "/js/translation.js",
		"app_src" 				=> $resource_src . $ver_slug . "/js/app.js",
	);
	
	echo "\n";
	foreach ( $scripts as $script) {
		echo "<script src='$script'></script> \n";
	}
}

function queue_tacdis_servicebooking_scripts() {
	global $servicebooking_ver, $resource_src;
	$ver_slug = 'sb-' . str_replace('.', '-', $servicebooking_ver);
	
	$scripts = array(		
		"vendor_src" 			=> $resource_src . $ver_slug . "/js/vendor.js",
		"polyfills_src" 		=> $resource_src . $ver_slug . "/js/polyfills.js",
		"core_src" 				=> $resource_src . $ver_slug . "/js/tacdis.core.js",
		"servicebooking_src" 	=> $resource_src . $ver_slug . "/js/tacdis.servicebooking.js",
		"translation_src" 		=> $resource_src . $ver_slug . "/js/translation.js",
		"app_src" 				=> $resource_src . $ver_slug . "/js/app.js",
	);
	
	echo "\n";
	foreach ( $scripts as $script) {
		echo "<script src='$script'></script> \n";
	}
}

function queue_tacdis_windowrepair_scripts() {
	global $windowrepair_ver, $resource_src;
	$ver_slug = 'wr-' . str_replace('.', '-', $windowrepair_ver);
	
	$scripts = array(		
		"vendor_src" 			=> $resource_src . $ver_slug . "/js/vendor.js",
		"polyfills_src" 		=> $resource_src . $ver_slug . "/js/polyfills.js",
		"core_src" 				=> $resource_src . $ver_slug . "/js/tacdis.core.js",
		"windowrepair_src" 		=> $resource_src . $ver_slug . "/js/tacdis.windowrepair.js",
		"translation_src" 		=> $resource_src . $ver_slug . "/js/translation.js",
		"app_src" 				=> $resource_src . $ver_slug . "/js/app.js",
	);
	
	echo "\n";
	foreach ( $scripts as $script) {
		echo "<script src='$script'></script> \n";
	}
}

add_action('wp_enqueue_scripts', function() {
	global $post, $wheelchange_shortcode, $servicebooking_shortcode, $windowrepair_shortcode, $resource_src, $wheelchange_ver, $servicebooking_ver, $windowrepair_ver;
	
	if (has_tacdis_shortcode($post, $wheelchange_shortcode, 'tacdis_wheelchange')) {
		$ver_slug = 'wc-' . str_replace('.', '-', $wheelchange_ver);

		$toaster_style_src 	= $resource_src . $ver_slug . "/assets/styles/toaster.css";
		wp_enqueue_style('tacdis_styling_toaster', $toaster_style_src, array(), '1.0.0' );		
	}

	if (has_tacdis_shortcode($post, $servicebooking_shortcode, 'tacdis_service')) {
		$ver_slug = 'sb-' . str_replace('.', '-', $servicebooking_ver);

		$toaster_style_src 	= $resource_src . $ver_slug . "/assets/styles/toaster.css";
		wp_enqueue_style('tacdis_styling_toaster', $toaster_style_src, array(), '1.0.0' );
	}

	if (has_tacdis_shortcode($post, $windowrepair_shortcode, 'tacdis_windowrepair')) {
		$ver_slug = 'wr-' . str_replace('.', '-', $windowrepair_ver);

		$toaster_style_src 	= $resource_src . $ver_slug . "/assets/styles/toaster.css";
		wp_enqueue_style('tacdis_styling_toaster', $toaster_style_src, array(), '1.0.0' );
	}
});

add_action('rest_api_init', function(){	
	for ($idx = 1; $idx <= 3; $idx++) {
		$route = 'tacdis-wp-proxy' . ($idx == 1  ? "" : $idx);
		register_rest_route($route, '/ecom(.*)' , 
			array(
				array(
					'methods' => 'GET',
					'callback' => 'api_callback_' . $idx,
				),
				array(
					'methods' => 'POST',
					'callback' => 'api_callback_' . $idx
				),
				array(
					'methods' => 'PUT',
					'callback' => 'api_callback_' . $idx
				),
				array(
					'methods' => 'HEAD',
					'callback' => 'api_callback_' . $idx
				),
				array(
					'methods' => 'DELETE',
					'callback' => 'api_callback_' . $idx
				)
			)
		);
	}
});

add_action( 'wp_head', function(){
	global $q, $post, $wheelchange_shortcode, $servicebooking_shortcode, $windowrepair_shortcode, $wheelchange_ver, $servicebooking_ver, $windowrepair_ver, $resource_src;

	if (has_tacdis_shortcode($post, $wheelchange_shortcode, 'tacdis_wheelchange') && !exclude_shortcode()) {
		$ver_slug = 'wc-' . str_replace('.', '-', $wheelchange_ver);

		$dealerId = get_dealer_id_from_short_code($wheelchange_shortcode, $post);	
		$q = get_pathquery('tacdis-wp-proxy' . $dealerId);

		echo "<script>
			window.localStorage.setItem('tacdisproxyurl','" . $q . "');
			window.localStorage.setItem('translationurl', '" . $resource_src . $ver_slug . "/assets/i18n');
			window.localStorage.setItem('citiesurl', '" . $resource_src . $ver_slug . "/assets/cities/cities.json');
			</script>";
	}

	if (has_tacdis_shortcode($post, $servicebooking_shortcode, 'tacdis_service') && !exclude_shortcode()) {
		$ver_slug = 'sb-' . str_replace('.', '-', $servicebooking_ver);

		$dealerId = get_dealer_id_from_short_code($servicebooking_shortcode, $post);	
		$q = get_pathquery('tacdis-wp-proxy' . $dealerId);

		echo "<script>
			window.localStorage.setItem('tacdisproxyurl','" . $q . "');
			window.localStorage.setItem('translationurl', '" . $resource_src . $ver_slug . "/assets/i18n');
			</script>";
	}

	if (has_tacdis_shortcode($post, $windowrepair_shortcode, 'tacdis_windowrepair') && !exclude_shortcode()) {
		$ver_slug = 'wr-' . str_replace('.', '-', $windowrepair_ver);

		$dealerId = get_dealer_id_from_short_code($windowrepair_shortcode, $post);	
		$q = get_pathquery('tacdis-wp-proxy' . $dealerId);

		echo "<script>
			window.localStorage.setItem('tacdisproxyurl', '" . $q . "');
			window.localStorage.setItem('translationurl', '" . $resource_src . $ver_slug . "/assets/i18n');
			</script>";
	}
}, 100 );


add_filter( 'get_the_excerpt', 'tacdis_excerpt_filter' );
  
function tacdis_excerpt_filter( $excerpt ){
	global $has_excerpt;
	// Check if short code are in excerpt
	$has_excerpt = false;
	if (has_shortcode($excerpt, 'tacdis_wheelchange') ||
		has_shortcode($excerpt, 'tacdis_service') ||
		has_shortcode($excerpt, 'tacdis_windowrepair')) {
			$has_excerpt = true;
	}
}

function exclude_shortcode() {
	global $has_excerpt;
	if($has_excerpt || is_admin()){
		return true;
	}

	return false;
}

function get_pathquery($apiPath) {
	$rest_url = get_rest_url();
	$parsed_url = wp_parse_url($rest_url);
	$path = $parsed_url['path'];

	if (array_key_exists('query', $parsed_url)) {
		$query = $parsed_url['query'];

		if ($query) {
			return $path . '?' . $query . $apiPath . '/ecom';
		}
	}

	return rtrim($path, '/') . '/' . $apiPath . '/ecom';
}

function api_callback_1() {
	$remote_host = get_option('tacdis_remote_host');;
	$api_key = get_option('tacdis_api_key');
	$tenant_id = get_option('tacdis_tenant_id');
	
	header("Content-Type: application/json");
	
	/*
		Initiate proxy request
	*/
	wp_send_json(json_decode(tacdis_handle_request(tacdis_get_proxy_target('tacdis-wp-proxy'),
	$remote_host, $api_key, $tenant_id)));	
}

function api_callback_2() {
	$remote_host = get_option('tacdis_remote_host');
	$api_key = get_option('tacdis_api_key');
	$tenant_id = get_option('tacdis_tenant_id_2');
	if (NULL == $tenant_id || "" == $tenant_id) {
		$tenant_id = get_option('tacdis_tenant_id');
	}

	header("Content-Type: application/json");

	/*
		Initiate proxy request
	*/

	// $test = json_decode(tacdis_handle_request(tacdis_get_proxy_target('tacdis-wp-proxy'),
	// $remote_host, $api_key, $tenant_id));

	wp_send_json(json_decode(tacdis_handle_request(tacdis_get_proxy_target('tacdis-wp-proxy'),
	$remote_host, $api_key, $tenant_id)));	
}

function api_callback_3() {
	$remote_host = get_option('tacdis_remote_host');
	$api_key = get_option('tacdis_api_key');
	$tenant_id = get_option('tacdis_tenant_id_3');
	if (NULL == $tenant_id || "" == $tenant_id) {
		$tenant_id = get_option('tacdis_tenant_id');
	}

	header("Content-Type: application/json");

	/*
		Initiate proxy request
	*/
	wp_send_json(json_decode(tacdis_handle_request(tacdis_get_proxy_target('tacdis-wp-proxy'),
	$remote_host, $api_key, $tenant_id)));	
}