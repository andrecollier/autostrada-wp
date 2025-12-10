<?php
namespace sircon;

if (!class_exists('\sircon\Lib')) {
	define('SIRCON_LIB_LOADED_BY', basename(dirname(dirname(__FILE__))));

	/**
	 * Sircon Library
	 */
	class Lib {

		/**
		 * Array of valid packages to load, with their respective classname, init function and dependencie
		 *
		 * @var array
		 */
		private static $_packages = [
			'formelements' 	=> [
				'class' => 'Form',
				'init' => 'init',
				'dependencies' => [],
			],
			'postdata' 		=> [
				'class' => 'Postdata',
				'init' => 'init',
				'dependencies' => [
					'formelements',
				],
			],
			'options' 		=> [
				'class' => 'Options',
				'init' => 'init',
				'dependencies' => [
					'formelements',
				],
			],
			'taxdata' 		=> [
				'class' => 'Taxdata',
				'init' => 'init',
				'dependencies' => [
					'formelements',
				],
			],
			'userdata' 		=> [
				'class' => 'Userdata',
				'init' => 'init',
				'dependencies' => [
					'formelements',
				],
			],
			'tabs' => [
				'class' => 'Tabs',
				'dependencies' => [],
			],
		];

		/**
		 * Array to keep track of loaded packages
		 *
		 * @var array
		 */
		private static $_loaded_packages = [];

		/**
		 * Load a library package and all its dependencies
		 *
		 * @param string $package The requested package
		 */
		public static function load(string $package) : bool {
			if (!array_key_exists($package, self::$_packages)) {
				return false;
			}
			
			if (in_array($package, self::$_loaded_packages)) {
				return true;
			}

			foreach (self::$_packages[$package]['dependencies'] as $dependency) {
				self::load($dependency);
			}

			include(dirname(__FILE__).'/classes/'.$package.'.class.php');
			self::$_loaded_packages[] = $package;

			if (self::$_packages[$package]['init']) {
				call_user_func([__NAMESPACE__.'\\'.self::$_packages[$package]['class'], self::$_packages[$package]['init']]);
			}
			
			return true;
		}

		/**
		 * Autoload hook
		 *
		 * @param  string $class Classname
		 *
		 * @return bool          True if the class was loaded
		 */
		public static function autoload(string $class) : bool {
			if (strpos($class, __NAMESPACE__) !== 0) {
				return false;
			}
			
			return self::load(str_replace(__NAMESPACE__.'\\', '', strtolower($class)));
		}

		/**
		 * Get the full URL to a file in the library
		 *
		 * @param  string $file The file to get, relative to the library folder
		 *
		 * @return string       The full file URL
		 */
		public static function get_lib_url(string $file) : string {
			if (self::is_loaded_by_theme()) {
				return get_template_directory_uri().'/library/'.$file;
			}
			
			return plugins_url($file, __FILE__);
		}

		/**
		 * Check if the library is currently loaded by theme
		 *
		 * @return bool True if library is currently loaded by theme
		 */
		public static function is_loaded_by_theme() : bool {
			$root = get_theme_root();
			$root = str_replace('\\', '/', $root);
			$file_path = str_replace('\\', '/', __FILE__);
			return (stripos($file_path, $root) !== false);
		}
	}

	if (Lib::is_loaded_by_theme()) {
		add_action('after_setup_theme', function(){
			load_theme_textdomain('sircon-lib', get_template_directory().'/library/languages/');
		});
	} else {
		add_action('plugins_loaded', function() {
			load_plugin_textdomain('sircon-lib', false, plugin_basename(dirname(__FILE__)).'/languages/');
		});
	}

	spl_autoload_register(['\sircon\Lib', 'autoload']);
}

?>