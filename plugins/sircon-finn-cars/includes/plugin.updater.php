<?php // phpcs:ignore PSR1.Files.SideEffects.FoundWithSymbols

/**
* Enable plugin updates from Sircon Norge AS.
*
* This file should be placed in any folder within the main plugin-folder, and included outside any hook
*/

namespace sircon\update;

global $SIRCON_PLUGINS, $SIRCON_THEMES;
if (!$SIRCON_PLUGINS) {
	$SIRCON_PLUGINS = [];
}

if (!$SIRCON_THEMES) {
	$SIRCON_THEMES = [];
}

$SIRCON_PLUGINS[] = basename(dirname(dirname(__FILE__)));

if (!function_exists('\sircon\update\validate')) {
	function validate($slug) {
		return empty(get_option($slug . '-update')) ? false : get_option($slug . '-update') === 'validated';
	}
	function get_license($slug) {
		$licenses = get_option('sircon-licenses');
		return $licenses[$slug] ?? null;
	}
	function f_($m) {
		switch ($m) {
			case 'Sircon License':
				switch (get_locale()) {
					case 'nb_NO':
						return 'Sircon Lisens';
					default:
						return $m;
				}
				break;

			case 'Sircon Licenses':
				switch (get_locale()) {
					case 'nb_NO':
						return 'Sircon Lisenser';
					default:
						return $m;
				}
				break;

			case 'license key':
				switch (get_locale()) {
					case 'nb_NO':
						return 'lisensnøkkel';
					default:
						return $m;
				}
				break;

			case 'Plugins':
				switch (get_locale()) {
					case 'nb_NO':
						return 'Utvidelser';
					default:
						return $m;
				}
				break;

			case 'Themes':
				switch (get_locale()) {
					case 'nb_NO':
						return 'Tema';
					default:
						return $m;
				}
				break;

			case 'Invalid licensekey for':
				switch (get_locale()) {
					case 'nb_NO':
						return 'Ugyldig lisensnøkkel for';
					default:
						return $m;
				}
				break;

			case 'Update you license key here':
				switch (get_locale()) {
					case 'nb_NO':
						return 'Oppdater lisensnøkkelen her';
					default:
						return $m;
				}
				break;

			default:
				return $m;
		}
	}
	function options_page_output() {
		echo '<div class="wrap"><h1>' . f_('Sircon License') . '</h1><form method="post" action="options.php">';
		settings_fields('sircon-license');
		do_settings_sections('sircon-license');
		submit_button();
		echo '</form></div>';
	}
	function update_request($action, $slug, $version = false) {
		global $wp_version;
		$request = [
			'body' => ['action' => $action, 'slug' => $slug],
			'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url('/'),
		];
		$request['body']['domain']  = $_SERVER['SERVER_NAME'];
		$request['body']['license'] = get_license($slug);
		if ($version) {
			$request['body']['version'] = $version;
		}

		return wp_remote_post('https://code.sircon.net/', $request);
	}
	add_action('admin_menu', function () {
		add_action('load-' . add_submenu_page('options-general.php', f_('Sircon License'), f_('Sircon Licenses'), 'manage_options', 'sircon-license', '\sircon\update\options_page_output'), function () {
			if (!empty($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') {
				global $SIRCON_PLUGINS, $SIRCON_THEMES;
				$licenses = get_option('sircon-licenses');
				foreach ($SIRCON_PLUGINS as $plugin) {
					$res = update_request('plugin_license', $plugin);
					if (is_wp_error($res) || ($res['response']['code'] != 200)) {
						continue;
					}

					$response = json_decode($res['body']);
					if (empty($response) || !is_object($response)) {
						continue;
					}

					update_option($plugin . '-update', $response->update);
				}

				foreach ($SIRCON_THEMES as $theme) {
					$res = update_request('theme_license', $theme);
					if (is_wp_error($res) || ($res['response']['code'] != 200)) {
						continue;
					}

					$response = json_decode($res['body']);
					if (empty($response) || !is_object($response)) {
						continue;
					}

					update_option($theme . '-update', $response->update);
				}

				set_site_transient('update_plugins', null);
				set_site_transient('update_themes', null);
			}
		});
		add_action('admin_init', function () {
			register_setting('sircon-license', 'sircon-licenses');
			$licenses = get_option('sircon-licenses');
			if (!$licenses) {
				$licenses = [];
			}

			global $SIRCON_PLUGINS, $SIRCON_THEMES;
			if ($SIRCON_PLUGINS) {
				add_settings_section('sircon-licenses-plugins', f_('Plugins'), '__return_false', 'sircon-license');
			}

			if ($SIRCON_THEMES) {
				add_settings_section('sircon-licenses-themes', f_('Themes'), '__return_false', 'sircon-license');
			}

			foreach ($SIRCON_PLUGINS as $plugin) {
				add_settings_field($plugin, ucwords(str_replace('-', ' ', $plugin)) . ' ' . f_('license key'), function ($args) {
					echo '<input type="text" id="' . $args['plugin'] . '" name="sircon-licenses[' . $args['plugin'] . ']" value="' . $args['value'] . '" />';
					echo (get_option($args['plugin'] . '-update') && get_option($args['plugin'] . '-update') !== 'validated') ? '<span class="dashicons dashicons-warning" style="color:#ca4a1f;line-height:28px;"></span>' : '<span class="dashicons dashicons-yes" style="color:green;line-height:28px;"></span>';
				}, 'sircon-license', 'sircon-licenses-plugins', ['label_for' => $plugin . '-license', 'plugin' => $plugin, 'value' => $licenses[$plugin] ?? '']);
			}

			foreach ($SIRCON_THEMES as $theme) {
				add_settings_field($theme, ucwords(str_replace('-', ' ', $theme)) . ' ' . f_('license key'), function ($args) {
					echo '<input type="text" id="' . $args['theme'] . '" name="sircon-licenses[' . $args['theme'] . ']" value="' . $args['value'] . '" />';
					echo (get_option($args['theme'] . '-update') && get_option($args['theme'] . '-update') !== 'validated') ? '<span class="dashicons dashicons-warning" style="color:#ca4a1f;line-height:28px;"></span>' : '<span class="dashicons dashicons-yes" style="color:green;line-height:28px;"></span>';
				}, 'sircon-license', 'sircon-licenses-themes', ['label_for' => $theme . '-license', 'theme' => $theme, 'value' => $licenses[$theme] ?? '']);
			}
		});
	});

	add_filter('add_menu_classes', function ($menu) {
		global $SIRCON_PLUGINS, $SIRCON_THEMES;
		$errors = 0;
		foreach (array_merge($SIRCON_PLUGINS, $SIRCON_THEMES) as $slug) {
			if (get_option($slug . '-update') && get_option($slug . '-update') !== 'validated') {
				$errors++;
			}
		}

		if (!$errors) {
			return $menu;
		}

		foreach ($menu as $menu_key => $menu_data) {
			if ($menu_data[2] != 'sircon-license') {
				continue;
			}

			$menu[$menu_key][0] .= ' <span class="update-plugins"><span class="plugin-count">' . number_format_i18n($errors) . '</span></span>';
			break;
		}

		return $menu;
	}, 10, 1);
}

add_action('admin_notices', function () {
	if (get_option(basename(dirname(dirname(__FILE__))) . '-update') && get_option(basename(dirname(dirname(__FILE__))) . '-update') !== 'validated') {
		if (!empty($_GET['page']) && $_GET['page'] === 'sircon-license') {
			return;
		}

		echo '<div class="notice notice-warning">
			<p>' . f_('Invalid licensekey for') . ' ' . ucwords(str_replace('-', ' ', basename(dirname(dirname(__FILE__))))) . '</p>
			<p>' . f_('Update you license key here') . ': <a href="' . menu_page_url('sircon-license', false) . '">' . f_('Sircon Licenses') . '</a></p>
		</div>';
	}
});

add_filter('pre_set_site_transient_update_plugins', function ($transient) {
	$plugin = basename(dirname(dirname(__FILE__)));
	if (empty($transient->checked)) {
		return $transient;
	}

	$res = update_request('plugin_update', $plugin, $transient->checked[$plugin . '/index.php']);
	if (is_wp_error($res) || ($res['response']['code'] != 200)) {
		return $transient;
	}

	$response = json_decode($res['body']);
	if (empty($response) || !is_object($response)) {
		return $transient;
	}

	$response->icons = (array) $response->icons;
	if (version_compare($response->new_version, $transient->checked[$plugin . '/index.php']) == 1) {
		$transient->response[$plugin . '/index.php'] = $response;
	} else {
		$transient->no_update[$plugin . '/index.php'] = $response;
	}

	update_option($plugin . '-update', $response->update);
	return $transient;
});

add_filter('plugins_api', function ($result, $action, $args) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundBeforeLastUsed
	$plugin = basename(dirname(dirname(__FILE__)));
	if (empty($args->slug) || ($args->slug != $plugin)) {
		return $result;
	}

	$response = update_request('plugin_information', $plugin);
	if (is_wp_error($response)) {
		return new \WP_Error('plugins_api_failed', 'An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>', $response->get_error_message());
	}

	$result = json_decode($response['body']);
	if (empty($result) || !is_object($result)) {
		return new \WP_Error('plugins_api_failed', 'An unknown error occurred', $response['body']);
	}

	$result->sections = (array) $result->sections;
	$result->banners = (array) $result->banners;
	return $result;
}, 10, 3);
