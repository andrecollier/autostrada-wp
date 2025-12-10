<?php

namespace Sircon\Library;

use Sircon\Library\Formfield\Formfield;

class Optionspage {

	public const MENU_TYPE_TAB = 'tab';

	public const MENU_TYPE_MENU = 'menu';

	public const MENU_TYPE_THEME = 'theme';

	public const MENU_TYPE_OPTIONS = 'options';

	public const MENU_TYPE_SUBMENU = 'submenu';

	public const MENU_TYPE_MANAGEMENT = 'management';

	protected $id = '';

	protected $page_title = '';

	protected $menu_title = '';

	protected $menu_type = '';

	protected $capability = '';

	protected $parent = '';

	protected $icon = '';

	protected $position = null;

	protected $fields = [];

	protected static $option_pages = [];

	public function __construct(string $id, string $page_title, string $menu_title, string $menu_type = self::MENU_TYPE_MENU, string $capability = 'manage_options') {
		$this->id = $id;
		$this->page_title = $page_title;
		$this->menu_title = $menu_title;
		$this->menu_type = $menu_type;
		$this->capability = $capability;

		self::add($this);
	}

	public function getId(): string {
		return $this->id;
	}

	public function getPageTitle(): string {
		return $this->page_title;
	}

	public function setPageTitle(string $page_title): self {
		$this->page_title = $page_title;

		return $this;
	}

	public function getMenuTitle(): string {
		return $this->menu_title;
	}

	public function setMenuTitle(string $menu_title): self {
		$this->menu_title = $menu_title;

		return $this;
	}

	public function getMenuType(): string {
		return $this->menu_type;
	}

	public function setMenuType(string $menu_type): self {
		$this->menu_type = $menu_type;

		return $this;
	}

	public function getCapability(): string {
		return $this->capability;
	}

	public function setCapability(string $capability): self {
		$this->capability = $capability;

		return $this;
	}

	public function getParent(): string {
		return $this->parent;
	}

	public function setParent(string $parent): self {
		$this->parent = $parent;

		return $this;
	}

	public function getIcon(): string {
		return $this->icon;
	}

	public function setIcon(string $icon): self {
		$this->icon = $icon;

		return $this;
	}

	public function getPosition(): ?int {
		return $this->position;
	}

	public function setPosition(int $position): self {
		$this->position = $position;

		return $this;
	}

	public function addOption(Formfield $Field): self {
		$this->fields[] = $Field;

		return $this;
	}

	public function getFields(): array {
		return $this->fields;
	}

	public function output() {
		?>
		<div class="wrap">
			<h1><?= $this->getPageTitle(); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields($this->getId()); ?>
				<table class="form-table" role="presentation">
					<tbody>
						<?php foreach ($this->getFields() as $Field) {
							$Field->setTableLayout(true);
							if ($Field->isSaveable()) {
								if (self::hasOption($this->getId(), $Field->getName())) {
									$Field->setValue(self::getOption($this->getId(), $Field->getName()));
								}

								$Field->setName($this->getId() . '_' . $Field->getName());
							}

							$Field->output();
						} ?>
					</tbody>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	public static function setup(): void {
		add_action('admin_menu', [__CLASS__ , 'registerOptionPages']);
		add_action('admin_init', [__CLASS__ , 'registerSettings']);
	}

	public static function add(Optionspage $Optionspage): void {
		self::$option_pages[$Optionspage->getId()] = $Optionspage;
	}

	protected static function hasPage(string $id): bool {
		return !empty(self::$option_pages[$id]);
	}

	public static function getOption(string $options_page_id, string $field): string {
		return get_option($options_page_id . '_' . $field, '');
	}

	public static function hasOption(string $options_page_id, string $field): bool {
		return get_option($options_page_id . '_' . $field) !== false;
	}

	public static function registerOptionPages(): void {
		foreach (self::$option_pages as $Optionspage) {
			switch ($Optionspage->getMenuType()) {
				case self::MENU_TYPE_MANAGEMENT:
					add_management_page($Optionspage->getPageTitle(), $Optionspage->getMenuTitle(), $Optionspage->getCapability(), $Optionspage->getId(), [__CLASS__, 'optionsPageOutput'], $Optionspage->getPosition());
					break;

				case self::MENU_TYPE_THEME:
					add_theme_page($Optionspage->getPageTitle(), $Optionspage->getMenuTitle(), $Optionspage->getCapability(), $Optionspage->getId(), [__CLASS__, 'optionsPageOutput'], $Optionspage->getPosition());
					break;

				case self::MENU_TYPE_OPTIONS:
					add_options_page($Optionspage->getPageTitle(), $Optionspage->getMenuTitle(), $Optionspage->getCapability(), $Optionspage->getId(), [__CLASS__, 'optionsPageOutput'], $Optionspage->getPosition());
					break;

				case self::MENU_TYPE_SUBMENU:
					add_submenu_page($Optionspage->getParent(), $Optionspage->getPageTitle(), $Optionspage->getMenuTitle(), $Optionspage->getCapability(), $Optionspage->getId(), [__CLASS__, 'optionsPageOutput'], $Optionspage->getPosition());
					break;

				case self::MENU_TYPE_TAB:
					break;

				default:
					add_menu_page($Optionspage->getPageTitle(), $Optionspage->getMenuTitle(), $Optionspage->getCapability(), $Optionspage->getId(), [__CLASS__, 'optionsPageOutput'], $Optionspage->getIcon(), $Optionspage->getPosition());
					break;
			}
		}
	}

	public static function registerSettings(): void {
		foreach (self::$option_pages as $options_page_id => $Optionspage) {
			foreach ($Optionspage->getFields() as $Field) {
				if ($Field->isSaveable()) {
					register_setting($options_page_id, $options_page_id . '_' . $Field->getName());
				}
			}
		}
	}

	public static function optionsPageOutput(): void {
		$options_page_id = $_GET['page'];
		if (!self::hasPage($options_page_id)) {
			return;
		}

		self::$option_pages[$options_page_id]->output();
	}
}

if (is_admin()) {
	Optionspage::setup();
}
