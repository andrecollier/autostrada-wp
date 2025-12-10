<?php

namespace sircon\finncars;

class Template {

	private const TEMPLATESPATH = FinnCars::BASEPATH . '/templates';

	public static function getArchive(): void {
		add_filter('the_content', function ($content) {
			ob_start();

			$Finn = new Finn();
			$result = simplexml_load_string($Finn->getArchive());

			if (!$result) {
				$content = ob_get_clean();
				return $content;
			}

			$filter_template = apply_filters('sircon_finn_cars_filter_template', self::TEMPLATESPATH . '/filter.php');
			$box_template = apply_filters('sircon_finn_cars_filter_template', self::TEMPLATESPATH . '/box.php');
			$archive_template = apply_filters('sircon_finn_cars_archive_template', self::TEMPLATESPATH . '/archive.php');
			$archive_classes = apply_filters('sircon_finn_cars_archive_classes', ['sfc-archive', 'alignwide']);

			$current_page = 1;
			echo '<div class="' . implode(' ', $archive_classes) . '">';
			if (file_exists($filter_template)) {
				include $filter_template;
			}

        	if (file_exists($box_template)) {
				include $box_template;
			}


			if (file_exists($archive_template)) {
				include $archive_template;
			}

			echo '</div>';
			$content = ob_get_clean();

			return $content;
		});
	}

	public static function getSingle(int $finn_id): void {
		add_filter('the_content', function ($content) use ($finn_id) {
			ob_start();

			$Finn = new Finn();
			$ad = simplexml_load_string($Finn->getSingle($finn_id));
			$item = Finn::parseSingle($ad);

			$single_template = apply_filters('sircon_finn_cars_single_template', self::TEMPLATESPATH . '/single.php');
			if (file_exists($single_template)) {
				include $single_template;
			}

			$content = ob_get_clean();
			return $content;
		});
	}

	public static function ajaxFilter() {
		$filter = $_POST['data'] ?? [];
		$filter = array_filter($filter, function ($value) {
			return !empty($value['value']);
		});

		$current_page = intval(filter_input(INPUT_POST, 'page', FILTER_SANITIZE_NUMBER_INT));
		if (!$current_page) {
			$current_page = 1;
		}

		$Finn = new Finn();
		$result = simplexml_load_string($Finn->getArchive($current_page, $filter));

		if (!$result) {
			echo json_encode([
				'content' => '',
			]);
			wp_die();
		}

		$archive_template = apply_filters('sircon_finn_cars_archive_template', self::TEMPLATESPATH . '/archive.php');

		ob_start();

		if (file_exists($archive_template)) {
			include $archive_template;
		}

		$content = ob_get_clean();
		$filter_results = [];

		foreach ($result->children($ns['f'])->filter as $filter) {
			if ((string) $filter->attributes()->range === 'true') {
				continue;
			}

			$filter_options = [];

			foreach ($filter->children($ns['f'])->Query as $option) {
				$filter_options[(string) $option->attributes($ns['f'])->filter] = (string) $option->attributes()->totalResults;

				foreach ($option->children($ns['f'])->filter as $subfilter) {
					$filter_sub_options = [];

					foreach ($subfilter->children($ns['f'])->Query as $suboption) {
						$filter_sub_options[(string) $suboption->attributes($ns['f'])->filter] = (string) $suboption->attributes()->totalResults;
					}

					$filter_results[(string) $subfilter->attributes()->name] = array_merge($filter_sub_options, $filter_results[(string) $subfilter->attributes()->name] ?? []);
				}
			}

			$filter_results[(string) $filter->attributes()->name] = $filter_options;
		}

		echo json_encode([
			'content' => $content,
			'filter' => [
				'results' => $filter_results,
			]
		]);
		wp_die();
	}
}
