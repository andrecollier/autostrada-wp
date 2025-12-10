<?php

namespace Sircon\Library\Formfield;

class Form {

	public static function text(string $name, string $label, string $value = '', string $placeholder = ''): Text {
		return new Text($name, $label, $value, $placeholder);
	}

	public static function email(string $name, string $label, string $value = '', string $placeholder = ''): Email {
		return new Email($name, $label, $value, $placeholder);
	}

	public static function password(string $name, string $label, string $value = '', string $placeholder = ''): Password {
		return new Password($name, $label, $value, $placeholder);
	}

	public static function number(string $name, string $label, string $value = '', string $placeholder = ''): Number {
		return new Number($name, $label, $value, $placeholder);
	}

	public static function checkbox(string $name, string $label, string $description = '', string $value = '0'): Checkbox {
		return new Checkbox($name, $label, $description, $value);
	}

	public static function textarea(string $name, string $label, string $value = '', string $placeholder = ''): Textarea {
		return new Textarea($name, $label, $value, $placeholder);
	}

	public static function select(string $name, string $label, array $options, string $selected = '', string $default = ''): Select {
		return new Select($name, $label, $options, $selected, $default);
	}

	public static function gselect(string $name, string $label, array $options, string $selected = '', string $default = ''): GroupedSelect {
		return new GroupedSelect($name, $label, $options, $selected, $default);
	}

	public static function radio(string $name, string $label, array $options, string $selected = '', string $default = ''): Radio {
		return new Radio($name, $label, $options, $selected, $default);
	}

	public static function hidden(string $name, string $value): Hidden {
		return new Hidden($name, $value);
	}

	public static function date(string $name, string $label, int $value = 0): Date {
		return new Date($name, $label, $value);
	}

	public static function time(string $name, string $label, string $value = ''): Time {
		return new Time($name, $label, $value);
	}

	public static function datetime(string $name, string $label, int $value = 0): DateTime {
		return new DateTime($name, $label, $value);
	}

	public static function color(string $name, string $label, string $value = ''): Color {
		return new Color($name, $label, $value);
	}

	public static function multifield(string $name, string $label = '', string $value = '[]'): Multifield {
		return new Multifield($name, $label, $value);
	}

	public static function media(string $name, string $label, string $value = ''): Media {
		return new Media($name, $label, $value);
	}

	public static function file(string $name, string $label, string $value = ''): File {
		return new File($name, $label, $value);
	}

	public static function editor(string $name, string $label, string $value = ''): Editor {
		return new Editor($name, $label, $value);
	}

	public static function group(): Group {
		return new Group();
	}

	public static function custom(string $content = ''): Custom {
		return new Custom($content);
	}
}
