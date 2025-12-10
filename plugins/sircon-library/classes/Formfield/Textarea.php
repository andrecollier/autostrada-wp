<?php

namespace Sircon\Library\Formfield;

class Textarea extends Formfield implements MultifieldSupport {

	private $name;

	private $label;

	private $value;

	private $placeholder;

	private $helper_text;

	private $readonly;

	private $required;

	public function __construct(string $name, string $label, string $value = '', string $placeholder = '') {
		parent::__construct($name);
		$this->name = $name;
		$this->label = $label;
		$this->value = $value;
		$this->placeholder = $placeholder;

		$this->addInputClass('regular-text');
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

	public function setRequired(bool $required = true): self {
		$this->required = $required;
		return $this;
	}

	public function setReadonly(bool $readonly = true): self {
		$this->readonly = $readonly;
		return $this;
	}

	public function isSaveable(): bool {
		return true;
	}

	public function getOutput(): string {
		if ($this->required) {
			$this->addAttribute('required');
		}

		if ($this->readonly) {
			$this->addAttribute('readonly');
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
				<textarea id="<?= $this->getId(); ?>"
					class="<?= $this->getInputClasses(); ?>"
					<?= $this->getNameAttributeName(); ?>="<?= $this->name; ?>"
					placeholder="<?= $this->placeholder; ?>"
					<?= ($this->helper_text ? 'aria-describedby="' . $this->getId() . '_help" ' : '') ?>
					<?= $this->getAttributes(); ?>
					rows="5"
				><?= $this->value; ?></textarea>
			</div>
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
