<?php

namespace Sircon\Library;

use Sircon\Library\Formfield\Formfield;

class Userdata {

	private static $fields = [];

	public static function setup(): void {
		add_action('show_user_profile', [__CLASS__ , 'outputFields'], 10, 1);
		add_action('edit_user_profile', [__CLASS__ , 'outputFields'], 10, 1);
		add_action('personal_options_update', [__CLASS__ , 'saveFields'], 10, 1);
		add_action('edit_user_profile_update', [__CLASS__ , 'saveFields'], 10, 1);
	}

	public static function add(Formfield $Field): void {
		self::$fields[] = $Field;
	}

	public static function get(int $user_id, string $field): string {
		return get_user_meta($user_id, $field, true);
	}

	public static function has(int $user_id, string $field): bool {
		return metadata_exists('user', $user_id, $field);
	}

	public static function outputFields($user) {
		if (!self::$fields) {
			return;
		}

		echo '<h2>' . __('Extra fields', 'sircon-library') . '</h2>';
		echo '<table class="form-table">';
		foreach (self::$fields as $Field) {
			if ($Field->isSaveable()) {
				$Field->setTableLayout(true);
				if (self::has($user->ID, $Field->getName())) {
					$Field->setValue(self::get($user->ID, $Field->getName()));
				}
			}

			$Field->output();
		}

		echo '</table>';
	}

	public static function saveFields($user_id) {
		if (!current_user_can('edit_user', $user_id)) {
			return false;
		}

		foreach (self::$fields as $Field) {
			if ($Field->isSaveable()) {
				update_user_meta($user_id, $Field->getName(), filter_input(INPUT_POST, $Field->getName()));
			}
		}
	}
}

if (is_admin()) {
	Userdata::setup();
}
