<?php

namespace Sircon\Library\Formfield;

class Checkbox extends Input {

	private $description = '';

	public function __construct(string $name, string $label, string $description = '', string $value = '0') {
		parent::__construct('checkbox', $name, $label, $value);
		$this->description = $description;
	}

	public function getOutput(): string {
		ob_start();
		if ($this->hasTableLayout()) { ?>
			<tr class="<?= $this->getClasses(); ?>">
				<th>
					<label <?= $this->getLabelClasses(); ?>><?= $this->getLabel(); ?></label>
				</th>
				<td>
					<fieldset>
		<?php } else { ?>
			<div class="form-group <?= $this->getClasses(); ?>">
				<label <?= $this->getLabelClasses(); ?>><?= $this->getLabel(); ?></label>
		<?php } ?>

			<div class="<?= $this->getInputGroupClasses(); ?>" <?= $this->getInputGroupAttributes(); ?>>
				<?= $this->getExtraInputGroupElementBefore(); ?>
				<legend class="screen-reader-text"><span><?= $this->getLabel(); ?></span></legend>
				<label for="<?= $this->getId(); ?>">
					<input id="<?= $this->getId(); ?>"
						type="<?= $this->getType(); ?>"
						class="<?= $this->getInputClasses(); ?>"
						<?= $this->getNameAttributeName(); ?>="<?= $this->getName(); ?>"
						value="1"
						<?= ($this->getValue() === '1' ? 'checked="checked"' : '') ?>
						<?= ($this->getHelperText() ? 'aria-describedby="' . $this->getId() . '_help"' : '') ?>
						<?= $this->getAttributes(); ?>
					>
					<?= $this->description; ?>
				</label>
				<?= $this->getExtraInputGroupElementAfter(); ?>
			</div>
			<?php if ($this->getHelperText()) { ?>
				<p id="<?= $this->getId() . '_help'; ?>" class="form-text text-muted description"><?= $this->getHelperText(); ?></p>
			<?php } ?>

		<?php if ($this->hasTableLayout()) { ?>
					</fieldset>
				</td>
			</tr>
		<?php } else { ?>
			</div>
		<?php } ?>

		<?php return ob_get_clean();
	}
}
