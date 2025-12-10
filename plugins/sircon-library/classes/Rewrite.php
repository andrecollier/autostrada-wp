<?php

namespace Sircon\Library;

final class Rewrite {

	/**
	 * Redirect all request starting with the page url to the page
	 *
	 * @param int $page_id
	 */
	public static function singlePageRewrite(int $page_id): void {
		$slug = trim(str_replace(home_url(), '', get_permalink($page_id)), "/");
		if (!$slug) {
			add_action('admin_notices', function () use ($page_id) {
				/* translators: %s will be replaced by the selected page title */
				echo AdminNotice::error(sprintf(__('Could not create a rewrite for the page "%s" because its URL is the same as the frontpage.', 'sircon-library'), get_the_title($page_id)));
			});
			return;
		}

		add_rewrite_rule('^' . $slug . '/?', 'index.php?page_id=' . $page_id, 'top');

		// Fix the canonical URL for rewritten page
		add_filter('get_canonical_url', function ($canonical_url) use ($page_id) {
			if (is_page($page_id)) {
				$base = str_replace(home_url(), '', get_permalink());
				$canonical_url .= str_replace($base, '', self::getCleanRequestUri());
			}
			return $canonical_url;
		});
	}

	/**
	 * Remove query parameters and anchorlinks from the request URI.
	 *
	 * @return string Clean request URI
	 */
	public static function getCleanRequestUri(): string {
		$script_url = (!empty($_SERVER['HTTPS']) ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '');
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
