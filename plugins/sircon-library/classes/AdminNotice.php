<?php

namespace Sircon\Library;

/**
 * Helperclass to generate admin notices
 */
class AdminNotice {

	public const WRAPPER = '<div class="notice notice-%s"><%s>%s</%s></div>';

	/**
	 * Get an error-status admin notice
	 *
	 * @param string $content The content of the admin notice
	 * @param boolean $inline True to display the admin notice inline. Fale (default) to display the notice in the header
	 * @param string $container The wrapper element for the content. Defaults to 'p' resulting in a <p>-wrapper
	 * @return string The generated admin notice html
	 */
	public static function error(string $content, bool $inline = false, string $container = 'p'): string {
		return self::get(__FUNCTION__, $content, $inline, $container);
	}

	/**
	 * Get a warning-status admin notice
	 *
	 * @param string $content The content of the admin notice
	 * @param boolean $inline True to display the admin notice inline. Fale (default) to display the notice in the header
	 * @param string $container The wrapper element for the content. Defaults to 'p' resulting in a <p>-wrapper
	 * @return string The generated admin notice html
	 */
	public static function warning(string $content, bool $inline = false, string $container = 'p'): string {
		return self::get(__FUNCTION__, $content, $inline, $container);
	}

	/**
	 * Get a success-status admin notice
	 *
	 * @param string $content The content of the admin notice
	 * @param boolean $inline True to display the admin notice inline. Fale (default) to display the notice in the header
	 * @param string $container The wrapper element for the content. Defaults to 'p' resulting in a <p>-wrapper
	 * @return string The generated admin notice html
	 */
	public static function success(string $content, bool $inline = false, string $container = 'p'): string {
		return self::get(__FUNCTION__, $content, $inline, $container);
	}

	/**
	 * Get a info-status admin notice
	 *
	 * @param string $content The content of the admin notice
	 * @param boolean $inline True to display the admin notice inline. Fale (default) to display the notice in the header
	 * @param string $container The wrapper element for the content. Defaults to 'p' resulting in a <p>-wrapper
	 * @return string The generated admin notice html
	 */
	public static function info(string $content, bool $inline = false, string $container = 'p'): string {
		return self::get(__FUNCTION__, $content, $inline, $container);
	}

	/**
	 * Get a status admin notice
	 *
	 * @param string $status The status class used in the admin notice
	 * @param string $content The content of the admin notice
	 * @param boolean $inline True to display the admin notice inline. Fale (default) to display the notice in the header
	 * @param string $container The wrapper element for the content. Defaults to 'p' resulting in a <p>-wrapper
	 * @return string The generated admin notice html
	 */
	private static function get(string $status, string $content, bool $inline, string $container): string {
		return sprintf(self::WRAPPER, $status . ($inline ? ' inline' : ''), $container, $content, $container);
	}
}
