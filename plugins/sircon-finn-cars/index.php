<?php

/*
Plugin name:    Sircon Finn Cars
Description:    Display car ads from finn.no
Version:        1.2.12
Author:         Sircon Norge AS
Text Domain:    sircon-finn-cars
*/

namespace sircon\finncars;

define( 'WP_PLUGIN_URL_ME', WP_CONTENT_URL . '/plugins' );

include dirname(__FILE__) . '/settings.php';

class FinnCars {

	public const BASEPATH = __DIR__;

	public const OPTIONSPAGE_ID = 'sircon_finn_cars';

	public function __construct() {
		include dirname(__FILE__) . '/includes/plugin.updater.php';
		include dirname(__FILE__) . '/library/loader.php';

		spl_autoload_register([$this, 'autoload']);

		add_action('init', [$this, 'setup']);
		add_action('plugins_loaded', [$this, 'setupTranslation']);
		add_action('wp_enqueue_scripts', [$this, 'enqueueFrontend']);

		add_action('wp_ajax_sfc_filter', [__NAMESPACE__ . '\\Template', 'ajaxFilter']);
		add_action('wp_ajax_nopriv_sfc_filter', [__NAMESPACE__ . '\\Template', 'ajaxFilter']);

		register_activation_hook(__FILE__, 'flush_rewrite_rules');
		register_deactivation_hook(__FILE__, 'flush_rewrite_rules');
	}

	public function setup() {
		include dirname(__FILE__) . '/includes/optionspage.setup.php';

		if (isset($_GET['clear-finn-cache'])) {
			Finn::clearCache();
		}

		$page_id = intval(\sircon\Options::get_option(self::OPTIONSPAGE_ID, 'cars_page_id'));

		if (!$page_id) {
			return;
		}

		$car_pages = [];

		Rewrite::singlePageRewrite($page_id);
		$car_pages[] = $page_id;

		if (function_exists('pll_languages_list')) {
			$languages = pll_languages_list();
			foreach ($languages as $language) {
				$page_id = pll_get_post($page_id, $language);
				if ($page_id) {
					Rewrite::singlePageRewrite($page_id);
					$car_pages[] = $page_id;
				}
			}
		}

		/**
		 * Fix the canonical URL on customerpage endpoints
		 */
		add_filter('get_canonical_url', function ($canonical_url) use ($car_pages) {
			if (in_array(get_the_ID(), $car_pages)) {
				$base = str_replace(home_url(), '', get_permalink());
				$canonical_url .= str_replace($base, '', Rewrite::getCleanRequestUri());
			}

			return $canonical_url;
		});

		/**
		 * Replace the content on customerpage endpoints
		 */
		add_action('template_redirect', function () use ($car_pages) {
			if (in_array(get_the_ID(), $car_pages)) {
				$base = str_replace(home_url(), '', get_permalink());
				$parts = untrailingslashit(str_replace($base, '', Rewrite::getCleanRequestUri()));
				if (!$parts) {
					Template::getArchive();
				} else {
					$finnid = intval(current(explode('/', $parts)));
					if (!$finnid) {
						global $wp_query;
						$wp_query->set_404();
						status_header(404);
						get_template_part(404);
						exit();
					} else {
						Template::getSingle($finnid);
					}
				}
			}
		});

		if (is_admin() && ($_GET['page'] ?? '') === 'sircon_finn_cars' && !empty($_GET['settings-updated'])) {
			flush_rewrite_rules();
		}
	}

	public function setupTranslation() {
		load_plugin_textdomain('sircon-finn-cars', false, plugin_basename(dirname(__FILE__)) . '/languages');
	}

	public function enqueueFrontend() {
		wp_enqueue_style('sircon-finn-cars', plugins_url('/style/styles.css', __FILE__));
		wp_enqueue_script('sircon-finn-cars', plugins_url('/script/scripts.js', __FILE__), ['jquery'], '1642509055', true);
		wp_localize_script('sircon-finn-cars', 'sfc', ['ajax_url' => admin_url('admin-ajax.php')]);
	}

	/**
	 * Autoload hook
	 *
	 * @param  string $class Classname
	 *
	 * @return bool          True if the class was loaded
	 */
	public function autoload(string $class): bool {
		if (strpos($class, __NAMESPACE__) !== 0) {
			return false;
		}

		$file = dirname(__FILE__) . '/classes/' . str_replace([__NAMESPACE__ . '\\', '\\'], ['', '/'], $class) . '.php';
		if (file_exists($file) && include_once $file) {
			return true;
		}

		return false;
	}
}

new FinnCars();
