<?php namespace sircon;

/**
 * [SirconPostdata description]
 */
class Postdata extends Form {

	/**
	* [private description]
	*
	* @var [type]
	*/
	private static $_metaboxes = [];

	/**
	* [private description]
	*
	* @var [type]
	*/
	private static $_metabox_order = [];

	/**
	* [init description]
	*/
	public static function init() : void {
		add_action('add_meta_boxes', [__CLASS__ , 'add_meta_boxes']);
		add_action('save_post', [__CLASS__ , 'save_meta']);
		
		add_action('admin_enqueue_scripts', function() {
			wp_enqueue_script('sircon-enabler');
		}, 0);
	}

	/**
	* [add_postbox description]
	*
	* @param string $postbox_id [description]
	* @param array  $metabox    [description]
	*/
	public static function add_postbox(string $postbox_id, array $metabox = []) : void {
		$metabox['title'] = $metabox['title'] ?? __('Custom Fields', 'sircon-lib');
		$metabox['post_type_in'] = $metabox['post_type_in'] ?? ['post', 'page'];
		$metabox['context'] = $metabox['context'] ?? 'normal';
		$metabox['fields'] = [];

		self::$_metaboxes[$postbox_id] 	= $metabox;
		self::$_metabox_order[] 			= $postbox_id;
	}

	/**
	* [has_postbox description]
	*
	* @param  string $postbox_id [description]
	*
	* @return bool               [description]
	*/
	public static function has_postbox(string $postbox_id) : bool {
		return isset(self::$_metaboxes[$postbox_id]);
	}

	/**
	* [add_postdata description]
	*
	* @param string $postbox_id [description]
	*
	* @param array  $fields     [description]
	*/
	public static function add_postdata(string $postbox_id, array $fields) : void {
		foreach ($fields as $field) {
			$field['type'] = $field['type'] ?? 'text';
			if (!self::has_postbox($postbox_id)) {
				self::add_postbox($postbox_id);
			}
			
			self::$_metaboxes[$postbox_id]['fields'][] = $field;
		}
	}

	/**
	* [get_postdata description]
	*
	* @param  int    $post_id [description]
	* @param  string $field   [description]
	*
	* @return mixed          [description]
	*/
	public static function get_postdata(int $post_id, string $field) {
		$value = get_post_meta($post_id, parent::NAME_PREFIX.$field, true);
		return (is_admin() || !is_string($value)) ? $value : do_shortcode($value);
	}

	/**
	* [get_postdatas description]
	*
	* @param  int   $post_id [description]
	*
	* @return array          [description]
	*/
	public static function get_postdatas(int $post_id) : array {
		$return_fields = [];

		foreach (self::$_metaboxes as $metabox_id => $metabox) {
			if (!self::_metabox_visible_on_post($metabox_id, $post_id)) {
				continue;
			}

			foreach ($metabox['fields'] as $field) {
				if ($field['type'] === 'custom' || empty($field['name'])) {
					continue;
				}

				if (!self::_field_visible_on_post($field, $post_id)) {
					continue;
				}

				$return_fields[$field['name']] = self::get_postdata($post_id, $field['name']);
				if (isset($field['enabler']) && $field['enabler']) {
					$return_fields[$field['name'].'-enabled'] = self::get_postdata($post_id, $field['name'].'-enabled');
				}
			}
		}
		
		return $return_fields;
	}


	/**
	 * [_metabox_visible_on_post description]
	 *
	 * @param  string $metabox_id [description]
	 * @param  int    $post_id    [description]
	 *
	 * @return bool               [description]
	 */
	private static function _metabox_visible_on_post(string $metabox_id, int $post_id) : bool {
		return in_array(get_post_type($post_id), self::$_metaboxes[$metabox_id]['post_type_in']);
	}

	/**
	 * [_field_visible_on_post description]
	 *
	 * @param  array $field   [description]
	 * @param  int   $post_id [description]
	 *
	 * @return bool           [description]
	 */
	private static function _field_visible_on_post(array $field, int $post_id) : bool {
		return empty($field['post_type_in']) || in_array(get_post_type($post_id), $field['post_type_in']);
	}

	/**
	 * [_metabox_has_fields_on_post description]
	 *
	 * @param  string $metabox_id [description]
	 * @param  int    $post_id    [description]
	 *
	 * @return bool               [description]
	 */
	private static function _metabox_has_fields_on_post(string $metabox_id, int $post_id) : bool {
		if (!self::_metabox_visible_on_post($metabox_id, $post_id)) {
			return false;
		}
		
		foreach (self::$_metaboxes[$metabox_id]['fields'] as $field) {
			if (self::_field_visible_on_post($field, $post_id)) {
				return true;
			}
		}
		
		return false;
	}

	/**
	 * [add_meta_boxes description]
	 */
	public static function add_meta_boxes() : void {
		foreach (self::$_metabox_order as $metabox_id) {
			if (!self::_metabox_has_fields_on_post($metabox_id, get_the_ID())) {
				continue;
			}
			
			add_meta_box(parent::NAME_PREFIX.$metabox_id, self::$_metaboxes[$metabox_id]['title'], [__CLASS__ , 'metabox_output'], get_post_type(), self::$_metaboxes[$metabox_id]['context']);
		}
	}

	/**
	 * [metabox_output description]
	 *
	 * @param WP_Post $post   [description]
	 *
	 * @param array   $params [description]
	 */
	public static function metabox_output(\WP_Post $post, array $params) : void {
		echo '<fieldset>';
		$metabox_id = str_replace(parent::NAME_PREFIX, '', $params['id']);
		foreach (self::$_metaboxes[$metabox_id]['fields'] as $field) {
			if (!self::_field_visible_on_post($field, $post->ID)) {
				continue;
			}
			
			self::_field_output($field, $post->ID);
		}
		
		echo '</fieldset>';
	}

	/**
	 * [_field_output description]
	 *
	 * @param  array  $field   [description]
	 * @param  int    $post_id [description]
	 */
	private static function _field_output(array $field, int $post_id) : void {
		$current_value = empty($field['name']) ? '' : self::get_postdata($post_id, $field['name']) ?? '';
		$enabled = (isset($field['enabler']) && $field['enabler']) ? self::get_postdata($post_id, $field['name'].'-enabled') : false;
		parent::print_field($field, $current_value, $enabled);
	}

	/**
	 * [save_meta description]
	 *
	 * @param int $post_id [description]
	 */
	public static function save_meta(int $post_id) : void {

		foreach (self::$_metaboxes as $metabox_id => $metabox) {
			if (!self::_metabox_visible_on_post($metabox_id, $post_id)) {
				continue;
			}

			foreach ($metabox['fields'] as $field) {
				if ($field['type'] === 'custom' || empty($field['name'])) {
					continue;
				}
				
				if (!self::_field_visible_on_post($field, $post_id)) {
					continue;
				}

				$fieldname = parent::NAME_PREFIX.$field['name'];
				if (!isset($_POST[$fieldname]) && $field['type'] !== 'checkbox') {
					continue;
				}

				update_post_meta($post_id, $fieldname, $_POST[$fieldname] ?? '');

				if (!empty($field['enabler'])) {
					$enabled = isset($_POST[$fieldname.'-enabled']) ? 1 : 0;
					update_post_meta($post_id, $fieldname.'-enabled', $enabled);
				}
			}
		}
	}

	/**
	 * [get_postids_with_postdata description]
	 *
	 * @param  string $field [description]
	 * @param  string $value [description]
	 *
	 * @return array         [description]
	 */
	public static function get_postids_with_postdata(string $field, string $value) : array {
		global $wpdb;
		$prefixed_name = parent::NAME_PREFIX.$fieldname;
		$sql = $wpdb->prepare('SELECT post_id FROM '.$wpdb->postmeta.' WHERE meta_key LIKE %s AND meta_value LIKE %s', parent::NAME_PREFIX.$field, $value);
		return $wpdb->get_results($sql);
	}
}

?>