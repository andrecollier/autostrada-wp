<?php

namespace Sircon\Library\Updater;

use WP_Error;

final class Plugin extends Updater {

	private $plugin_file;

	public function __construct(string $plugin_file) {
		$this->plugin_file = $plugin_file;
		add_filter('pre_set_site_transient_update_plugins', [$this, 'updateCheck']);
		add_filter('plugins_api', [$this, 'info'], 10, 3);
	}

	public function updateCheck(?object $transient) {
		$slug = basename(dirname($this->plugin_file));
		$main_file = basename($this->plugin_file);
		if (empty($transient->checked) || empty($transient->checked[$slug . '/' . $main_file])) {
			return $transient;
		}

		$version = $transient->checked[$slug . '/' . $main_file];

		$res = $this->apiRequest("/plugin/$slug/update", $slug, $version);
		if (is_wp_error($res) || ($res['response']['code'] != 200)) {
			return $transient;
		}

		$response = json_decode($res['body']);
		if (empty($response) || !is_object($response)) {
			return $transient;
		}

		$response->icons = (array) $response->icons;
		if (version_compare($response->new_version, $version) == 1) {
			$transient->response[$slug . '/' . $main_file] = $response;
		} else {
			$transient->no_update[$slug . '/' . $main_file] = $response;
		}

		update_option($slug . '-update', $response->update);
		return $transient;
	}

	public function info($result, string $action, object $args) {
		if ($action !== 'plugin_information') {
			return $result;
		}

		$slug = basename(dirname($this->plugin_file));

		if (empty($args->slug) || ($args->slug != $slug)) {
			return $result;
		}

		$response = $this->apiRequest("/plugin/$slug/info", $slug);
		if (is_wp_error($response)) {
			return new WP_Error('plugins_api_failed', sprintf('%s<br /><a href="?" onclick="document.location.reload(); return false;">%s</a>', __('An Unexpected HTTP Error occurred during the API request.', 'sircon-library'), __('Try again', 'sircon-library')), $response->get_error_message());
		}

		$result = json_decode($response['body'], true);
		if (empty($result)) {
			return new WP_Error('plugins_api_failed', __('An unknown error occurred', 'sircon-library'), $response['body']);
		}

		return (object) $result;
	}

	public static function register(string $plugin_file, bool $is_licensed = false): void {
		new Plugin($plugin_file);

		if ($is_licensed) {
			Licenser::register($plugin_file);
		}
	}
}
