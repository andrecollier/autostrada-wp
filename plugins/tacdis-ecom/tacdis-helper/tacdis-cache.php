<?php
require_once "tacdis-proxy-functions.php";

function tacdis_get_module_version($module_name, $expire_time_in_seconds) {
    if ( false === ( $module_versions = get_transient( 'tacdis-module-versions'))) {
        // this code runs when there is no valid transient set
        $module_versions = tacdis_get_module_version_remote();
        set_transient('tacdis-module-versions', $module_versions, $expire_time_in_seconds);
    }
    else {
        if ($module_versions == "") {
            // this code runs when there is no valid transient set
            $module_versions = tacdis_get_module_version_remote();
            set_transient('tacdis-module-versions', $module_versions, $expire_time_in_seconds);
        }
    }

    if (empty($module_versions) === false){
        foreach($module_versions as $module_version) {
            if ((is_array($module_version) && sizeof($module_version) > 1) && (strtolower($module_name) == strtolower($module_version["id"]))) {
                return $module_version["version"];
            }
        }
    }

   return "latest";
}

function tacdis_get_module_version_remote() {
	try
	{
		$remote_host = trim(get_option('tacdis_remote_host'), '/') . '/api/coremodule/versions';
		$api_key = get_option('tacdis_api_key');
		$tenant_id = get_option('tacdis_tenant_id');
		$data = '';
		$method = 'GET';
		$result = json_decode(tacdis_make_proxy_request($remote_host, $data, $method, $api_key, $tenant_id, FALSE), true);

		return $result;
	}
	catch (Exception $e) {
        return array();
	}
}