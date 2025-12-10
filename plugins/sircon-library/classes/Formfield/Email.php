<?php

namespace Sircon\Library\Formfield;

class Email extends Input {

	public function __construct(string $name, string $label, string $value = '', string $placeholder = '') {
		parent::__construct('email', $name, $label, $value, $placeholder);
	}
}
