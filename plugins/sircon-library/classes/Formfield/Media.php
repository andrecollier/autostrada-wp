<?php

namespace Sircon\Library\Formfield;

class Media extends Formfield implements MultifieldSupport {

	private $name = '';

	private $label = '';

	private $value = '';

	private $helper_text;

	public function __construct(string $name, string $label, string $value = '') {
		parent::__construct($name);
		$this->name = $name;
		$this->label = $label;
		$this->value = $value;
	}

	public function setValue(string $value): self {
		$this->value = $value;
		return $this;
	}

	public function getName(): string {
		return $this->name;
	}

	public function setName(string $name): self {
		$this->name = $name;
		return $this;
	}

	public function setHelperText(string $helper_text): self {
		$this->helper_text = $helper_text;
		return $this;
	}

	public function isSaveable(): bool {
		return true;
	}

	public function getOutput(): string {
		$image_type = null;
		$image_src = null;
		$image_output = null;

		if ($this->value) {
			$image_type = get_post_mime_type($this->value);
			if ($image_type === 'image/svg+xml') {
				$image_output = file_get_contents(get_attached_file($this->value));
			} else {
				$image_src = wp_get_attachment_image_url($this->value);
				$image_output = wp_get_attachment_image($this->value);
			}
		}

		ob_start();
		if ($this->hasTableLayout()) { ?>
			<tr class="<?= $this->getClasses(); ?>">
				<th>
					<label><?= $this->label; ?></label>
				</th>
				<td>
		<?php } else { ?>
			<div class="form-group <?= $this->getClasses(); ?>">
				<label><?= $this->label; ?></label>
		<?php } ?>
			<div class="media-select">
				<div class="custom-img-preview"<?= $image_src ? ' style="background-image: url(' . $image_src . ');"' : ''?>>
					<?php if ($image_output) {
						echo $image_output;
					}?>
				</div>
				<div class="media-select-buttons">
					<button type="button" class="button button-primary" onclick="sirconLibraryMediaSelect(this);return false;"><?= __('Select/upload image', 'sircon-library')?></button>
					<button type="button" class="button" onclick="sirconLibraryMediaDeselect(this);return false;"><?= __('Remove', 'sircon-library')?></button>
				</div>
				<input type="hidden"
					id="<?= $this->getId(); ?>"
					value="<?= htmlentities($this->value); ?>"
					<?= $this->getNameAttributeName(); ?>="<?= $this->name; ?>"
					<?= ($this->helper_text ? 'aria-describedby="' . $this->getId() . '_help"' : '') ?>
					>
			</div>
			<?php if ($this->helper_text) { ?>
				<p id="<?= $this->getId() . '_help'; ?>" class="form-text text-muted"><?= $this->helper_text; ?></p>
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
