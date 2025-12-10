<?php

namespace Sircon\Library\Formfield;

class Editor extends Formfield {

	private $name;

	private $label;

	private $value;

	private $helper_text;

	public function __construct(string $name, string $label, string $value = '') {
		parent::__construct($name);
		$this->name = $name;
		$this->label = $label;
		$this->value = $value;
	}

	public function getName(): string {
		return $this->name;
	}

	public function setName(string $name): self {
		$this->name = $name;
		return $this;
	}

	public function setValue(string $value): self {
		$this->value = $value;
		return $this;
	}

	public function setHelperText(string $helper_text): self {
		$this->helper_text = $helper_text;
		return $this;
	}

	public function isSaveable(): bool {
		return true;
	}

	public function getOutput(): string {
		ob_start();
		if ($this->hasTableLayout()) { ?>
			<tr class="<?= $this->getClasses(); ?>">
				<th>
					<label for="<?= $this->getId(); ?>"><?= $this->label; ?></label>
				</th>
				<td>
		<?php } else { ?>
			<div class="form-group <?= $this->getClasses(); ?>">
				<label for="<?= $this->getId(); ?>"><?= $this->label; ?></label>
		<?php } ?>
			<?php wp_editor($this->value, $this->getId(), ['textarea_name' => $this->getName()]); ?>
			<?php if ($this->helper_text) { ?>
				<p id="<?= $this->getId() . '_help'; ?>" class="form-text text-muted"><?= $this->helper_text; ?></p>
			<?php } ?>

		<?php if ($this->hasTableLayout()) { ?>
				</td>
			</tr>
		<?php } else { ?>
			</div>
		<?php } ?>
		<?php return ob_get_clean();
	}
}
