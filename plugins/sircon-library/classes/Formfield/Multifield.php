<?php

namespace Sircon\Library\Formfield;

use Exception;

class Multifield extends Formfield implements MultifieldSupport {

	private $fields;

	private $name;

	private $label;

	private $values;

	private $adder_label;

	private $sortable = false;

	private $field_row_classes = ['card'];

	private $field_wrapper_classes = [];

	private $close_button_content = 'X';

	private $close_button_classes = ['multifield-action-button', 'multifield-remove-row', 'button', 'button-secondary'];

	public function __construct(string $name, string $label = '', string $value = '[]') {
		parent::__construct($name);
		$this->name = $name;
		$this->label = $label;
		$this->values = json_decode($value, true) ?? [];
		$this->adder_label = __('Add row', 'sircon-library');
	}

	public function isSaveable(): bool {
		return true;
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

	public function setValue(string $value): self {
		$this->values = json_decode($value, true) ?? [];
		return $this;
	}

	public function setAdderLabel(string $adder_label): self {
		$this->adder_label = $adder_label;
		return $this;
	}

	public function setSortable(bool $sortable = true): self {
		$this->sortable = $sortable;
		return $this;
	}

	public function setCloseButtonContent(string $content): self {
		$this->close_button_content = $content;
		return $this;
	}

	public function addFieldRowClass(string $class): self {
		$this->field_row_classes[] = $class;
		return $this;
	}

	public function removeFieldRowClass(string $class): self {
		$index = array_search($class, $this->field_row_classes);
		if ($index !== false) {
			unset($this->field_row_classes[$index]);
		}
		return $this;
	}

	public function addFieldWrapperClass(string $class): self {
		$this->field_wrapper_classes[] = $class;
		return $this;
	}

	public function addCloseButtonClass(string $class): self {
		$this->close_button_classes[] = $class;
		return $this;
	}

	public function getCloseButtonContent(): string {
		return $this->close_button_content;
	}

	public function getFieldRowClasses(): string {
		return implode(' ', $this->field_row_classes);
	}

	public function getFieldWrapperClasses(): string {
		return implode(' ', $this->field_wrapper_classes);
	}

	public function getCloseButtonClasses(): string {
		return implode(' ', $this->close_button_classes);
	}

	public function addFormfield(Formfield $Field): self {
		if (!in_array('Sircon\Library\Formfield\MultifieldSupport', class_implements($Field))) {
			/* translators: %s will be replaced by the class of the Formfield */
			throw new Exception(sprintf(__('Field type [%s] not supported in multifields', 'sircon-library'), get_class($Field)));
		}

		$this->fields[] = $Field;
		return $this;
	}

	public function getOutput(): string {
		$template_elements = '';
		foreach ($this->fields as $Field) {
			$Field->setPartOfMultifield(true);
			$template_elements .= (clone $Field)->getOutput();
		}

		$close_button = '<button type="button" class="' . $this->getCloseButtonClasses() . '" data-action="multifield-remove-row">' . $this->getCloseButtonContent() . '</button>';

		$label = '';
		if ($this->label) {
			$label = '<label>' . $this->label . '</label>';
		}

		$wrapper_classes = 'multifieldwrap';
		if ($this->sortable) {
			$wrapper_classes .= ' multifield-sortable';
		}

		$multifield = '<div class="field-wrapper ' . $this->getFieldWrapperClasses() . '">';
		$sortable_handle = '<div class="sortable-handle"></div>';

		foreach ($this->values as $row_values) {
			$multifield .= '<div class="multifield-row ' . $this->getFieldRowClasses() . '">';
			$multifield .= $close_button;
			if ($this->sortable) {
				$multifield .= $sortable_handle;
			}

			foreach ($this->fields as $Field) {
				$Field = clone $Field;
				if (is_a($Field, self::class)) {
					$Field->setValue($row_values[$Field->getName()] ?? '[]');
				} else {
					$Field->setValue($row_values[$Field->getName()] ?? '');
				}

				$multifield .= $Field->getOutput();
			}

			$multifield .= '</div>';
		}

		$multifield .= '</div>';

		$multifield .= '<div class="multifield-row-template hidden ' . $this->getFieldRowClasses() . '">' . $close_button . ($this->sortable ? $sortable_handle : '') . $template_elements . '</div>';
		$multifield .= '<button type="button" class="multifield-action-button multifield-add-row button button-primary" data-action="multifield-add-row">' . $this->adder_label . '</button>';

		$multifield .= '<input type="hidden" class="multifield-json" ' . $this->getNameAttributeName() . '="' . $this->name . '" value="' . htmlentities(json_encode($this->values)) . '" />';

		if ($this->hasTableLayout()) {
			return sprintf('<tr><th><label>%s</label></th><td><div class="' . $wrapper_classes . ' ' . $this->getClasses() . '" ' . $this->getAttributes() . '>%s</div></td></tr>', $label, $multifield);
		} else {
			return sprintf('<div class="' . $wrapper_classes . ' ' . $this->getClasses() . '" ' . $this->getAttributes() . '>%s</div>', $label . $multifield);
		}
	}
}
