<?php

namespace Sircon\Library\Updater;

use Sircon\Library\AdminNotice;

final class Licenser extends Updater {

	private static $instance = null;

	private static $themes = [];

	private static $plugins = [];

	public function __construct() {
		add_action('admin_menu', [$this, 'adminMenu']);
		add_action('admin_notices', [$this, 'adminNotices']);
	}

	public function adminMenu() {
		$sumenu_page = add_submenu_page('options-general.php', __('Sircon Licenses', 'sircon-library'), __('Sircon Licenses', 'sircon-library'), 'manage_options', 'sircon-library-license', function () {
			echo '<div class="wrap"><h1>' . __('Sircon Licenses', 'sircon-library') . '</h1><form method="post" action="options.php">';
			settings_fields('sircon-library-license');
			do_settings_sections('sircon-library-license');
			submit_button();
			echo '</form></div>';
		});

		add_action('load-' . $sumenu_page, function () {
			if (!empty($_GET['settings-updated']) && $_GET['settings-updated'] == 'true') {
				foreach (array_merge(self::$plugins, self::$themes) as $license) {
					switch ($license['type']) {
						case 'plugins':
							$res = $this->apiRequest("/plugin/{$license['slug']}/license", $license['slug'], $license['version']);
							break;

						case 'themes':
							$res = $this->apiRequest("/theme/{$license['slug']}/license", $license['slug'], $license['version']);
							break;

						default:
							continue 2;
					}

					if (is_wp_error($res) || ($res['response']['code'] != 200)) {
						continue;
					}

					$response = json_decode($res['body']);
					if (empty($response) || !is_object($response)) {
						continue;
					}

					update_option($license['slug'] . '-update', $response->update);
				}

				set_site_transient('update_plugins', null);
				set_site_transient('update_themes', null);
			}
		});

		add_action('admin_init', function () {
			register_setting('sircon-library-license', 'sircon-library-licenses');
			$licenses = get_option('sircon-library-licenses');

			if (self::$themes) {
				add_settings_section('sircon-licenses-themes', __('Themes', 'sircon-library'), '__return_false', 'sircon-library-license');
			}

			if (self::$plugins) {
				add_settings_section('sircon-licenses-plugins', __('Plugins', 'sircon-library'), '__return_false', 'sircon-library-license');
			}

			foreach (array_merge(self::$plugins, self::$themes) as $license) {
				/* translators: %s will be replaced by the plugin/theme name */
				add_settings_field($license['slug'], sprintf(__('%s license key', 'sircon-library'), $license['name']), function ($args) {
					echo '<input type="text" id="' . $args['slug'] . '" name="sircon-library-licenses[' . $args['slug'] . ']" value="' . $args['value'] . '" class="regular-text" />';
					if (get_option($args['slug'] . '-update') !== 'validated') {
						echo '<span class="dashicons dashicons-warning" style="color:#ca4a1f;line-height:28px;"></span>';
					} else {
						echo '<span class="dashicons dashicons-yes" style="color:green;line-height:28px;"></span>';
					}
				}, 'sircon-library-license', 'sircon-licenses-' . $license['type'], ['label_for' => $license['slug'] . '-license', 'slug' => $license['slug'], 'value' => $licenses[$license['slug']] ?? '']);
			}
		});
	}

	public function adminNotices() {
		foreach (array_merge(self::$plugins, self::$themes) as $license) {
			if (get_option($license['slug'] . '-update') !== 'validated') {
				/* translators: %s will be replaced by the plugin/theme name */
				$header = sprintf(__('Invalid license key for %s', 'sircon-library'), $license['name']);
				/* translators: %s will be replaced by a link to the license page */
				$body = sprintf(__('Update your license key here: %s', 'sircon-library'), sprintf('<a href="%s">%s</a>', menu_page_url('sircon-library-license', false), __('Sircon Licenses', 'sircon-library')));
				echo AdminNotice::warning(sprintf('%s<br /><small>%s</small>', $header, $body));
			}
		}
	}

	public static function enable(): void {
		self::$instance = self::$instance ?? new self();
	}

	public static function register(string $file): void {
		$slug = basename(dirname($file));
		$type = basename(dirname(dirname($file)));
		switch ($type) {
			case 'plugins':
				$data = get_file_data($file, ['Name' => 'Plugin Name', 'Version' => 'Version'], false);
				self::$plugins[] = [
					'slug' => $slug,
					'type' => $type,
					'name' => $data['Name'],
					'version' => $data['Version']
				];
				break;

			case 'themes':
				$data = get_file_data(dirname($file) . '/style.css', ['Name' => 'Theme Name', 'Version' => 'Version'], false);
				self::$themes[] = [
					'slug' => $slug,
					'type' => $type,
					'name' => $data['Name'],
					'version' => $data['Version']
				];
				break;

			default:
				return;
		}

		self::enable();
	}
}
