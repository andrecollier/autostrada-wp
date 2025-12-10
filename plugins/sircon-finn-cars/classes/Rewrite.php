<?php

namespace sircon\finncars;

class Rewrite {

	/**
	 * Redirect all request starting with the customer page url to the customer page
	 *
	 * @param int $page_id Customerpage ID
	 */
	public static function singlePageRewrite(int $page_id): void {
		$slug = str_replace(home_url(), '', get_permalink($page_id));
		if (strpos($slug, '/') == 0) {
			$slug = substr($slug, 1);
		}

		add_rewrite_rule('^' . $slug . '/?', 'index.php?page_id=' . $page_id, 'top');
	}

	/**
	 * Remove query parameters and anchorlinks from the request URI.
	 * Used for canonical URI on the customerpages, and filtering out the URLparts for customerpage templates.
	 *
	 * @return string Clean request URI
	 */
	public static function getCleanRequestUri(): string {
		$script_url = (!empty($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		$uri = str_replace(get_site_url(), '', $script_url);
		$hash = strpos($uri, '#');
		$query = strpos($uri, '?');
		if ($hash === false) {
			if ($query === false) {
				return $uri;
			}

			return substr($uri, 0, $query);
		}

		if ($query === false) {
			if ($hash === false) {
				return $uri;
			}

			return substr($uri, 0, $hash);
		}

		return substr($uri, 0, min($query, $hash));
	}
}
