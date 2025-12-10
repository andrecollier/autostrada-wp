<?php namespace sircon;

class Form {

	/**
	 * Database prefix
	 * @var string
	 */
	protected const NAME_PREFIX = 'sircon_';

	/**
	 * Function executed when the class is loaded
	 */
	public static function init() : void {
		if (is_admin()) {
			add_action('admin_enqueue_scripts', function() {
				self::_register_and_enqueue();
			}, 0);
		}
	}

	/**
	 * Call this to enqueue scripts and styles used by this class
	 */
	public static function enable_frontend_support() : void {
		add_action('wp_enqueue_scripts', function() {
			self::_register_and_enqueue();
		}, 0);
	}

	/**
	 * Register available scripts and enqueue default styles
	 */
	private static function _register_and_enqueue() : void {
		wp_register_script('rubaxa-sortable', Lib::get_lib_url('scripts/rubaxa-sortable.js'), ['jquery'], false, true);
		wp_register_script('sircon-multifield', Lib::get_lib_url('scripts/multifields.js'), ['jquery', 'rubaxa-sortable'], false, true);
		wp_register_script('wp-color-picker-change', Lib::get_lib_url('scripts/wp-color-picker-change.js'), ['jquery', 'wp-color-picker', 'underscore'], false, true);
		wp_register_script('sircon-image-select', Lib::get_lib_url('scripts/image-select.js'), ['jquery'], false, true);
		wp_register_script('sircon-file-select', Lib::get_lib_url('scripts/file-select.js'), ['jquery'], false, true);
		wp_register_script('sircon-rangeslider', Lib::get_lib_url('scripts/rangeslider.js'), ['jquery'], false, true);
		wp_register_script('sircon-flatpickr', Lib::get_lib_url('scripts/flatpickr.js'), ['jquery'], false, true);
		wp_register_script('sircon-datepicker', Lib::get_lib_url('scripts/datepicker.js'), ['sircon-flatpickr'], false, true);

		wp_register_style('sircon-multifield', Lib::get_lib_url('styles/multifields.css'));
		wp_register_style('sircon-image-select', Lib::get_lib_url('styles/image-select.css'));
		wp_register_style('sircon-rangeslider', Lib::get_lib_url('styles/rangeslider.css'));
		wp_register_style('sircon-flatpickr', Lib::get_lib_url('styles/flatpickr.css'));

		wp_enqueue_style('sircon-formelements', Lib::get_lib_url('styles/formelements.css'));
	}

	/**
	 * Print a single field
	 *
	 * @param array   $field         Field array
	 * @param string  $current_value The current field value
	 * @param boolean $enabled       True if the field-enabler should be checked
	 */
	public static function print_field(array $field, string $current_value = '', $enabled = false) : void {
		echo self::get_field($field, $current_value, $enabled);
	}

	/**
	 * Get the output for a single field
	 *
	 * @param  array   $field         The field array
	 * @param  string  $current_value The current field value
	 * @param  boolean $enabled       True if the field-enabler should be checked
	 *
	 * @return string                 The completed HTML element for the field
	 */
	public static function get_field(array $field, string $current_value = '', bool $enabled = false) : string {
		// Require name if field is not of type [custom]
		if (empty($field['name']) && $field['type'] !== 'custom') {
			return '<div><strong>ERROR: Missing parameter "name" for a field of type "'.$field['type'].'"</strong></div>';
		}

		//Set default values
		$field['type'] 			= $field['type'] ?? 'text';
		$field['name'] 			= $field['name'] ?? 'custom-field-'.rand();
		$field['value'] 		= $field['value'] ?? '';
		$field['default'] 		= $field['default'] ?? '';
		$field['label'] 		= $field['label'] ?? '';
		$field['tooltip'] 		= $field['tooltip'] ?? '';
		$field['placeholder'] 	= $field['placeholder'] ?? '';
		$field['options'] 		= $field['options'] ?? [];
		$field['is_multifield'] = $field['is_multifield'] ?? false;
		$field['multiple'] 		= $field['multiple'] ?? false;
		$field['classes']		= $field['classes'] ?? [];
		$field['disabled']		= $field['disabled'] ?? false;
		$field['prefix']		= $field['prefix'] ?? ($field['is_multifield'] ? '' : self::NAME_PREFIX);

		$current_value = ($current_value === '' && $field['type'] !== 'checkbox') ? $field['default'] : htmlentities($current_value);

		//Prepare attributes
		$attr_id	= ' id="'.$field['prefix'].$field['name'].'"';
		$attr_for	= ' for="'.$field['prefix'].$field['name'].'"';
		$attr_type	= ' type="'.$field['type'].'"';
		$attr_name	= ' name="'.$field['prefix'].$field['name'].'"';
		$attr_value	= ' value="'.$current_value.'"';

		//Override if in multifield
		if ($field['is_multifield']) {
			$attr_id = '';
			$attr_for = '';
			$field['classes'][] = 'field-'.$field['prefix'].$field['name'];
			$field['classes'][] = 'is-multifield';
			$attr_name = ' data-name="'.$field['prefix'].$field['name'].'"';
		}

		//Add default classes
		$field['classes'][] = 'formfield';
		$field['classes'][] = 'fieldtype-'.$field['type'];

		$attr_class	= ' class="'.implode(' ', $field['classes']).'"';
		$attr_disabled = $field['disabled'] ? ' disabled="disabled"' : '';
		$element_label = $field['label'] ? '<label '.$attr_for.'>'.$field['label'].'</label>' : '';
		$tooltip = $field['tooltip'] ? '<div class="tooltip tooltip-for-'.$field['prefix'].$field['name'].'"><div class="tooltip-content">'.$field['tooltip'].'</div></div>' : '';
		$placeholder = $field['placeholder'] ? ' placeholder="'.$field['placeholder'].'"' : '';


		$enabler = '';
		if (!empty($field['enabler'])) {
			$enabler_label = is_string($field['enabler']) ? $field['enabler'] : __('Enable', 'sircon-lib');
			$enabler_name = $field['prefix'].$field['name'].'-enabled';
			$enabler = '<label for="'.$enabler_name.'">'.$enabler_label.'</label>
				<input type="checkbox" class="sircon-enabler" id="'.$enabler_name.'" name="'.$enabler_name.'"'.($enabled ? ' checked="checked" ' : '').' />
				<div class="enabler"></div>';
		}

		$element_styles = '';
		if (!empty($field['style'])) {
			$element_styles = '<style>'.str_replace('##', '#'.$field['prefix'].$field['name'], $field['style']).'</style>';
		}

		switch($field['type']){
			case 'text':
			case 'password':
				$element = $element_label;
				$element .= '<input'.$attr_id.$attr_type.$attr_disabled.$attr_name.$attr_value.$placeholder.' />';
				break;

			case 'slider':
			case 'range':
				self::load_rangeslider();
				$element = $element_label;
				$min = (!empty($field['range']['min'])) ? ' min="'.$field['range']['min'].'"' : '';
				$max = (!empty($field['range']['max'])) ? ' max="'.$field['range']['max'].'"' : '';
				$step = (!empty($field['range']['step'])) ? ' step="'.$field['range']['step'].'"' : '';
				$suffix = $field['suffix'] ? '<span class="suffix">'.$field['suffix'].'</span>' : '';

				$element .= '<input type="range" class="sircon-rangeslider"'.$min.$max.$step.$attr_name.$attr_id.$attr_value.' />';
				$element .= '<div class="outputwrap"><span class="output">'.$current_value.'</span>'.$suffix.'</div>';
				break;

			case 'date':
				self::load_flatpickr();
				$datepicker_config = [
					'locale'		=> $field['locale'] ?? 'en',
					'dateFormat' 	=> $field['dateformat'] ?? 'Y-m-d',
					'altFormat' 	=> $field['viewformat'] ?? 'F j, Y',
				];

				if ($current_value) {
					$datepicker_config['defaultDate'] = $current_value;
				}

				if (!empty($field['viewformat'])) {
					$datepicker_config['altInput'] = true;
				}

				if (!empty($field['min'])) {
					$datepicker_config['minDate'] = $field['min'];
				}

				if (!empty($field['max'])) {
					$datepicker_config['maxDate'] = $field['max'];
				}

				$element = $element_label;
				$element .= '<input type="text" class="sircon-datepicker"'.$attr_id.$attr_name.$attr_value.$placeholder.' data-config="'.htmlentities(json_encode($datepicker_config)).'"/>';
				break;

			case 'time':
				self::load_flatpickr();
				$datepicker_config = [
					'locale'		=> $field['locale'] ?? 'en',
					'enableTime'	=> true,
					'noCalendar' 	=> true,
					'time_24hr'		=> true,
					'dateFormat' 	=> $field['timeformat'] ?? 'H:i',
				];

				if ($current_value) {
					$datepicker_config['defaultDate'] = $current_value;
				}

				if (!empty($field['min'])) {
					$datepicker_config['minTime'] = $field['min'];
				}

				if (!empty($field['max'])) {
					$datepicker_config['maxTime'] = $field['max'];
				}

				$element = $element_label;
				$element .= '<input type="text" class="sircon-datepicker"'.$attr_id.$attr_name.$attr_value.$placeholder.' data-config="'.htmlentities(json_encode($datepicker_config)).'"/>';
				break;

			case 'datetime':
				self::load_flatpickr();
				$datepicker_config = [
					'locale'		=> $field['locale'] ?? 'en',
					'enableTime'	=> true,
					'time_24hr'		=> true,
					'dateFormat' 	=> $field['dateformat'] ?? 'Y-m-d H:i',
					'altFormat' 	=> $field['viewformat'] ?? 'F j, Y',
				];

				if ($current_value) {
					$datepicker_config['defaultDate'] 	= $current_value;
				}

				if (!empty($field['viewformat'])) {
					$datepicker_config['altInput'] = true;
				}

				if (!empty($field['min'])) {
					$datepicker_config['minDate'] = $field['min'];
				}

				if (!empty($field['max'])) {
					$datepicker_config['maxDate'] = $field['max'];
				}

				if (!empty($field['min_time'])) {
					$datepicker_config['minTime'] = $field['min_time'];
				}

				if (!empty($field['max_time'])) {
					$datepicker_config['maxTime'] = $field['max_time'];
				}

				$element = $element_label;
				$element .= '<input type="text" class="sircon-datepicker"'.$attr_id.$attr_name.$attr_value.$placeholder.' data-config="'.htmlentities(json_encode($datepicker_config)).'"/>';
				break;

			case 'hidden':
				if ($current_value === '') {
					$attr_value	= ' value="'.$field['value'].'"';
				}

				$element = $element_label;
				$element .= '<input'.$attr_id.$attr_type.$attr_name.$attr_value.' />';
				break;

			case 'radio':
				$element = '';
				$current_found = false;
				$count = 1;
				foreach ($field['options'] as $optionlabel => $option_value) {
					$current_option = ($option_value == $current_value) ? ' checked="checked" ' : '';
					if (!$field['is_multifield']) {
						$attr_id = ' id="'.$field['prefix'].$field['name'].'-'.$count.'"';
						$attr_for = ' for="'.$field['prefix'].$field['name'].'-'.$count.'"';
					}

					$attr_value = ' value="'.$option_value.'"';
					$element .= '<div class="sircon-radio" >';
					$element .= '<input '.$attr_id.$attr_type.$attr_name.$current_option.$attr_value.' />';
					$element .= '<label '.$attr_for.' class="alignleft">'.$optionlabel.'</label>';
					$element .= '</div>';
					if ($current_option) {
						$current_found = true;
					}

					$count++;
				}

				if (!$current_found && $field['default']) {
					$element = str_replace(' value="'.$field['default'].'"', ' checked="checked"  value="'.$field['default'].'"', $element);
				}
				break;

			case 'checkbox':
				$field['value'] = $field['value'] ?? 'on';
				$attr_value	= ' value="'.$field['value'].'"';
				$element = $element_label;
				$element .= '<input'.$attr_id.$attr_type.$attr_name.$attr_value;
				if ($current_value === $field['value']) {
					$element .= ' checked="checked" ';
				}

				$element .= ' />';
				break;

			case 'select':
				$multiple = $field['multiple'] ? ' multiple="multiple"' : '';
				if ($field['multiple']) {
					$attr_name = ' '.($field['is_multifield'] ? 'data-' : '').'name="'.$field['prefix'].$field['name'].'[]"';
					$current_value = json_decode(html_entity_decode($current_value));
				}

				$value_is_array = is_array($current_value);

				$element = $element_label;
				$element .= '<select'.$attr_id.$attr_name.$multiple.'>';

				foreach ($field['options'] as $optionlabel => $option_value) {
					$attr_value = ' value="'.$option_value.'"';
					if ($value_is_array) {
						$current_option = (in_array($option_value, $current_value)) ? ' selected="selected" ' : '';
					} else {
						$current_option = ($option_value == $current_value) ? ' selected="selected" ' : '';
					}

					$element .= '<option'.$current_option.$attr_value.'>'.$optionlabel.'</option>';
				}

				$element .= '</select>';
				break;

			case 'textarea':
				$element = $element_label;
				$element .= '<textarea'.$attr_id.$attr_name.$placeholder.'>'.$current_value.'</textarea>';
				break;

			case 'image':
				self::load_imageselect();
				$image_type = null;
				$image_src = null;
				$image_output = null;
				$image_field = '';
				if ($current_value) {
					$image_type = get_post_mime_type($current_value);
					if ($image_type === 'image/svg+xml') {
						$image_output = file_get_contents(get_attached_file($current_value));
					} else {
						$image_src = wp_get_attachment_image_url($current_value);
						$image_output = wp_get_attachment_image($current_value);
					}
				}

				ob_start(); ?><div class="image-select-wrapper">
					<div class="image-preview-buttons">
						<button class="button button-primary" onclick="sirconImageSelect(this);return false;"><?php echo __('Select/upload image', 'sircon-lib')?></button>
						<button class="button" onclick="sirconImageDeselect(this);return false;"><?php echo __('Remove', 'sircon-lib')?></button>
					</div>
					<div class="custom-img-preview"<?php echo $image_src ? ' style="background-image: url('.$image_src.');"' : ''?>>
						<?php if ($image_output) {
							echo $image_output;
						}?>
					</div>
				</div><?php
				$image_field = ob_get_clean();
				$element = $element_label.'<input type="hidden"'.$attr_id.$attr_name.$attr_value.' />'.$image_field;
				break;

			case 'file':
				self::load_fileselect();
				ob_start(); ?><div class="file-select-wrapper">
					<div class="file-preview-buttons">
						<button class="button button-primary" onclick="sirconFileSelect(this);return false;"><?php echo __('Select/upload file', 'sircon-lib')?></button>
						<button class="button" onclick="sirconFileDeselect(this);return false;"><?php echo __('Remove', 'sircon-lib')?></button>
					</div>
					<div class="file-preview-name">
						<?php if ($current_value) {
							echo '<a href="'.wp_get_attachment_url($current_value).'" target="_blank">'.basename(get_attached_file($current_value)).'</a>';
						}?>
					</div>
				</div><?php
				$image_field = ob_get_clean();
				$element = $element_label.'<input type="hidden"'.$attr_id.$attr_name.$attr_value.' />'.$image_field;
				break;

			case 'color': case 'colour': case 'colorpicker': case 'colourpicker':
				self::load_colorpicker();
				$element = $element_label.'<input class="wp-sircon-colorpicker" type="text"'.$attr_id.$attr_name.$attr_value.' data-default-color="#eee" />';
				break;

			case 'editor':

				$editor_args['wpautop'] = false;

				if ($field['is_multifield']) {
					$element = $element_label;
					$element .= '<textarea'.$attr_id.$attr_name.$placeholder.'>'.$current_value.'</textarea>';
				} else {
					ob_start();
					if ($field['label']) {
						echo '<h2>'.$field['label'].'</h2>';
					}

					wp_editor(html_entity_decode($current_value), $field['prefix'].$field['name'], [
						'wpautop' => false,
						'textarea_name' => $field['prefix'].$field['name'],
					]);
					$element = ob_get_clean();
				}
				break;

			case 'multiple':
				if (empty($field['template'])) {
					return '<div><strong>ERROR: missing param: Field "multiple" is missing param "template"</strong></div>';
				}

				if (is_string($current_value) && $current_value) {
					$current_value = (html_entity_decode($current_value));
					$current_value = json_decode($current_value, true);
				} elseif (!is_array($current_value)) {
					$first_empty_val = [];
					foreach ($field['template'] as $tfield) {
						$first_empty_val[$tfield['name']] = '';
					}

					$current_value = [$first_empty_val];
				}

				$sortable		= empty($field['sortable']) ? '' : ' data-sortable="1"';
				$sortable_class	= empty($field['sortable']) ? '' : ' sortable';
				$adder_label	= $field['adder_label'] ?? '+1 '.__('Add row', 'sircon-lib');

				$multifield_remover = '<div class="multifield-modifier remove-row dashicons dashicons-dismiss" data-sircon="multifield-modifier" data-action="remove"></div>';
				$multifield_sorter	= '<div class="multifield-modifier sort-row" data-sortgrab="1"><span class="dashicons dashicons-sort"></span></div>';
				$multifield_adder	= '<div class="multifield-modifier add-row" data-sircon="multifield-modifier" data-action="add"><span class="adder-label">'.$adder_label.'</span></div>';

				$element = $element_label;
				$element .= '<div class="multifieldwrap'.$sortable_class.'"'.$attr_id.$sortable.'>';
				$element .= '<div class="sortables">';

				foreach ($current_value as $row_values) {
					$element .= '<div class="multifield-row">'."\n".$multifield_remover.$multifield_sorter;
					foreach ($field['template'] as $tfield) {
						$tfield['is_multifield'] = true;
						unset($tfield['enabler']);
						$current_tfield_value = $row_values[$tfield['name']] ?? $row_values[$tfield['name'].'[]'] ?? '';
						if (is_array($current_tfield_value)) {
							$current_tfield_value = json_encode($current_tfield_value);
						}

						$tfield_output = self::get_field($tfield, $current_tfield_value ?? '');
						$element .= $tfield_output;
					}

					$element .= '</div>';
				}

				$element .= '</div><!-- .sortables -->';

				$element .= '<div class="multifield-row-template">'."\n".$multifield_remover.$multifield_sorter;
					foreach ($field['template'] as $tfield) {
						$tfield['is_multifield'] = true;
						unset($tfield['enabler']);
						$tfield_output = self::get_field($tfield);
						$element .= $tfield_output;
					}

				$element .= '</div>';
				$element .= $multifield_adder;

				if (strpos($attr_value, 'value="Array"') !== false) {
					$attr_value = ' value="'.htmlentities(json_encode($current_value)).'"';
				}

				$element .= '<input type="hidden" class="multifield-json"'.$attr_name.$attr_value.' />';
				$element .= '</div><!-- .multifieldwrap -->';

				self::add_form_multifield_support();
				break;

			case 'custom':
				$element = $element_label.'<div class="custom-content">'.$field['value'].'</div>';
				break;

			case 'pagebreak':
				$current_page = $field['class'] ?? '';

				$element = get_submit_button(__('Save Changes'), 'primary large', 'submit-'.$field['name']).'</div><!-- .fieldset-content -->
					</fieldset>
					<fieldset class="fieldset-'.$field['name'].$current_page.'" data-name="'.$field['name'].'">
						<legend>'.$field['label'].'</legend>
						<div class="fieldset-content">';
				return $element;

			default:
				$element = '<div><strong>ERROR: Uknown field type "'.$field['type'].'"</strong></div>';
				break;
		}

		return '<div'.$attr_class.'>'.$enabler.$element.$tooltip.$element_styles.'</div>';
	}

	/**
	* Enqueue scripts and styles needed to use colorpickers
	*/
	public static function load_colorpicker() : void {
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('wp-color-picker');
		wp_enqueue_script('wp-color-picker-change');
	}

	/**
	* Enqueue scripts and styles needed to use rangesliders
	*/
	public static function load_rangeslider() : void {
		wp_enqueue_style('sircon-rangeslider');
		wp_enqueue_script('sircon-rangeslider');
	}

	/**
	* Enqueue scripts and styles needed to use flatpickr
	*/
	public static function load_flatpickr() : void {
		wp_enqueue_style('sircon-flatpickr');
		wp_enqueue_script('sircon-flatpickr');
		wp_enqueue_script('sircon-datepicker');
	}

	/**
	* Enqueue scripts and styles needed to use image fields
	*/
	public static function load_imageselect() : void {
		wp_enqueue_media();
		wp_enqueue_style('sircon-image-select');
		wp_enqueue_script('sircon-image-select');
		wp_localize_script('sircon-image-select', 'sirconlib', ['imageSelectButtonText' => __('Select image', 'sircon-lib')]);
	}

	/**
	* Enqueue scripts and styles needed to use image fields
	*/
	public static function load_fileselect() : void {
		wp_enqueue_media();
		wp_enqueue_script('sircon-file-select');
		wp_localize_script('sircon-file-select', 'sirconlib', ['fileSelectButtonText' => __('Select file', 'sircon-lib')]);
	}

	/**
	* Enqueue scripts and styles needed to use multifield
	*/
	public static function add_form_multifield_support() : void {
		wp_enqueue_script('rubaxa-sortable');
		wp_enqueue_script('sircon-multifield');
		wp_enqueue_style('sircon-multifield');
	}
}

/**
 * HOWTO
 *
 *text: normal text field
 *	type 		= string (The type of field, defaults to 'text')
 *	*name 		= string (Name of the field, must be unique)
 *	default 	= string (The default value)
 *	label 		= string (The field label)
 *	classes 	= array (Extra classes to add to wrapper)
 *	tooltip 	= string (Tooltip for the field, shown when hovering a questionmark after the field)
 *	placeholder = string (HTML placeholder)
 *	enabler 	= string (If set, a checkbox with this as label will be shown that will have to be checked for this field to be displayed)
 *	style 		= string (CSS for this spesific element. Use '##' as selector to select this element.)
 *
 *password: password field
 *	*type 		= string 'password' (The type of field)
 *	*name 		= string (Name of the field, must be unique)
 *	default 	= string (The default value)
 *	label 		= string (The field label)
 *	classes 	= array (Extra classes to add to wrapper)
 *	tooltip 	= string (Tooltip for the field, shown when hovering a questionmark after the field)
 *	placeholder = string (HTML placeholder)
 *	enabler 	= string (If set, a checkbox with this as label will be shown that will have to be checked for this field to be displayed)
 *	style 		= string (CSS for this spesific element. Use '##' as selector to select this element.)
 *
 *slider/range: slider field
 *	*type 		= string 'slider' or 'range' (The type of field)
 *	*name 		= string (Name of the field, must be unique)
 *	default 	= string (The default value)
 *	label 		= string (The field label)
 *	suffix		= string (Text after the slider)
 *	range		= array (range options)
 *		min			= float (The minimum value)
 *		max			= float (The maximum value)
 *		step		= float (The slider step)
 *	classes 	= array (Extra classes to add to wrapper)
 *	tooltip 	= string (Tooltip for the field, shown when hovering a questionmark after the field)
 *	placeholder = string (HTML placeholder)
 *	enabler 	= string (If set, a checkbox with this as label will be shown that will have to be checked for this field to be displayed)
 *	style 		= string (CSS for this spesific element. Use '##' as selector to select this element.)
 *
 *date: date field
 *	*type 		= string 'date' (The type of field)
 *	*name 		= string (Name of the field, must be unique)
 *	default 	= string (The default value)
 *	label 		= string (The field label)
 *	suffix		= string (Text after the slider)
 *	locale		= string (Datepicker locale, defaults to "en")
*	dateformat	= string (The submit format, defaults to "Y-m-d")
*	viewformat	= string (The format visible to the user, defautls to "F j, Y")
*	min			= string|int (Minimum selectable date, timestamp, ISO date string or string format matching dateformat)
*	max			= string|int (Maximum selectable date, timestamp, ISO date string or string format matching dateformat)
 *	classes 	= array (Extra classes to add to wrapper)
 *	tooltip 	= string (Tooltip for the field, shown when hovering a questionmark after the field)
 *	placeholder = string (HTML placeholder)
 *	enabler 	= string (If set, a checkbox with this as label will be shown that will have to be checked for this field to be displayed)
 *	style 		= string (CSS for this spesific element. Use '##' as selector to select this element.)
 *
 *time: time field
 *	*type 		= string 'time' (The type of field)
 *	*name 		= string (Name of the field, must be unique)
 *	default 	= string (The default value)
 *	label 		= string (The field label)
 *	suffix		= string (Text after the slider)
 *	locale		= string (Datepicker locale, defaults to "en")
*	dateformat	= string (The submit format, defaults to "H:i")
*	min			= string|int (Minimum selectable time, timestamp, ISO date string or string format matching dateformat)
*	max			= string|int (Maximum selectable time, timestamp, ISO date string or string format matching dateformat)
 *	classes 	= array (Extra classes to add to wrapper)
 *	tooltip 	= string (Tooltip for the field, shown when hovering a questionmark after the field)
 *	placeholder = string (HTML placeholder)
 *	enabler 	= string (If set, a checkbox with this as label will be shown that will have to be checked for this field to be displayed)
 *	style 		= string (CSS for this spesific element. Use '##' as selector to select this element.)
 *
 *datetime: datetime field
 *	*type 		= string 'datetime' (The type of field)
 *	*name 		= string (Name of the field, must be unique)
 *	default 	= string (The default value)
 *	label 		= string (The field label)
 *	suffix		= string (Text after the slider)
 *	locale		= string (Datepicker locale, defaults to "en")
*	dateformat	= string (The submit format, defaults to "Y-m-d H:i")
*	viewformat	= string (The date format visible to the user, defautls to "F j, Y")
*	min			= string|int (Minimum selectable date, timestamp, ISO date string or string format matching dateformat)
*	max			= string|int (Maximum selectable date, timestamp, ISO date string or string format matching dateformat)
*	min_time	= string|int (Minimum selectable time, timestamp, ISO date string or string format matching dateformat)
*	max_time	= string|int (Maximum selectable time, timestamp, ISO date string or string format matching dateformat)
 *	classes 	= array (Extra classes to add to wrapper)
 *	tooltip 	= string (Tooltip for the field, shown when hovering a questionmark after the field)
 *	placeholder = string (HTML placeholder)
 *	enabler 	= string (If set, a checkbox with this as label will be shown that will have to be checked for this field to be displayed)
 *	style 		= string (CSS for this spesific element. Use '##' as selector to select this element.)
 *
 *hidden: hidden field
 *	*type 		= string 'hidden' (The type of field)
 *	*name 		= string (Name of the field, must be unique)
 *	*value		= string (The field value)
 *	classes 	= array (Extra classes to add to wrapper)
 *
 *radio: radio buttons
 *	*type 		= string 'radio' (The type of field)
 *	*name 		= string (Name of the field, must be unique)
 *	default 	= string (The default value)
 *	label 		= string (The field label)
 *	classes 	= array (Extra classes to add to wrapper)
 *	tooltip 	= string (Tooltip for the field, shown when hovering a questionmark after the field)
 *	placeholder = string (HTML placeholder)
 *	enabler 	= string (If set, a checkbox with this as label will be shown that will have to be checked for this field to be displayed)
 *	style 		= string (CSS for this spesific element. Use '##' as selector to select this element.)
 *	*options	= array (The different options, structured as array('label1' => 'value1', 'label2' => 'value2'))
 *
 *checkbox: a checkbox
 *	*type 		= string 'checkbox' (The type of field)
 *	*name 		= string (Name of the field, must be unique)
 *	value		= string (The value to save, defaults to 'on')
 *	default 	= string (The default value)
 *	label 		= string (The field label)
 *	classes 	= array (Extra classes to add to wrapper)
 *	tooltip 	= string (Tooltip for the field, shown when hovering a questionmark after the field)
 *	placeholder = string (HTML placeholder)
 *	enabler 	= string (If set, a checkbox with this as label will be shown that will have to be checked for this field to be displayed)
 *	style 		= string (CSS for this spesific element. Use '##' as selector to select this element.)
 *
 *select: a dropdown select
 *	*type 		= string 'select' (The type of field)
 *	*name 		= string (Name of the field, must be unique)
 *	default 	= string/array (The default value, array accepted if multiple is true)
 *	label 		= string (The field label)
 *	classes 	= array (Extra classes to add to wrapper)
 *	tooltip 	= string (Tooltip for the field, shown when hovering a questionmark after the field)
 *	placeholder = string (HTML placeholder)
 *	enabler 	= string (If set, a checkbox with this as label will be shown that will have to be checked for this field to be displayed)
 *	style 		= string (CSS for this spesific element. Use '##' as selector to select this element.)
 *	*options	= array (The different options, structured as array('label1' => 'value1', 'label2' => 'value2'))
 *	multiple	= boolean (set to true to enable selection of multiple elements)
 *
 *textarea: textarea
 *	*type 		= string 'textarea' (The type of field)
 *	*name 		= string (Name of the field, must be unique)
 *	default 	= string (The default value)
 *	label 		= string (The field label)
 *	classes 	= array (Extra classes to add to wrapper)
 *	tooltip 	= string (Tooltip for the field, shown when hovering a questionmark after the field)
 *	placeholder = string (HTML placeholder)
 *	enabler 	= string (If set, a checkbox with this as label will be shown that will have to be checked for this field to be displayed)
 *	style 		= string (CSS for this spesific element. Use '##' as selector to select this element.)
 *
 *image: WP Media selector
 *	*type 		= string 'image' (The type of field)
 *	*name 		= string (Name of the field, must be unique)
 *	label 		= string (The field label)
 *	classes 	= array (Extra classes to add to wrapper)
 *	tooltip 	= string (Tooltip for the field, shown when hovering a questionmark after the field)
 *	placeholder = string (HTML placeholder)
 *	enabler 	= string (If set, a checkbox with this as label will be shown that will have to be checked for this field to be displayed)
 *	style 		= string (CSS for this spesific element. Use '##' as selector to select this element.)
 *
 *file: WP Media selector
 *	*type 		= string 'file' (The type of field)
 *	*name 		= string (Name of the field, must be unique)
 *	label 		= string (The field label)
 *	classes 	= array (Extra classes to add to wrapper)
 *	tooltip 	= string (Tooltip for the field, shown when hovering a questionmark after the field)
 *	placeholder = string (HTML placeholder)
 *	enabler 	= string (If set, a checkbox with this as label will be shown that will have to be checked for this field to be displayed)
 *	style 		= string (CSS for this spesific element. Use '##' as selector to select this element.)
 *
 *color: Colorpicker
 *	*type 		= string 'color' (The type of field)
 *	*name 		= string (Name of the field, must be unique)
 *	default 	= string (The default value)
 *	label 		= string (The field label)
 *	classes 	= array (Extra classes to add to wrapper)
 *	tooltip 	= string (Tooltip for the field, shown when hovering a questionmark after the field)
 *	placeholder = string (HTML placeholder)
 *	enabler 	= string (If set, a checkbox with this as label will be shown that will have to be checked for this field to be displayed)
 *	style 		= string (CSS for this spesific element. Use '##' as selector to select this element.)
 *
 *editor: WP editor (Simple when part of multifield)
 *	*type 		= string 'editor' (The type of field)
 *	*name 		= string (Name of the field, must be unique)
 *	default 	= string (The default value)
 *	label 		= string (The field label)
 *	classes 	= array (Extra classes to add to wrapper)
 *	tooltip 	= string (Tooltip for the field, shown when hovering a questionmark after the field)
 *	placeholder = string (HTML placeholder)
 *	enabler 	= string (If set, a checkbox with this as label will be shown that will have to be checked for this field to be displayed)
 *	style 		= string (CSS for this spesific element. Use '##' as selector to select this element.)
 *
 *multiple: A special type of field to enable one or more occurences for a group of fields
 *	*type 		= string 'multiple' (The type of field)
 *	*name 		= string (Name of the field, must be unique)
 *	default 	= string (The default value)
 *	label 		= string (The field label)
 *	classes 	= array (Extra classes to add to wrapper)
 *	tooltip 	= string (Tooltip for the field, shown when hovering a questionmark after the field)
 *	placeholder = string (HTML placeholder)
 *	enabler 	= string (If set, a checkbox with this as label will be shown that will have to be checked for this field to be displayed)
 *	style 		= string (CSS for this spesific element. Use '##' as selector to select this element.)
 *	sortable	= boolean (Set this to true to enable sorting of elements)
 *	adder_label = string (The label for the "Add new element" button)
 *	*template	= array (Array of fields in the group)
 *
 *custom: A custom element for outputting a string as part of the form
 *	*type 		= string 'custom' (The type of field)
 *	*value		= string (The content to be shown)
 *	label 		= string (The field label)
 *	classes 	= array (Extra classes to add to wrapper)
 *	tooltip 	= string (Tooltip for the field, shown when hovering a questionmark after the field)
 *	enabler 	= string (If set, a checkbox with this as label will be shown that will have to be checked for this field to be displayed)
 *	style 		= string (CSS for this spesific element. Use '##' as selector to select this element.)
 *
 *pagebreak: Spesial type used to split optionpages into tabs
 *	*type 		= string 'pagebreak' (The type of field)
 *	*name		= string (The name of the new page)
 *	label		= string (The title on the new page)
 *
 */
?>
