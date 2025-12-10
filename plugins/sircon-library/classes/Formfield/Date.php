<?php

namespace Sircon\Library\Formfield;

class Date extends Input {

	private $input_format = 'j. F Y';

	private $locale = null;

	private $min_date = null;

	private $max_date = null;

	public function __construct(string $name, string $label, int $value = 0) {
		if (!$value) {
			$value = time();
		}

		parent::__construct('text', $name, $label, (string) $value);

		$wplocale = get_locale();
		switch ($wplocale) {
			case 'nb_NO':
			case 'nn_NO':
				$this->locale = explode('_', $wplocale)[1];
				break;

			default:
				$this->locale = explode('_', $wplocale)[0];
		}

		$this->addInputGroupClass('sircon-datepicker');
		$this->addInputClass('flatpickr');
	}

	/**
	 * Set the two-letter lowercase locale to be used for this input
	 */
	public function setLocale(string $locale): Formfield {
		$this->locale = $locale;
		return $this;
	}

	public function setInputFormat(string $format): Formfield {
		$this->input_format = $format;
		return $this;
	}

	public function setMinDate(?int $min_date): Formfield {
		$this->min_date = $min_date;
		return $this;
	}

	public function setMaxDate(?int $max_date): Formfield {
		$this->max_date = $max_date;
		return $this;
	}

	public function getOutput(): string {
		$config = [
			'defaultDate'   => $this->getValue(),
			'altInput'      => true,
			'altFormat'     => $this->input_format,
			'dateFormat'    => 'U',
			'locale'        => $this->locale
		];

		if ($this->min_date !== null) {
			$config['minDate'] = $this->min_date * 1000;
			if ($config['defaultDate'] < $this->min_date) {
				$config['defaultDate'] = $this->min_date;
			}
		}

		if ($this->max_date !== null) {
			$config['maxDate'] = $this->max_date * 1000;
			if ($config['defaultDate'] > $this->max_date) {
				$config['defaultDate'] = $this->max_date;
			}
		}

		$this->addInputGroupAttribute('data-config="' . htmlentities(json_encode($config)) . '"');
		return parent::getOutput();
	}
}
