<?php

namespace Sircon\Library\Formfield;

abstract class Input extends Formfield implements MultifieldSupport {

	private $type = '';

	private $name = '';

	private $label = '';

	private $value = '';

	private $placeholder = '';

	private $helper_text = '';

	private $readonly = false;

	private $required = false;

	private $pattern = '';

	private $extra_input_group_elements_before = '';

	private $extra_input_group_elements_after = '';

	private $input_group_classes = ['input-group'];

	private $input_group_attributes = [];

	private $label_classes = [];

	public function __construct(string $type, string $name, string $label, string $value = '', string $placeholder = '') {
		parent::__construct($name);
		$this->type = $type;
		$this->name = $name;
		$this->label = $label;
		$this->value = $value;
		$this->placeholder = $placeholder;

		$this->addInputClass('regular-text');
	}

	public function getType(): string {
		return $this->type;
	}

	public function setValue(string $value): self {
		$this->value = $value;
		return $this;
	}

	public function getName(): string {
		return $this->name;
	}

	public function setName(string $name): self {
		$this->name = $name;
		return $this;
	}

	public function getValue(): string {
		return $this->value;
	}

	public function getPlaceholder(): string {
		return $this->placeholder;
	}

	public function getLabel(): string {
		return $this->label;
	}

	public function addLabelClass(string $class): self {
		$this->label_classes[] = $class;
		return $this;
	}

	protected function getLabelClasses(): string {
		if ($this->label_classes) {
			return ' class="' . implode(' ', $this->label_classes) . '" ';
		}

		return '';
	}

	public function addInputGroupClass(string $class): self {
		$this->input_group_classes[] = $class;
		return $this;
	}

	protected function getInputGroupClasses(): string {
		return implode(' ', $this->input_group_classes);
	}

	public function addInputGroupAttribute(string $attribute): self {
		$this->input_group_attributes[] = $attribute;
		return $this;
	}

	protected function getInputGroupAttributes(): string {
		return implode(' ', $this->input_group_attributes);
	}

	public function setExtraInputGroupElementBefore(string $content): self {
		$this->extra_input_group_elements_before = $content;
		return $this;
	}

	public function getExtraInputGroupElementBefore(): string {
		if ($this->extra_input_group_elements_before) {
			return '<span class="input-group-prepend">' . $this->extra_input_group_elements_before . '</span>';
		}

		return '';
	}

	public function setExtraInputGroupElementAfter(string $content): self {
		$this->extra_input_group_elements_after = $content;
		return $this;
	}

	public function getExtraInputGroupElementAfter(): string {
		if ($this->extra_input_group_elements_after) {
			return '<span class="input-group-append">' . $this->extra_input_group_elements_after . '</span>';
		}

		return '';
	}

	public function setHelperText(string $helper_text): self {
		$this->helper_text = $helper_text;
		return $this;
	}

	public function getHelperText(): string {
		return $this->helper_text;
	}

	public function setRequired(bool $required = true): self {
		$this->required = $required;
		return $this;
	}

	public function setReadonly(bool $readonly = true): self {
		$this->readonly = $readonly;
		return $this;
	}

	public function setPattern(string $pattern): self {
		$this->pattern = $pattern;
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
					<label for="<?= $this->getId(); ?>"<?= $this->getLabelClasses(); ?>><?= $this->label; ?></label>
				</th>
				<td>
		<?php } else { ?>
			<div class="form-group <?= $this->getClasses(); ?>">
				<label for="<?= $this->getId(); ?>"<?= $this->getLabelClasses(); ?>><?= $this->label; ?></label>
		<?php } ?>

			<div class="<?= $this->getInputGroupClasses(); ?>" <?= $this->getInputGroupAttributes(); ?>>
				<?= $this->getExtraInputGroupElementBefore(); ?>
				<input id="<?= $this->getId(); ?>"
					type="<?= $this->type; ?>"
					class="<?= $this->getInputClasses(); ?>"
					<?= $this->getNameAttributeName(); ?>="<?= $this->name; ?>"
					value="<?= htmlentities($this->value); ?>"
					placeholder="<?= $this->placeholder; ?>"
					<?= ($this->helper_text ? 'aria-describedby="' . $this->getId() . '_help"' : '') ?>
					<?= ($this->pattern ? 'pattern="' . $this->pattern . '"' : '') ?>
					<?= $this->getAttributes(); ?>
				>
				<?= $this->getExtraInputGroupElementAfter(); ?>
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
