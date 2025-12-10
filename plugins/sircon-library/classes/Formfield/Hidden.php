<?php

namespace Sircon\Library\Formfield;

class Hidden extends Formfield implements MultifieldSupport {

	private $name;

	private $value;

	public function __construct(string $name, string $value) {
		parent::__construct($name);
		$this->name = $name;
		$this->value = $value;
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

	public function isSaveable(): bool {
		return true;
	}

	public function getOutput(): string {
		return '<input type="hidden" ' . $this->getNameAttributeName() . '="' . $this->name . '" value="' . $this->value . '">';
	}
}
