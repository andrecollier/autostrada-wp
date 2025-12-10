<?php

namespace sircon\finncars;

use sircon\Options;

Options::add_page(FinnCars::OPTIONSPAGE_ID, [
	'title' => __('Finn cars', 'sircon-finn-cars'),
	'tab_title' => __('Settings', 'sircon-finn-cars'),
	'type' => 'main',
	'icon' => 'dashicons-index-card',
]);

$pages = [
	__('Select page', 'sircon-finn-cars') => 0,
];

$clear_cache_href = '';

if (is_admin() && !empty($_GET['page']) && $_GET['page'] == 'sircon_finn_cars') {
	$args = [
		'post_type' => 'page',
		'posts_per_page' => -1,
		'post__not_in' => [\get_option('page_on_front'), \get_option('page_for_posts')],
	];

	if (function_exists('pll_current_language')) {
		$slug = pll_default_language();
		$args['lang'] = $slug;
	}

	$posts = get_posts($args);
	foreach ($posts as $post) {
		$pages["[{$post->ID}] {$post->post_title}"] = $post->ID;
	}

	$clear_cache_href = $_SERVER['REQUEST_URI'] . '&clear-finn-cache';
}


Options::add_options(FinnCars::OPTIONSPAGE_ID, [
	[
		'type' => 'custom',
		'value' => (isset($_GET['clear-finn-cache']) ? '<div class="button" style="background:green;color:#fff;border:green;pointer-events:none;">' . __('Cache was cleared', 'sircon-finn-cars') . '</div>' : '<a href="' . $clear_cache_href . '" class="button">' . __('Clear Finn API Cache', 'sircon-finn-cars') . '</a>'),
	],
	[
		'name' => 'api_key',
		'label' => __('API key', 'sircon-finn-cars'),
	],
	[
		'name' => 'org_id',
		'label' => __('Org ID', 'sircon-finn-cars'),
	],
	[
		'name' => 'cars_page_id',
		'label' => __('Cars page ID', 'sircon-finn-cars'),
		'tooltip' => __('Used for the archive view, and as a base for single view permalinks', 'sircon-finn-cars'),
		'type' => 'select',
		'options' => $pages,
	],
	[
		'name' => 'cache_max_age',
		'label' => __('Cache max age, in minutes', 'sircon-finn-cars'),
		'default' => '1440',
	],
	[
		'label' => __('Archive fields', 'sircon-finn-cars'),
		'name' => 'features',
		'type' => 'pagebreak',
	],
	[
		'type' => 'custom',
		'value' => '<p>' . __('Which data fields should be visible on the archive page?', 'sircon-finn-cars') . '</p>',
	],
	[
		'name' => 'summary_show_in_archive',
		'label' => __('Summary', 'sircon-finn-cars'),
		'type' => 'checkbox',
		'value' => 'yes',
	],
	[
		'name' => 'price_main_show_in_archive',
		'label' => __('Price', 'sircon-finn-cars'),
		'type' => 'checkbox',
		'value' => 'yes',
	],
	[
		'name' => 'mileage_show_in_archive',
		'label' => __('Mileage', 'sircon-finn-cars'),
		'type' => 'checkbox',
		'value' => 'yes',
	],
	[
		'name' => 'engine_effect_show_in_archive',
		'label' => __('Engine effect', 'sircon-finn-cars'),
		'type' => 'checkbox',
		'value' => 'yes',
	],
	[
		'name' => 'engine_fuel_show_in_archive',
		'label' => __('Engine fuel', 'sircon-finn-cars'),
		'type' => 'checkbox',
		'value' => 'yes',
	],
	[
		'name' => 'city_show_in_archive',
		'label' => __('City', 'sircon-finn-cars'),
		'type' => 'checkbox',
		'value' => 'yes',
	],
	[
		'name' => 'dealer_show_in_archive',
		'label' => __('Dealer', 'sircon-finn-cars'),
		'type' => 'checkbox',
		'value' => 'yes',
	],
	[
		'name' => 'updated_show_in_archive',
		'label' => __('Last updated date', 'sircon-finn-cars'),
		'type' => 'checkbox',
		'value' => 'yes',
	],
	[
		'label' => __('Filters', 'sircon-finn-cars'),
		'name' => 'filters',
		'type' => 'pagebreak',
	],
	[
		'name' => 'show_total_results',
		'label' => 'Vis antall resultater bak filter',
		'type' => 'select',
		'options' => [
			'Nei' => 'no',
			'Ja' => 'yes',
		]
	],
	[
		'name' => 'show_total_results_subfilter',
		'label' => 'Vis antall resultater bak under-filter',
		'type' => 'select',
		'options' => [
			'Nei' => 'no',
			'Ja' => 'yes',
		]
	],
	[
		'type' => 'custom',
		'value' => '<p>' . __('Which filters should be visible on the archive page?', 'sircon-finn-cars') . '</p>',
	],
	[
		'name' => 'filter_year',
		'label' => 'Årsmodell',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_engine_effect',
		'label' => 'Hestekrefter',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_mileage',
		'label' => 'Kilometerstand',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_number_of_seats',
		'label' => 'Antall seter',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_make',
		'label' => 'Merke',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_registration_class',
		'label' => 'Avgiftsklasse',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_engine_fuel',
		'label' => 'Drivstoff',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_warranty_insurance',
		'label' => 'Garanti og forsikringer',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_max_trailer_weight',
		'label' => 'Tilhengervekt',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_wheel_sets',
		'label' => 'Hjulsett',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_sales_form',
		'label' => 'Salgsform',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_exterior_colour',
		'label' => 'Farge',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_published',
		'label' => 'Publisert',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_car_equipment',
		'label' => 'Utstyr',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_transmission',
		'label' => 'Girkasse',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_dealer_segment',
		'label' => 'Annonsør',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_wheel_drive',
		'label' => 'Hjuldrift',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_condition',
		'label' => 'Bilens tilstand',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_motor_ad_location',
		'label' => 'Kjøretøyet står i',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_location',
		'label' => 'Område',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_price',
		'label' => 'Pris',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_body_type',
		'label' => 'Karosseri',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_price_changed',
		'label' => 'Redusert pris',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'name' => 'filter_sortable',
		'label' => 'Sortering',
		'type' => 'select',
		'options' => [
			__('Show filter', 'sircon-finn-cars') => 'show',
			__('Hide filter', 'sircon-finn-cars') => 'hide',
		]
	],
	[
		'label' => __('Dealers', 'sircon-finn-cars'),
		'name' => 'dealers',
		'type' => 'pagebreak',
	],
	[
		'type' => 'custom',
		'value' => '<p>' . __('Enables dealership filtering. For dealers with multiple contacts, separate with | (pipe)', 'sircon-finn-cars') . '</p>',
	],
	[
		'type' => 'multiple',
		'name' => 'dealers',
		'adder_label' => __('Add new dealer', 'sircon-finn-cars'),
		'template' => [
			[
				'name' => 'orgId',
				'label' => __('Org ID', 'sircon-finn-cars'),
			],
			[
				'name' => 'name',
				'label' => __('Dealer Name', 'sircon-finn-cars'),
			],
			[
				'name' => 'email',
				'label' => __('Email (separate multiple with |)', 'sircon-finn-cars'),
				'placeholder' => 'email1@domain.com|email2@domain.com',
			],
			[
				'name' => 'phone',
				'label' => __('Phone (separate multiple with |)', 'sircon-finn-cars'),
				'placeholder' => '12 34 56 78|87 65 43 21',
			],
			[
				'name' => 'contact_names',
				'label' => __('Contact Names (separate multiple with |)', 'sircon-finn-cars'),
				'placeholder' => 'Geir Arne Svartdal|Rune Johansen',
			],
		]
	],
]);