<?php namespace sircon;

class Options extends Form {

	/**
	 * All option pages
	 * @var array
	 */
	private static $_option_pages = [];
	
	/**
	 * [private description]
	 * @var array
	 */
	private static $_multilingual = [];

	/**
	* Function executed when the class is loaded
	*/
	public static function init() : void {
		if (!is_admin()) {
			return;
		}

		add_action('admin_menu', [__CLASS__ , 'register_option_pages'], 5);
		add_action('admin_init', [__CLASS__ , 'register_settings'], 5);
		
		add_action('admin_enqueue_scripts', function() {
			wp_enqueue_script('sircon-enabler');
			wp_enqueue_script('sircon-option-tabs', Lib::get_lib_url('scripts/option-tabs.js'), ['jquery'], false, true);
			wp_enqueue_style('sircon-option-tabs', Lib::get_lib_url('styles/option-tabs.css'));
		}, 0);
	}

	/**
	 * Add a new options page
	 *
	 * @param string $id     [description]
	 * @param array  $params [description]
	 */
	public static function add_page(string $id, array $params = []) : void {
		self::$_multilingual[$id] = [];
		self::$_option_pages[$id] = [
			'menu_type' 	=> $params['type'] ?? '',
			'page_title' 	=> $params['title'] ?? $id,
			'menu_title' 	=> $params['menu_title'] ?? $params['title'] ?? $id,
			'capability' 	=> $params['capability'] ?? 'manage_options',
			'icon_url' 		=> $params['icon'] ?? '',
			'position'		=> $params['position'] ?? null,
			'parent'		=> $params['parent'] ?? null,
			'fields'		=> [
				[
					'label' => $params['tab_title'] ?? __('Main', 'sircon-lib'),
					'name' => $id,
					'type' => 'pagebreak',
					'class' => ' current-fieldset',
				],
			],
		];
	}

	/**
	 * [_has_page description]
	 *
	 * @param  string $id [description]
	 *
	 * @return bool       [description]
	 */
	private static function _has_page(string $id) : bool {
		return !empty(self::$_option_pages[$id]);
	}

	/**
	 * [_localize_field description]
	 *
	 * @param  string $options_page_id [description]
	 * @param  array  $field           [description]
	 *
	 * @return [type]                  [description]
	 */
	private static function _localize_field(string $options_page_id, array $field) {
		if (empty($field['multilingual']) || !$field['multilingual']) {
			return $field;
		}
		
		self::$_multilingual[$options_page_id][] = $field['name'];
		if (!function_exists('pll_current_language')) {
			return $field;
		}
		
		if (pll_current_language('slug') === pll_default_language('slug')) {
			return $field;
		}
		
		$field['name'] .= '_'.pll_current_language('slug');
		return $field;
	}

	/**
	 * [_get_localized_fieldname description]
	 *
	 * @param  string $options_page_id [description]
	 * @param  string $fieldname       [description]
	 *
	 * @return string                  [description]
	 */
	private static function _get_localized_fieldname(string $options_page_id, string $fieldname) : string {
		if (empty(self::$_multilingual[$options_page_id]) || !in_array($fieldname, self::$_multilingual[$options_page_id])) {
			return $fieldname;
		}
		
		if (!function_exists('pll_current_language')) {
			return $fieldname;
		}
		
		if (pll_current_language('slug') === pll_default_language('slug')) {
			return $fieldname;
		}
		
		return $fieldname.'_'.pll_current_language('slug');
	}

	/**
	 * [add_options description]
	 *
	 * @param string $options_page_id [description]
	 * @param array  $fields          [description]
	 */
	public static function add_options(string $options_page_id, array $fields) : void {
		foreach ($fields as $field) {
			self::add_option($options_page_id, $field);
		}
	}

	/**
	 * [add_option description]
	 *
	 * @param string $options_page_id [description]
	 * @param array  $field           [description]
	 */
	public static function add_option(string $options_page_id, array $field) : void {
		$field['prefix'] = $options_page_id.'_';
		self::$_option_pages[$options_page_id]['fields'][] = self::_localize_field($options_page_id, $field);
	}

	/**
	 * [get_option description]
	 *
	 * @param  string $options_page_id [description]
	 * @param  string $field           [description]
	 *
	 * @return string                  [description]
	 */
	public static function get_option(string $options_page_id, string $field) : string {
		$field = self::_get_localized_fieldname($options_page_id, $field);
		$value = get_option($options_page_id.'_'.$field, '');
		if (is_array($value)) {
			return json_encode($value) ?? '';
		}
		
		return $value;
	}

	/**
	 * [add_option_pages description]
	 */
	public static function register_option_pages() : void {
		foreach (self::$_option_pages as $menu_slug => $params) {
			switch($params['menu_type']) {
				case 'tools':
				case 'management':
					add_management_page($params['page_title'], $params['menu_title'], $params['capability'], $menu_slug, [__CLASS__, 'option_page_output']);
					break;

				case 'theme':
					add_theme_page($params['page_title'], $params['menu_title'], $params['capability'], $menu_slug, [__CLASS__, 'option_page_output']);
					break;

				case 'options':
				case 'option':
					add_options_page($params['page_title'], $params['menu_title'], $params['capability'], $menu_slug, [__CLASS__, 'option_page_output']);
					break;

				case 'submenu':
				case 'subpage':
					add_submenu_page($params['parent'], $params['page_title'], $params['menu_title'], $params['capability'], $menu_slug, [__CLASS__, 'option_page_output']);
					break;

				default:
					add_menu_page($params['page_title'], $params['menu_title'], $params['capability'], $menu_slug, [__CLASS__, 'option_page_output'], $params['icon_url'], $params['position']);
					break;
			}
		}
	}

	/**
	 * [register_settings description]
	 */
	public static function register_settings() : void {
		foreach (self::$_option_pages as $options_page_id => $params) {
			foreach ($params['fields'] as $field) {
				if (empty($field['name'])) {
					continue;
				}

				register_setting($options_page_id, $options_page_id.'_'.$field['name']);
				if (!empty($field['enabler'])) {
					register_setting($options_page_id, $options_page_id.'_'.$field['name'].'-enabled');
				}
			}
		}
	}

	/**
	 * [option_page_output description]
	 */
	public static function option_page_output() : void {
		$options_page_id = $_GET['page'];
		if (!self::_has_page($options_page_id)) {
			return;
		}
		
		$params = self::$_option_pages[$options_page_id];
		$fields = self::$_option_pages[$options_page_id]['fields'] ?? [];
		if (!empty($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') {
			do_action('save_sirconoptions_'.$options_page_id, $fields);
		}
		
		echo '<div class="wrap"><h1>'.$params['page_title'].'</h1><form method="post" action="options.php"><div class="sircon-option-tabs">';
			echo '<div class="fieldset-content">';
			settings_fields($options_page_id);
			foreach ($fields as $field) {
				$enabled = !empty($field['enabler']) && self::get_option($options_page_id, $field['name'].'-enabled');
				$current_value = !empty($field['name']) ? self::get_option($options_page_id, $field['name']) : '';
				parent::print_field($field, $current_value, $enabled);
			}
			
			submit_button();
			echo '</div>';
		echo '</div>';
		echo '</form></div>';
	}
}
?>