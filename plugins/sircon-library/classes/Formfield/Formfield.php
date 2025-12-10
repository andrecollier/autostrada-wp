<?php

namespace Sircon\Library\Formfield;

abstract class Formfield {

	private $id;

	private $classes = [];

	private $attributes = ['autocomplete="off"'];

	private $output = '';

	private $is_multifield_part = false;

	private $table_layout = false;

	private $input_classes = ['form-control'];

	public function __construct(string $name, string $output = '') {
		$this->output = $output;
		$this->id = uniqid($name . '_formfield_');
	}

	public function addClass(string $class): self {
		$this->classes[] = $class;
		return $this;
	}

	public function removeClass(string $class): self {
		$index = array_search($class, $this->classes);
		if ($index !== false) {
			unset($this->classes[$index]);
		}

		return $this;
	}

	protected function getInputClasses(): string {
		return implode(' ', $this->input_classes);
	}

	protected function getClasses(): string {
		return implode(' ', $this->classes);
	}

	public function addAttribute(string $attribute): self {
		$this->attributes[] = $attribute;
		return $this;
	}

	protected function getAttributes(): string {
		return implode(' ', $this->attributes);
	}

	public function addInputClass(string $class): self {
		$this->input_classes[] = $class;
		return $this;
	}

	public function removeInputClass(string $class): self {
		$index = array_search($class, $this->input_classes);
		if ($index !== false) {
			unset($this->input_classes[$index]);
		}

		return $this;
	}

	protected function getId(): string {
		return $this->id;
	}

	protected function getNameAttributeName(): string {
		return $this->is_multifield_part ? 'data-name' : 'name';
	}

	public function getOutput(): string {
		return $this->output;
	}
	public function setPartOfMultifield(bool $is_part): self {
		$this->is_multifield_part = $is_part;
		return $this;
	}

	public function setTableLayout(bool $table_layout): self {
		$this->table_layout = $table_layout;
		return $this;
	}

	public function hasTableLayout(): bool {
		return $this->table_layout;
	}

	public function isSaveable(): bool {
		return false;
	}

	public function output(): void {
		echo $this->getOutput();
	}

	public function __toString(): string {
		return $this->getOutput();
	}
}
