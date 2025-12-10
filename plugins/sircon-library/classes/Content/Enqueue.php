<?php

namespace Sircon\Library\Content;

final class Enqueue {

	public static function scrolledDown(): void {
		wp_enqueue_script('sircon-scrolled-down');
	}

	public static function equalHeightBoxes(): void {
		wp_enqueue_script('sircon-equal-height-boxes');
	}

	public static function addClassWhenVisible(): void {
		wp_enqueue_script('sircon-class-when-visible');
	}

	public static function dynamicImages(): void {
		wp_enqueue_style('sircon-dynamic-images');
		wp_enqueue_script('sircon-dynamic-images');
	}

	public static function calendar(): void {
		wp_enqueue_style('sircon-calendar');
		wp_enqueue_script('sircon-calendar');
	}

	public static function slick(bool $with_theme = true): void {
		wp_enqueue_style('slick');
		wp_enqueue_script('slick');
		if ($with_theme) {
			wp_enqueue_style('slick-theme');
		}
	}

	public static function swiper(bool $with_autoconfig = true): void {
		wp_enqueue_style('swiper');
		wp_enqueue_script('swiper');
		if ($with_autoconfig) {
			wp_enqueue_script('swiper-autoconfig');
		}
	}
}
