<?php

namespace Sircon\Library\Formfield;

class Custom extends Formfield implements MultifieldSupport {

	private $content = '';

	public function __construct(string $content) {
		parent::__construct('custom', $content);
		$this->content = $content;
	}

	public function getName(): string {
		return 'custom';
	}

	public function setValue(string $value): self {
		return $this;
	}

	public function getOutput(): string {
		ob_start();
		if ($this->hasTableLayout()) { ?>
			<tr>
				<td colspan="2">
					<div class="custom-field"><?= $this->content; ?></div>
				</td>
			</tr>
			<?php
		} else {
			echo $this->content;
		}

		return ob_get_clean();
	}
}
