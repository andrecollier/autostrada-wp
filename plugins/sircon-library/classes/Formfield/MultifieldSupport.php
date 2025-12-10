<?php

namespace Sircon\Library\Formfield;

interface MultifieldSupport {
	public function setPartOfMultifield(bool $is_part): Formfield;
	public function getName(): string;
	public function setValue(string $value): Formfield;
}
