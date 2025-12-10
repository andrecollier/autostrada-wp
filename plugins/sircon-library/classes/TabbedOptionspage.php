<?php

namespace Sircon\Library;

class TabbedOptionspage extends Optionspage {

	private $tabs = [];

	public function addTab(Optionspage $optionspage): self {
		$optionspage->setMenuType(Optionspage::MENU_TYPE_TAB);
		$this->tabs[$optionspage->getId()] = $optionspage;

		return $this;
	}

	public function output() {
		?>
		<div class="wrap">
			<h1><?= $this->getPageTitle(); ?></h1>
			<?php if ($this->tabs) {
				uasort($this->tabs, function ($a, $b) {
					return $a->getPosition() <=> $b->getPosition();
				});

				$active = $this->tabs[$_GET['tab'] ?? ''] ?? current($this->tabs);
				?>
				<h2 class="nav-tab-wrapper">
					<?php foreach ($this->tabs as $tab) { ?>
						<a href="?<?= http_build_query(['page' => $this->getId(), 'tab' => $tab->getId()]); ?>" class="nav-tab<?= ($active->getId() === $tab->getId() ? ' nav-tab-active' : '') ; ?>"><?= $tab->getMenuTitle(); ?></a>
					<?php } ?>
				</h2>
				<?php $active->output(); ?>
			<?php } ?>
		</div>
		<?php
	}
}
