<?php namespace sircon;

/**
* [SirconPostdata description]
*/
class Taxdata extends Form {

	private static $_taxfields = [];

	/**
	* [init description]
	*/
	public static function init() : void {
		if (!is_admin()) {
			return;
		}
		
		add_action('admin_init', [__CLASS__ , 'add_taxmeta_actions'], 200);
		add_action('created_term', [__CLASS__ , 'save_fields'], 10, 3);
		add_action('edit_term', [__CLASS__ , 'save_fields'], 10, 3);
		
		add_action('admin_enqueue_scripts', function() {
			wp_enqueue_script('sircon-enabler');
			wp_enqueue_style('sircon-taxmeta', Lib::get_lib_url('styles/taxmeta.css'));
			wp_enqueue_script('sircon-taxmeta', Lib::get_lib_url('scripts/taxmeta.js'), ['jquery'], false, true);
		}, 0);
	}

	public static function add_taxdata(string $taxonomy, array $fields) : void {
		foreach ($fields as $field) {
			$field['type'] = $field['type'] ?? 'text';
			self::$_taxfields[$taxonomy][] = $field;
		}
	}

	public static function get_taxdata(int $term_id, string $field) : string {
		$value = get_term_meta($term_id, parent::NAME_PREFIX.$field, true);
		return (is_admin() || !is_string($value)) ? $value : do_shortcode($value);
	}

	public static function get_taxdatas(int $term_id) : array {
		$taxonomy = get_term($term_id)->taxonomy;

		if (empty(self::$_taxfields[$taxonomy])) {
			return [];
		}

		$return_fields = [];

		foreach (self::$_taxfields[$taxonomy] as $field) {
			if ($field['type'] === 'custom' || empty($field['name'])) {
				continue;
			}

			$return_fields[$field['name']] = self::get_taxdata($term_id, $field['name']);
			if (isset($field['enabler']) && $field['enabler']) {
				$return_fields[$field['name'].'-enabled'] = self::get_taxdata($term_id, $field['name'].'-enabled');
			}
		}

		return $return_fields;
	}

	public static function add_taxmeta_actions() {
		foreach (array_keys(self::$_taxfields) as $taxonomy) {
			add_action($taxonomy.'_add_form_fields', [__CLASS__ , 'output_taxfields'], 10, 1);
			add_action($taxonomy.'_edit_form', [__CLASS__ , 'output_edit_taxfields'], 10, 2);
		}
	}

	public static function output_taxfields($taxonomy) {
		if (empty(self::$_taxfields[$taxonomy])) {
			return;
		}
		
		echo '<div class="sircon-taxmeta-add">';
		foreach (self::$_taxfields[$taxonomy] as $field) {
			parent::print_field($field);
		}
		
		echo '</div>';
	}

	public static function output_edit_taxfields($tag, $taxonomy) {
		if (empty(self::$_taxfields[$taxonomy])) {
			return;
		}
		
		echo '<div class="sircon-taxmeta">';
		foreach (self::$_taxfields[$taxonomy] as $field) {
			$current_value = empty($field['name']) ? '' : self::get_taxdata($tag->term_id, $field['name']);
			$enabled = (isset($field['enabler']) && $field['enabler']) ? self::get_taxdata($tag->term_id, $field['name'].'-enabled') : false;
			parent::print_field($field, $current_value, $enabled);
		}
		
		echo '</div>';
	}

	public static function save_fields($term_id, $tt_id, $taxonomy) {
		if (empty(self::$_taxfields[$taxonomy])) {
			return;
		}

		foreach (self::$_taxfields[$taxonomy] as $field) {
			$fieldname = parent::NAME_PREFIX.$field['name'];
			if (!isset($_POST[$fieldname]) && $field['type'] !== 'checkbox') {
				continue;
			}

			update_term_meta($term_id, $fieldname, $_POST[$fieldname] ?? '');

			if (!empty($field['enabler'])) {
				$enabled = isset($_POST[$fieldname.'-enabled']) ? 1 : 0;
				update_term_meta($term_id, $fieldname.'-enabled', $enabled);
			}
		}
	}
}
?>