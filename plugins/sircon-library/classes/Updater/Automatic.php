<?php

namespace Sircon\Library\Updater;

use Sircon\Library\Util;
use WP_Error;

final class Automatic extends Updater {

	private static $instance = null;

	private function __construct() {
		add_filter('update_plugins_code.sircon.net', [$this, 'check'], 10, 3);
		add_filter('plugins_api', [$this, 'info'], 10, 3);
	}

	public function check($result, $plugin_data, $plugin_file): array {
		$slug = dirname($plugin_file);
		$response = $this->apiRequest("/plugin/$slug/update", $slug, $plugin_data['Version']);
		if (is_wp_error($response) || ($response['response']['code'] != 200)) {
			return [];
		}

		$result = json_decode($response['body'], true) ?? [];
		if (empty($result)) {
			return [];
		}

		return $result;
	}

	public function info($result, string $action, object $args) {
		if (empty($args->slug) || $action !== 'plugin_information') {
			return $result;
		}

		$plugin_data = Util::pluginDataBySlug($args->slug, ['UpdateURI' => 'Update URI']);
		if (empty($plugin_data['UpdateURI'])) {
			return $result;
		}

		$hostname = wp_parse_url(esc_url_raw($plugin_data['UpdateURI']), PHP_URL_HOST);
		if ($hostname !== 'code.sircon.net') {
			return $result;
		}

		$response = $this->apiRequest("/plugin/{$args->slug}/info", $args->slug);
		if (is_wp_error($response)) {
			return new WP_Error('plugins_api_failed', sprintf('%s<br /><a href="?" onclick="document.location.reload(); return false;">%s</a>', __('An Unexpected HTTP Error occurred during the API request.', 'sircon-library'), __('Try again', 'sircon-library')), $response->get_error_message());
		}

		$result = json_decode($response['body'], true);
		if (empty($result)) {
			return new WP_Error('plugins_api_failed', __('An unknown error occurred', 'sircon-library'), $response['body']);
		}

		return (object) $result;
	}

	public static function enable(): void {
		self::$instance = self::$instance ?? new self();
	}
}
