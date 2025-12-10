<?php

namespace Sircon\Library\Formfield;

class Color extends Input {

	public function __construct(string $name, string $label, string $value = '') {
		parent::__construct('text', $name, $label, $value);
		$this->addInputClass('sircon-colorpicker');
	}
}
