<?php namespace sircon;

class Userdata extends Form {

	private static $_fields = [];

	public static function init() : void {
		if (!is_admin()) {
			return;
		}
		
		add_action('show_user_profile', [__CLASS__ , 'output_fields'], 10, 1);
		add_action('edit_user_profile', [__CLASS__ , 'output_fields'], 10, 1);
		add_action('personal_options_update', [__CLASS__ , 'save_fields'], 10, 1);
		add_action('edit_user_profile_update', [__CLASS__ , 'save_fields'], 10, 1);
		
		add_action('admin_enqueue_scripts', function() {
			wp_enqueue_script('sircon-enabler');
			wp_enqueue_style('sircon-usermeta', Lib::get_lib_url('/styles/usermeta.css'));
		}, 0);
	}

	public static function add_userdata(array $fields) : void {
		foreach ($fields as $field) {
			$field['type'] = $field['type'] ?? 'text';
			self::$_fields[] = $field;
		}
	}

	public static function get_userdata(int $user_id, string $field) : string {
		$value = get_user_meta($user_id, $field, true);
		return (is_admin() || !is_string($value)) ? $value : do_shortcode($value);
	}

	public static function output_fields($user) {
		if (!self::$_fields) {
			return;
		}
		
		echo '<h2>'.__('Extra fields', 'sircon-lib').'</h2>';
		echo '<div class="sircon-usermeta">';
		foreach (self::$_fields as $field) {
			$field['prefix'] = '';
			$current_value = empty($field['name']) ? '' : self::get_userdata($user->ID, $field['name']);
			$enabled = (isset($field['enabler']) && $field['enabler']) ? self::get_userdata($user->ID, $field['name'].'-enabled') : false;
			parent::print_field($field, $current_value, $enabled);
		}
		
		echo '</div>';
	}


	public static function save_fields($user_id) {
		if (!current_user_can('edit_user', $user_id)) {
			return false;
		}

		foreach (self::$_fields as $field) {
			if (!isset($_POST[$field['name']]) && $field['type'] !== 'checkbox') {
				continue;
			}

			update_user_meta($user_id, $field['name'], $_POST[$field['name']] ?? '');

			if (!empty($field['enabler'])) {
				$enabled = isset($_POST[$field['name'].'-enabled']) ? 1 : 0;
				update_user_meta($user_id, $field['name'].'-enabled', $enabled);
			}
		}
	}
}
?>