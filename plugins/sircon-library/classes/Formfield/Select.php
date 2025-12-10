<?php

namespace Sircon\Library\Formfield;

class Select extends Formfield implements MultifieldSupport {

	protected $name;

	protected $label;

	protected $options;

	protected $selected;

	protected $default;

	protected $helper_text;

	protected $required;

	public function __construct(string $name, string $label, array $options, string $selected = '', string $default = '') {
		parent::__construct($name);
		$this->name = $name;
		$this->label = $label;
		$this->options = $options;
		if (!array_key_exists($selected, $options)) {
			$selected = '';
		}

		$this->selected = $selected;
		$this->default = $default;
	}

	public function getName(): string {
		return $this->name;
	}

	public function setName(string $name): self {
		$this->name = $name;
		return $this;
	}

	public function setValue(string $value): self {
		$this->selected = $value;
		return $this;
	}

	public function setHelperText(string $helper_text): self {
		$this->helper_text = $helper_text;
		return $this;
	}

	public function setRequired(bool $required = true): self {
		$this->required = $required;
		return $this;
	}

	public function isSaveable(): bool {
		return true;
	}

	public function getOutput(): string {
		if ($this->required) {
			$this->addAttribute('required');
		}

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
			<div class="input-group">
				<select id="<?= $this->getId(); ?>"
						class="<?= $this->getInputClasses(); ?>"
						<?= $this->getNameAttributeName(); ?>="<?= $this->name; ?>"
						<?= ($this->helper_text ? 'aria-describedby="' . $this->getId() . '_help" ' : '') ?>
						<?= $this->getAttributes(); ?>
					>
				<?php if ($this->default) { ?>
					<option disabled<?= $this->selected ? '' : ' selected';?> value=""><?= $this->default; ?></option>
				<?php } ?>
				<?php foreach ($this->options as $value => $label) { ?>
					<option<?= $this->selected == $value ? ' selected' : '';?> value="<?= $value; ?>"><?= $label; ?></option>
				<?php } ?>
				</select>
			</div>
			<?php if ($this->helper_text) { ?>
				<p id="<?= $this->getId() . '_help'; ?>" class="form-text text-muted description"><?= $this->helper_text; ?></p>
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
