<?php

namespace Sircon\Library\Formfield;

class GroupedSelect extends Select {

	public function getOutput(): string {
		if ($this->required) {
			$this->addAttribute('required');
		}

		ob_start();
		if ($this->hasTableLayout()) { ?>
			<tr class="<?= $this->getClasses(); ?>">
				<th>
					<label for="<?= $this->getId(); ?>"><?= $this->label; ?></label>
				</th>
				<td>
		<?php } else { ?>
			<div class="form-group <?= $this->getClasses(); ?>">
				<label for="<?= $this->getId(); ?>"><?= $this->label; ?></label>
		<?php } ?>

			<select id="<?= $this->getId(); ?>"
					class="<?= $this->getInputClasses(); ?>"
					<?= $this->getNameAttributeName(); ?>="<?= $this->name; ?>"
					<?= ($this->helper_text ? 'aria-describedby="' . $this->getId() . '_help" ' : '') ?>
					<?= $this->getAttributes(); ?>
				>
			<?php if ($this->default) { ?>
				<option disabled<?= $this->selected ? '' : ' selected';?> value=""><?= $this->default; ?></option>
			<?php } ?>
			<?php foreach ($this->options as $group) { ?>
				<optgroup label="<?= $group['label'] ?? ''?>">
					<?php foreach ($group['options'] ?? [] as $value => $label) { ?>
						<option<?= $this->selected == $value ? ' selected' : '';?> value="<?= $value; ?>"><?= $label; ?></option>
					<?php } ?>
				</optgroup>
			<?php } ?>
			</select>
			<?php if ($this->helper_text) { ?>
				<p id="<?= $this->getId() . '_help'; ?>" class="form-text text-muted description"><?= $this->helper_text; ?></p>
			<?php } ?>

		<?php if ($this->hasTableLayout()) { ?>
				</td>
			</tr>
		<?php } else { ?>
			</div>
		<?php } ?>
		<?php return ob_get_clean();
	}
}
