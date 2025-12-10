<?php

namespace Sircon\Library;

use Exception;
use Sircon\Library\Formfield\Formfield;

class Taxonomy {

	private $id;

	private $name;

	private $singular_name;

	private $rewrite_slug = null;

	private $hidden = false;

	private $post_types = [];

	private $args = [];

	public function __construct(string $id, string $name, string $singular_name, array $post_types) {
		if (strlen($id) > 32) {
			throw new Exception('Taxonomy key must not exceed 32 characters.');
		}

		$this->id = $id;
		$this->name = $name;
		$this->singular_name = $singular_name;
		$this->post_types = $post_types;
	}

	public function setHidden(bool $hidden = true): self {
		$this->hidden = $hidden;
		return $this;
	}

	public function setRewriteSlug(string $slug): self {
		$this->rewrite_slug = $slug;
		return $this;
	}

	public function defaultArgs(): array {
		$args = [
			'labels' => [
				'name'                          => $this->name,
				'menu_name'                     => $this->name,
				'singular_name'                 => $this->singular_name,
				'search_items'                  => sprintf(_x('Search %s', 'plural', 'sircon-library'), $this->name),
				/* translators: %s will be replaced by the taxonomy plural name */
				'popular_items'                 => sprintf(_x('Popular %s', 'plural', 'sircon-library'), $this->name),
				/* translators: %s will be replaced by the taxonomy plural name */
				'all_items'                     => sprintf(_x('All %s', 'plural', 'sircon-library'), $this->name),
				/* translators: %s will be replaced by the taxonomy singular name */
				'parent_item'                   => sprintf(_x('Parent %s', 'singular', 'sircon-library'), $this->singular_name),
				'parent_item_colon'             => sprintf(_x('Parent %s:', 'singular', 'sircon-library'), $this->singular_name),
				'edit_item'                     => sprintf(_x('Edit %s', 'singular', 'sircon-library'), $this->singular_name),
				/* translators: %s will be replaced by the taxonomy singular name */
				'update_item'                   => sprintf(_x('Update %s', 'singular', 'sircon-library'), $this->singular_name),
				'add_new_item'                  => sprintf(_x('Add new %s', 'singular', 'sircon-library'), $this->singular_name),
				'new_item_name'                 => sprintf(_x('New %s', 'singular', 'sircon-library'), $this->singular_name),
				/* translators: %s will be replaced by the taxonomy plural name */
				'separate_items_with_commas'    => sprintf(_x('Separate %s with commas', 'plural', 'sircon-library'), $this->name),
				/* translators: %s will be replaced by the taxonomy plural name */
				'add_or_remove_items'           => sprintf(_x('Add or remove %s', 'plural', 'sircon-library'), $this->name),
				/* translators: %s will be replaced by the taxonomy plural name */
				'choose_from_most_used'         => sprintf(_x('Choose from the most used %s', 'plural', 'sircon-library'), $this->name),
			],
			'public'            => !$this->hidden,
			'show_in_nav_menus' => !$this->hidden,
			'show_in_menu'      => true,
			'show_ui'           => true,
			'show_tagcloud'     => false,
			'hierarchical'      => true,
			'rewrite'           => !$this->hidden,
			'query_var'         => true,
			'show_in_rest'      => true,
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
		register_taxonomy($this->id, $this->post_types, array_merge($this->defaultArgs(), $this->args));
	}

	public function addTaxdata(Formfield $Field): self {
		Taxdata::add($this->id, $Field);

		return $this;
	}
}
