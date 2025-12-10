<?php namespace sircon;

class Tabs {
	
	/**
	 * Registered tabs
	 * @var array
	 */
	private $_tabs = [];
	
	/**
	 * Tab wrapper classes
	 * @var array
	 */
	private $_classes = ['sircon-tabs-wrapper'];


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
		wp_enqueue_script('sircon-tabs', Lib::get_lib_url('scripts/tabs.js'), ['jquery'], false, true);
		wp_enqueue_style('sircon-tabs',	Lib::get_lib_url('styles/tabs.css'));
	}

	/**
	 * Add a class to the tab container
	 *
	 * @param string $class The new class to add
	 */
	public function add_class(string $class) : void {
		$this->_classes[] = $class;
	}

	/**
	 * Add a new tab
	 *
	 * @param string  $title   The tab title
	 * @param string  $content The tab content
	 * @param boolean $active  True if the tab should be selected by default
	 *
	 * @param array   $params  Extra tab parameters: string|array class, string|array attributes, string|array title_class
	 */
	public function add_tab(string $title, string $content, bool $active = false, array $params = []) : void {
		$tab_id = 'tab-'.sanitize_title($title);

		$classes = ['sircon-tab'];
		if ($active) {
			$classes[] = 'current';
		}

		if (isset($params['class'])) {
			if (is_string($params['class'])) {
				$params['class'] = [$params['class']];
			}
			
			$classes = array_merge($classes, $params['class']);
		}

		$attributes = '';
		if (isset($params['attributes'])) {
			if (is_string($params['attributes'])) {
				$attributes = ' '.trim($params['attributes']);
			} elseif (is_array($params['attributes'])) {
				foreach ($params['attributes'] as $attr => $value) {
					$attributes .= ' '.trim($attr).'="'.trim($value).'"';
				}
			}
		}

		$title_classes = [];
		if (isset($params['title_class'])) {
			if (is_string($params['title_class'])) {
				$params['title_class'] = [$params['title_class']];
			}
			
			$title_classes = array_merge($title_classes, $params['title_class']);
		}

		$this->_tabs[$tab_id] = [
			'title' => $title,
			'content' => '<div class="'.implode(' ', $classes).'" id="'.$tab_id.'"'.$attributes.'>'.$content.'</div>',
			'active' => $active,
			'classes' => $title_classes,
		];
	}

	/**
	 * Get the tabs output
	 *
	 * @return string The tabs output
	 */
	public function get_output() : string {
		ob_start();
		$this->output();
		return ob_get_clean();
	}

	/**
	 * Print the tabs output
	 */
	public function output() : void {
		?>
		<div class="<?php echo implode(' ', $this->_classes); ?>" data-sircon="sircontabs">
			<div class="tab-titles"><?php echo $this->_get_tabs_titles(); ?></div>
			<div class="them-tabs"><?php
			foreach ($this->_tabs as $tab) {
				echo $tab['content'];
			}
			?></div>
		</div>
		<?php
	}

	/**
	 * Get the tab titles html output
	 *
	 * @return string The tab titles
	 */
	private function _get_tabs_titles() : string {
		$tabs_titles = '';
		foreach ($this->_tabs as $tab_id => $tab) {
			if (!$tab['title']) {
				return '';
			}
			
			$tab_active = $tab['active'] ? ' current' : '';
			$tabs_titles .= '<a href="#" class="sircon-tab-title'.$tab_active.' '.implode(' ', $tab['classes']).'" data-tabtarget="'.$tab_id.'">'.$tab['title'].'</a>';
		}
		
		return $tabs_titles;
	}
}
?>