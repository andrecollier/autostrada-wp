<?php

/*
  DEBUG, CORS ALLOW ANY
*/
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Access-Control-Allow-Methods: *");
/* -- */

$_supported_http_methods = array('GET', 'POST', 'PUT', 'DELETE', 'OPTIONS');


function tacdis_get_executing_file() {
	return basename($_SERVER['SCRIPT_FILENAME']);
}	

function tacdis_get_proxy_target() {
	
	if (!empty($_GET['rest_route'])) {
		$parts = explode('?path=', $_GET['rest_route']);

		return urldecode($parts[1]);
	}

	if (!empty($_GET['path'])) {
		return '/' . trim($_GET['path'], '/');
	}

	$p = explode(tacdis_get_executing_file(), $_SERVER['REQUEST_URI']);
	return count($p) > 1 ? urldecode($p[1]) : null;
}

function tacdis_get_http_method() {
	return strtoupper($_SERVER['REQUEST_METHOD']);
}

function tacdis_get_proxy_data($method) {
	if ($method == "POST" || $method == "PUT") {
		return file_get_contents('php://input');
	}
	
	return null;
}

function tacdis_is_method_allowed($uri, $method) {
	/* 
		We could implement a whitelist here,
		to filter out requests that's not
		allowed to use
	*/
	global $_supported_http_methods;
	return in_array($method, $_supported_http_methods);
}

function tacdis_create_remote_addr($path, $host) {
	return trim($host, '/') . '/' . trim($path, '/');
}

function tacdis_is_successful($status_code) {
	return round($status_code / 200) == 1 && $status_code >= 200;
}

function _tacdis_make_empty_proxy_request($uri, $method, $api_key, $tenant_id, $writeErrorToResponse) {
	$c = curl_init($uri);

	if (parse_url($uri, PHP_URL_SCHEME) === "https") {
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
	}

	curl_setopt($c, CURLOPT_CUSTOMREQUEST, $method);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($c, CURLOPT_HTTPHEADER, array(
		'Accept: application/json',
		'X-Forwarded-For: ' . $_SERVER['REMOTE_ADDR'],
		'Host: ' . parse_url($uri, PHP_URL_HOST),
		'api_key: ' . $api_key,
		'tenant_id: ' . $tenant_id,
		'client_source: ' . 'd-cms',
		'cookie: ' . $_SERVER['HTTP_COOKIE'],
		'user-agent: ' . $_SERVER['HTTP_USER_AGENT']
	));

	$response = curl_exec($c);
	$status_code = intval(curl_getinfo($c, CURLINFO_RESPONSE_CODE));
	if (curl_errno($c) || !tacdis_is_successful($status_code)) {
		if ($writeErrorToResponse == true) {
			tacdis_exit_with_error($status_code, $response);
		}
	}
	
	return $response;
}
	
function _tacdis_make_data_proxy_request($uri, $data, $method, $api_key, $tenant_id, $writeErrorToResponse) {
	$c = curl_init($uri);

	if (parse_url($uri, PHP_URL_SCHEME) === "https") {
		curl_setopt($c, CURLOPT_SSL_VERIFYPEER, false);
	}
	
	curl_setopt($c, CURLOPT_CUSTOMREQUEST, $method);
	curl_setopt($c,	CURLOPT_POSTFIELDS, $data);
	curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($c, CURLOPT_HTTPHEADER, array(
		'Accept: application/json',
		'Content-Type: application/json',
		'Content-Length: ' . strlen($data),
		'X-Forwarded-For: ' . $_SERVER['REMOTE_ADDR'],
		'Host: ' . parse_url($uri, PHP_URL_HOST),
		'api_key: ' . $api_key,
		'tenant_id: ' . $tenant_id,
		'client_source: ' . 'd-cms',
		'cookie: ' . $_SERVER['HTTP_COOKIE'],
		'user-agent: ' . $_SERVER['HTTP_USER_AGENT']
	));
	
	$response = curl_exec($c);
 	$status_code = intval(curl_getinfo($c, CURLINFO_RESPONSE_CODE));
	if (curl_errno($c) || !tacdis_is_successful($status_code)) {
		if ($writeErrorToResponse == true) {
			tacdis_exit_with_error($status_code, $response);
		}
	}
	
	return $response;
}

function tacdis_make_proxy_request($target, $data, $method, $api_key, $tenant_id, $writeErrorToResponse) {
	if ($method == "OPTIONS") {
		return "";
	}
	if ($method == "GET" || $method == "DELETE") {
		return _tacdis_make_empty_proxy_request($target, $method, $api_key, $tenant_id, $writeErrorToResponse);
	}
	
	return _tacdis_make_data_proxy_request($target, $data, $method, $api_key, $tenant_id, $writeErrorToResponse);
}

function tacdis_exit_with_error($code, $message) {
	http_response_code($code);
	echo $message;
	die();
}

function tacdis_handle_request($target, $remote_host, $api_key, $tenant_id) {
	$method = tacdis_get_http_method();
	if (empty($target) || !tacdis_is_method_allowed($target, $method)) {
		tacdis_exit_with_error(400, "Bad Request");
	}
	
	return tacdis_make_proxy_request(tacdis_create_remote_addr($target, $remote_host), tacdis_get_proxy_data($method), $method, $api_key, $tenant_id, true);
}