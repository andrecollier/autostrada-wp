<?php

namespace Sircon\Library\Formfield;

class Password extends Input {

	public function __construct(string $name, string $label, string $value = '', string $placeholder = '') {
		parent::__construct('password', $name, $label, $value, $placeholder);
	}
}
