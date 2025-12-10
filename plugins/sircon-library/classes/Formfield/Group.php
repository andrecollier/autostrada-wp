<?php

namespace Sircon\Library\Formfield;

class Group extends Formfield {

	private $fields;

	public function __construct() {
		parent::__construct('wrapper');
	}

	public function addFormfield(Formfield $Field): self {
		$this->fields[] = $Field;
		return $this;
	}

	public function getOutput(): string {
		$formfields = '';
		foreach ($this->fields as $Field) {
			$formfields .= $Field->getOutput();
		}

		return sprintf('<div class="form-row ' . $this->getClasses() . '" ' . $this->getAttributes() . '>%s</div>', $formfields);
	}
}
