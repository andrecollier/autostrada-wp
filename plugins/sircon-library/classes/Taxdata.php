<?php

namespace Sircon\Library;

use Sircon\Library\Formfield\Formfield;

class Taxdata {

	private static $taxfields = [];

	public static function setup(): void {
		add_action('admin_init', [__CLASS__ , 'addTaxmetaActions'], 200);
		add_action('created_term', [__CLASS__ , 'saveFields'], 10, 3);
		add_action('edit_term', [__CLASS__ , 'saveFields'], 10, 3);
	}

	public static function add(string $taxonomy, Formfield $Field): void {
		self::$taxfields[$taxonomy][] = $Field;
	}

	public static function get(int $term_id, string $field): string {
		return get_term_meta($term_id, $field, true);
	}

	public static function has(int $term_id, string $field): bool {
		return metadata_exists('term', $term_id, $field);
	}

	public static function addTaxmetaActions() {
		foreach (array_keys(self::$taxfields) as $taxonomy) {
			add_action($taxonomy . '_add_form_fields', [__CLASS__ , 'outputAddTaxfields'], 10, 1);
			add_action($taxonomy . '_edit_form', [__CLASS__ , 'outputEditTaxfields'], 10, 2);
		}
	}

	public static function outputAddTaxfields($taxonomy) {
		if (empty(self::$taxfields[$taxonomy])) {
			return;
		}

		foreach (self::$taxfields[$taxonomy] as $Field) {
			$Field->addClass('form-field');
			$Field->output();
		}
	}

	public static function outputEditTaxfields($tag, $taxonomy) {
		if (empty(self::$taxfields[$taxonomy])) {
			return;
		}

		?>
		<table class="form-table">
			<input type="hidden" name="sircon_taxmeta" value="true">
			<?php
			foreach (self::$taxfields[$taxonomy] as $Field) {
				if ($Field->isSaveable()) {
					$Field->setTableLayout(true);
					$Field->addClass('form-field');
					if (self::has($tag->term_id, $Field->getName())) {
						$Field->setValue(self::get($tag->term_id, $Field->getName()));
					}
				}

				$Field->output();
			}
			?>
		</table>
		<?php
	}

	public static function saveFields($term_id, $tt_id, $taxonomy) {
		if (empty(self::$taxfields[$taxonomy])) {
			return;
		}

		if (empty($_POST['sircon_taxmeta'])) {
			return;
		}

		foreach (self::$taxfields[$taxonomy] as $Field) {
			if ($Field->isSaveable()) {
				update_term_meta($term_id, $Field->getName(), filter_input(INPUT_POST, $Field->getName()));
			}
		}
	}
}

if (is_admin()) {
	Taxdata::setup();
}
