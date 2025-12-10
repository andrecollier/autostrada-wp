<?php

namespace Sircon\Library\Formfield;

class Text extends Input {

	public function __construct(string $name, string $label, string $value = '', string $placeholder = '') {
		parent::__construct('text', $name, $label, $value, $placeholder);
	}
}
