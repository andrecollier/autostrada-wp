<?php

/*
Plugin name:		Sircon Library
Description:		A Collection of library files and utilities used by other Sircon plugins and themes
Version:			1.0.28
Requires at least:	5.8
Requires PHP:		7.4
Author:				Sircon Norge AS
Author URI:			https://sircon.no/
Update URI:			https://code.sircon.net/wordpress/plugins/sircon-library
Text Domain:		sircon-library
Domain Path:		/languages
*/

/*
	Use the following check in the top of every plugin that uses this library to stop loading if this plugin is not activated:

	if (!in_array('sircon-library/sircon-library.php', apply_filters('active_plugins', get_option('active_plugins')))) {
		add_action('admin_notices', function () {
			$plugin_data = get_plugin_data(__FILE__);
			printf('<div class="notice notice-error"><p>%s</p></div>', sprintf('%s requires Sircon Library to be activated to work.', $plugin_data['Name']));
		});
		return;
	}

	Do not use library classes before the "plugins_loaded" hook (https://codex.wordpress.org/Plugin_API/Action_Reference)!
*/

namespace Sircon\Library;

/**
 * Main plugin class
 */
final class Library {

	public const ASSETS_PATH = __DIR__ . "/assets";

	/**
	 * Library constructor to initialize plugin
	 */
	public function __construct() {
		// Autoloader
		include __DIR__ . '/vendor/autoload.php';

		// Initialize updater
		\Sircon\Library\Updater\Automatic::enable();

		// Hook setup
		add_action('init', [$this, 'setupTranslation']);
		add_action('wp_enqueue_scripts', [$this, 'registerFrontend'], 5);
		add_action('admin_enqueue_scripts', [$this, 'enqueueBackend']);
	}

	/**
	 * Setup library translations
	 *
	 * @return void
	 */
	public function setupTranslation(): void {
		load_plugin_textdomain('sircon-library', false, plugin_basename(__DIR__) . '/languages');
	}

	/**
	 * Register frontend scripts and styles
	 */
	public function registerFrontend(): void {
		// Utility scripts
		wp_register_script('sircon-scrolled-down', plugins_url('/assets/scripts/scrolled-down.js', __FILE__), ['jquery'], false, true);
		wp_register_script('sircon-equal-height-boxes', plugins_url('/assets/scripts/equal-height-boxes.js', __FILE__), ['jquery'], false, true);
		wp_register_script('sircon-class-when-visible', plugins_url('/assets/scripts/class-when-visible.js', __FILE__), ['jquery'], false, true);

		// Dynamic images
		wp_register_script('sircon-dynamic-images', plugins_url('/assets/scripts/dynamic-images.js', __FILE__), ['jquery'], false, true);
		wp_register_style('sircon-dynamic-images', plugins_url('/assets/styles/dynamic-images.css', __FILE__));

		// Calendar
		wp_register_style('sircon-calendar', plugins_url('/assets/styles/calendar.css', __FILE__));
		wp_register_script('sircon-calendar', plugins_url('/assets/scripts/calendar.js', __FILE__), ['jquery'], false, true);
		wp_localize_script('sircon-calendar', 'sirconLibrary', ['adminAjax' => admin_url('admin-ajax.php')]);

		// Slick slider
		wp_register_style('slick', plugins_url('/assets/slick/slick.css', __FILE__));
		wp_register_style('slick-theme', plugins_url('/assets/slick/slick-theme.css', __FILE__));
		wp_register_script('slick', plugins_url('/assets/slick/slick.min.js', __FILE__), ['jquery'], false, true);

		// Swiper slider
		wp_register_style('swiper', plugins_url('/assets/swiper/swiper-bundle.min.css', __FILE__));
		wp_register_script('swiper', plugins_url('/assets/swiper/swiper-bundle.min.js', __FILE__), [], false, true);
		wp_register_script('swiper-autoconfig', plugins_url('/assets/swiper/swiper-autoconfig.js', __FILE__), ['jquery', 'swiper'], false, true);
	}

	/**
	 * Enqueue backend scripts and styles
	 */
	public function enqueueBackend(): void {
		wp_enqueue_media();
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('wp-color-picker');
		wp_enqueue_style('sircon-library-admin', plugins_url('/assets/library.css', __FILE__));
		wp_enqueue_script('sircon-library-admin', plugins_url('/assets/library.js', __FILE__), ['jquery', 'wp-color-picker', 'wp-i18n'], false, true);
		wp_set_script_translations('sircon-library-admin', 'sircon-library', __DIR__ . '/languages');
	}

	/**
	 * Get the current Sircon Library version number
	 */
	public static function version(): string {
		return get_file_data(__FILE__, ['Version' => 'Version'], false)['Version'];
	}
}

// Initialize library plugin
new Library();
