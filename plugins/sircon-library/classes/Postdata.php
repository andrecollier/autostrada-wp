<?php

namespace Sircon\Library;

use Sircon\Library\Formfield\Formfield;
use WP_Post;

class Postdata {

	public const CONTEXT_SIDE = 'side';

	public const CONTEXT_NORMAL = 'normal';

	public const CONTEXT_ADVANCED = 'advanced';

	private static $metaboxes = [];

	public static function setup(): void {
		add_action('add_meta_boxes', [__CLASS__ , 'addMetaBoxes']);
		add_action('save_post', [__CLASS__ , 'saveMeta']);
	}

	public static function addPostbox(string $id, string $title, string $context = self::CONTEXT_ADVANCED, array $post_types = ['post', 'page']): void {
		$metabox = [
			'title' => $title,
			'post_type_in' => $post_types,
			'context' => $context,
			'fields' => [],
		];

		self::$metaboxes[$id] = $metabox;
	}

	public static function add(string $postbox_id, Formfield $Field): void {
		self::$metaboxes[$postbox_id]['fields'][] = $Field;
	}

	public static function get(int $post_id, string $field): string {
		return get_post_meta($post_id, $field, true) ?? '';
	}

	public static function has(int $post_id, string $field): bool {
		return $post_id && metadata_exists('post', $post_id, $field);
	}

	private static function metaboxVisibleOnPost(string $metabox_id, int $post_id): bool {
		return in_array(get_post_type($post_id), self::$metaboxes[$metabox_id]['post_type_in']);
	}

	public static function addMetaBoxes(): void {
		foreach (self::$metaboxes as $metabox_id => $metabox) {
			if (!self::metaboxVisibleOnPost($metabox_id, get_the_ID())) {
				continue;
			}

			add_meta_box($metabox_id, $metabox['title'], [__CLASS__ , 'metaboxOutput'], get_post_type(), $metabox['context']);
		}
	}

	public static function metaboxOutput(WP_Post $post, array $params): void {
		$metabox_id = $params['id'];

		if (empty(self::$metaboxes[$metabox_id]['fields'])) {
			return;
		}

		?>
		<input type="hidden" name="sircon_postmeta" value="true">
		<?php

		foreach (self::$metaboxes[$metabox_id]['fields'] as $Field) {
			if ($Field->isSaveable()) {
				$Field->removeInputClass('regular-text');
				$Field->addInputClass('components-text-control__input');
				if (self::has($post->ID, $Field->getName())) {
					$Field->setValue(self::get($post->ID, $Field->getName()));
				}
			}

			$Field->output();
		}
	}

	public static function saveMeta(int $post_id): void {

		foreach (self::$metaboxes as $metabox_id => $metabox) {
			if (!self::metaboxVisibleOnPost($metabox_id, $post_id)) {
				continue;
			}

			if (empty($_POST['sircon_postmeta'])) {
				return;
			}

			foreach ($metabox['fields'] as $Field) {
				if ($Field->isSaveable()) {
					update_post_meta($post_id, $Field->getName(), filter_input(INPUT_POST, $Field->getName()));
				}
			}
		}
	}
}

if (is_admin()) {
	Postdata::setup();
}
