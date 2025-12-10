<?php

namespace Sircon\Library\Improvements;

use WP_Query;

/**
 * Disables paging for hierarchical post types and taxonomies on Edit Menus screen to preserve proper hierarchy in meta boxes.
 * Original source: https://core.trac.wordpress.org/attachment/ticket/18282/preserve-page-and-taxonomy-hierarchy.php
 */
class PreserveMetaboxHierarchy {

	private static $instance = null;

	public function __construct() {
		add_action('load-nav-menus.php', [$this, 'init']);
	}

	public function init() {
		add_action('pre_get_posts', [$this, 'disablePagingForHierarchicalPostTypes']);
		add_filter('get_terms_args', [$this, 'removeLimitForHierarchicalTaxonomies'], 10, 2);
		add_filter('get_terms_fields', [$this, 'removePageLinksForHierarchicalTaxonomies'], 10, 3);
	}

	public function disablePagingForHierarchicalPostTypes(WP_Query $query): void {
		if (!is_admin() || 'nav-menus' !== get_current_screen()->id) {
			return;
		}

		if (!is_post_type_hierarchical($query->get('post_type'))) {
			return;
		}

		if (50 == $query->get('posts_per_page')) {
			$query->set('nopaging', true);
		}
	}

	public function removeLimitForHierarchicalTaxonomies(array $args, array $taxonomies): array {
		if (!is_admin() || 'nav-menus' !== get_current_screen()->id) {
			return $args;
		}

		if (!is_taxonomy_hierarchical(reset($taxonomies))) {
			return $args;
		}

		if (50 == $args['number']) {
			$args['number'] = '';
		}

		return $args;
	}

	public function removePageLinksForHierarchicalTaxonomies(array $selects, array $args, array $taxonomies): array {
		if (!is_admin() || 'nav-menus' !== get_current_screen()->id) {
			return $selects;
		}

		if (!is_taxonomy_hierarchical(reset($taxonomies))) {
			return $selects;
		}

		if ('count' === $args['fields']) {
			$selects = ['1'];
		}

		return $selects;
	}

	public static function enable(): void {
		self::$instance = self::$instance ?? new self();
	}
}
