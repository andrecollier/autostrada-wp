<?php

namespace Sircon\Library\Content;

class Swiper {

	private $id;

	private $autoconfig = true;

	private $config = [
		'loop' =>  true,
		'keyboard' => true,
		'pagination' => [
			'clickable' => true,
			'dynamicBullets' => true,
			'el' =>  '.swiper-pagination',
		],
		'navigation' => [
			'nextEl' => '.swiper-button-next',
			'prevEl' => '.swiper-button-prev',
		],
		'output' => [
			'navigation' => true,
			'pagination' => true,
		]
	];

	private $slides = [];

	public function __construct(string $id) {
		$this->id = $id;
	}

	public function addSlide(string $slide): self {
		$this->slides[] = $slide;

		return $this;
	}

	public function getConfig(): array {
		return $this->config;
	}

	public function setConfig(array $config): self {
		$this->config = $config;

		return $this;
	}

	public function setAutoconfig(bool $autoconfig): self {
		$this->autoconfig = $autoconfig;

		return $this;
	}

	public function generate() {
		ob_start();
		?>
		<!-- Slider main container -->
		<div class="swiper swiper-container" id="<?= $this->id; ?>" <?= $this->autoconfig ? 'data-autoconfig="true" data-config="' . htmlentities(json_encode($this->config)) . '"' : 'data-autoconfig="false"'; ?>>
			<!-- Additional required wrapper -->
			<div class="swiper-wrapper">
				<!-- Slides -->
				<?php foreach ($this->slides as $slide) { ?>
					<div class="swiper-slide"><?= $slide; ?></div>
				<?php } ?>
			</div>

			<!-- If we need pagination -->
			<?php if (($this->config['pagination'] ?? false) && ($this->config['output']['pagination'] ?? true)) { ?>
				<div class="swiper-pagination"></div>
			<?php } ?>

			<!-- If we need navigation buttons -->
			<?php if (($this->config['navigation'] ?? false) && ($this->config['output']['navigation'] ?? true)) { ?>
				<div class="swiper-button-prev"></div>
				<div class="swiper-button-next"></div>
			<?php } ?>

			<!-- If we need scrollbar -->
			<?php if ($this->config['scrollbar'] ?? false) { ?>
				<div class="swiper-scrollbar"></div>
			<?php } ?>
		</div>
		<?php
		return ob_get_clean();
	}
}
