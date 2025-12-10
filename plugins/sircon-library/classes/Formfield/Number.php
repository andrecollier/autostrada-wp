<?php

namespace Sircon\Library\Formfield;

class Number extends Input {

	public function __construct(string $name, string $label, string $value = '', string $placeholder = '') {
		parent::__construct('number', $name, $label, $value, $placeholder);
	}
}
