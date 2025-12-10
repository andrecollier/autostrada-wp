<?php

namespace Sircon\Library\Updater;

/*
Put this in functions.php:

if (in_array('sircon-library/sircon-library.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	\Sircon\Library\Updater\Theme::register(__FILE__);
}

For licensed themes pass true as a second parameter to Theme::register like this:
	\Sircon\Library\Updater\Theme::register(__FILE__, true);

*/

final class Theme extends Updater {

	private $theme;

	public function __construct(string $theme_functions) {
		$this->theme = basename(dirname($theme_functions));
		add_filter('pre_set_site_transient_update_themes', [$this, 'updateCheck']);
	}

	public function updateCheck(?object $transient) {
		if (empty($transient->checked) || empty($transient->checked[$this->theme])) {
			return $transient;
		}

		$version = $transient->checked[$this->theme];

		$res = $this->apiRequest("/theme/{$this->theme}/update", $this->theme, $version);
		if (is_wp_error($res) || ($res['response']['code'] != 200)) {
			return $transient;
		}

		$response = json_decode($res['body']);
		if (empty($response) || !is_object($response)) {
			return $transient;
		}

		if (version_compare($response->new_version, $version) == 1) {
			$transient->response[$this->theme] = (array) $response;
		}

		update_option($this->theme . '-update', $response->update);
		return $transient;
	}

	public static function register(string $theme_functions, bool $is_licensed = false): void {
		new Theme($theme_functions);

		if ($is_licensed) {
			Licenser::register($theme_functions);
		}
	}
}
