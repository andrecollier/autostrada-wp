<?php

namespace Sircon\Library\Content;

class Tabs {

	protected static $tabs_index = 0;

	protected $index = 0;

	protected $tabs = [];

	public function __construct() {
		$this->index = self::$tabs_index++;
	}

	public function addTab(string $title, string $content): self {
		$this->tabs[] = [
			'title' => $title,
			'content' => $content,
		];

		return $this;
	}

	public function print() {
		$titles = [];
		$contents = [];

		foreach ($this->tabs as $i => $tab) {
			$slug = sanitize_title($tab['title']) . '_' . $this->index . '_' . $i;
			$titles[$slug] = $tab['title'];
			$contents[$slug] = $tab['content'];
		}
		?>
		<div id="sircon-tabs-<?= $this->index; ?>" class="sircon-tabs">
			<h2 class="nav-tab-wrapper">
				<?php foreach ($titles as $slug => $title) { ?>
					<a href="#<?= $slug; ?>" class="nav-tab"><?= $title; ?></a>
				<?php } ?>
			</h2>
			<?php foreach ($contents as $slug => $content) { ?>
				<div id="<?= $slug; ?>"><?= $content; ?></div>
			<?php } ?>
		</div>
		<?php
	}

	public function __toString(): string {
		ob_start();
		$this->print();
		return ob_get_clean();
	}
}
