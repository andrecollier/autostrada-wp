<?php

namespace Sircon\Library;

use Exception;

class PostType {

	private $id;

	private $name;

	private $singular_name;

	private $rewrite_slug = null;

	private $hidden = false;

	private $gutenberg = false;

	private $dashicon = 'dashicons-chart-line';

	private $args = [];

	public function __construct(string $id, string $name, string $singular_name) {
		if (strlen($id) > 20) {
			throw new Exception('Make sure your custom post type identifier does not exceed 20 characters as the post_type column in the database is currently a VARCHAR field of that length.');
		}

		$this->id = $id;
		$this->name = $name;
		$this->singular_name = $singular_name;
	}

	public function setHidden(bool $hidden = true): self {
		$this->hidden = $hidden;
		return $this;
	}

	public function setGutenbergSupport(bool $supported = true): self {
		$this->gutenberg = $supported;
		return $this;
	}

	public function setRewriteSlug(string $slug): self {
		$this->rewrite_slug = $slug;
		return $this;
	}

	public function setDashicon(string $dashicon): self {
		$this->dashicon = $dashicon;
		return $this;
	}

	public function setArgs(array $args): self {
		$this->args = $args;
		return $this;
	}

	public function defaultArgs(): array {
		$args = [
			'labels' => [
				'name'                  => $this->name,
				'singular_name'         => $this->singular_name,
				/* translators: %s will be replaced by the posttype/taxonomy singular name */
				'add_new'               => sprintf(_x('Add new %s', 'singular', 'sircon-library'), $this->singular_name),
				/* translators: %s will be replaced by the posttype singular name */
				'add_new_item'          => sprintf(_x('Add new item %s', 'singular', 'sircon-library'), $this->singular_name),
				/* translators: %s will be replaced by the posttype/taxonomy singular name */
				'edit_item'             => sprintf(_x('Edit %s', 'singular', 'sircon-library'), $this->singular_name),
				/* translators: %s will be replaced by the posttype/taxonomy singular name */
				'new_item'              => sprintf(_x('New %s', 'singular', 'sircon-library'), $this->singular_name),
				/* translators: %s will be replaced by the posttype singular name */
				'view_item'             => sprintf(_x('Show %s', 'singular', 'sircon-library'), $this->singular_name),
				/* translators: %s will be replaced by the posttype/taxonomy singular name */
				'search_items'          => sprintf(_x('Search %s', 'plural', 'sircon-library'), $this->name),
				/* translators: %s will be replaced by the posttype plural name */
				'not_found'             => sprintf(_x('No %s was found', 'plural', 'sircon-library'), $this->name),
				/* translators: %s will be replaced by the posttype plural name */
				'not_found_in_trash'    => sprintf(_x('No %s were found in the trash', 'plural', 'sircon-library'), $this->name),
				/* translators: %s will be replaced by the posttype/taxonomy singular name */
				'parent_item_colon'     => sprintf(_x('Parent %s:', 'singular', 'sircon-library'), $this->singular_name),
			],
			'public'                => !$this->hidden,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'show_in_nav_menus'     => !$this->hidden,
			'publicly_queryable'    => !$this->hidden,
			'exclude_from_search'   => $this->hidden,
			'has_archive'           => !$this->hidden,
			'query_var'             => !$this->hidden,
			'can_export'            => !$this->hidden,
			'capability_type'       => 'post',
			'menu_position'         => 10,
			'show_in_rest'          => $this->gutenberg,
			'menu_icon'             => $this->dashicon,
			'hierarchical'          => false,
			'taxonomies'            => [],
			'supports'              => ['title', 'editor', 'thumbnail'],
		];

		if (!$this->hidden) {
			$args['rewrite'] = [
				'slug' => $this->rewrite_slug ?? $this->id,
				'with_front' => true
			];
		}

		return $args;
	}

	public function register(): void {
		register_post_type($this->id, array_merge($this->defaultArgs(), $this->args));
	}

	public function addPostbox(string $id, string $title, string $context = Postdata::CONTEXT_ADVANCED, array $additional_post_types = []): self {
		Postdata::addPostbox($id, $title, $context, array_merge($additional_post_types, [$this->id]));

		return $this;
	}

	public function addTaxonomy(string $id, string $name, string $singular_name, array $additional_post_types = []): Taxonomy {
		$this->args['taxomonies'][] = $id;
		return new Taxonomy($id, $name, $singular_name, array_merge($additional_post_types, [$this->id]));
	}
}
