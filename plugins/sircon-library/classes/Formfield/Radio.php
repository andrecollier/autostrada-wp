<?php

namespace Sircon\Library\Formfield;

class Radio extends Formfield {

	private $name;

	private $label;

	private $options;

	private $selected;

	private $helper_text;

	public function __construct(string $name, string $label, array $options, string $selected = '', string $default = '') {
		parent::__construct($name);
		$this->name = $name;
		$this->label = $label;
		$this->options = $options;
		if (!array_key_exists($selected, $options)) {
			$selected = '';
		}

		if (!$selected) {
			$selected = $default;
		}

		$this->selected = $selected;
	}

	public function getName(): string {
		return $this->name;
	}

	public function setName(string $name): self {
		$this->name = $name;
		return $this;
	}

	public function setHelperText(string $helper_text): self {
		$this->helper_text = $helper_text;
		return $this;
	}

	public function setValue(string $value): self {
		$this->selected = $value;
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
					<label><?= $this->label; ?></label>
				</th>
				<td>
					<fieldset>
		<?php } else { ?>
			<div class="form-group <?= $this->getClasses(); ?>">
				<label><?= $this->label; ?></label>
		<?php } ?>
			<?php
			$first = true;
			foreach ($this->options as $value => $label) {
				if (!$first) { ?>
					<br />
				<?php } ?>
				<label>
					<input type="radio" name="<?= $this->name; ?>" class="<?= $this->getInputClasses(); ?>" value="<?= $value; ?>" <?= $this->selected == $value ? ' checked="checked"' : '';?><?= $this->getAttributes(); ?>>
					<?= $label; ?>
				</label>
				<?php
				$first = false;
			} ?>
			<?php if ($this->helper_text) { ?>
				<p id="<?= $this->getId() . '_help'; ?>" class="form-text text-muted description"><?= $this->helper_text; ?></p>
			<?php } ?>

		<?php if ($this->hasTableLayout()) { ?>
					</fieldset>
				</td>
			</tr>
		<?php } else { ?>
			</div>
		<?php } ?>
		<?php return ob_get_clean();
	}
}
