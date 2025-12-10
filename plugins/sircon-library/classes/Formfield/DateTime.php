<?php

namespace Sircon\Library\Formfield;

class DateTime extends Input {

	private $input_format = 'j. F Y H:i';

	private $locale = null;

	private $min_date = null;

	private $max_date = null;

	private $min_time = null;

	private $max_time = null;

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

	public function setMinTime(?string $min_time): Formfield {
		$this->min_time = $min_time;
		return $this;
	}

	public function setMaxTime(?string $max_time): Formfield {
		$this->max_time = $max_time;
		return $this;
	}

	public function getOutput(): string {
		$config = [
			'defaultDate'   => intval($this->getValue()) * 1000,
			'enableTime'    => true,
			'time_24hr'     => true,
			'altInput'      => true,
			'altFormat'     => $this->input_format,
			'dateFormat'    => 'U',
			'locale'        => $this->locale
		];

		if ($this->min_date !== null) {
			$config['minDate'] = $this->min_date * 1000;
			if ($config['defaultDate'] < $config['minDate']) {
				$config['defaultDate'] = $config['minDate'];
			}
		}

		if ($this->max_date !== null) {
			$config['maxDate'] = $this->max_date * 1000;
			if ($config['defaultDate'] > $config['maxDate']) {
				$config['defaultDate'] = $config['maxDate'];
			}
		}

		if ($this->min_time !== null) {
			$config['minTime'] = $this->min_time;
		}

		if ($this->max_time !== null) {
			$config['maxTime'] = $this->max_time;
		}

		$this->addInputGroupAttribute('data-config="' . htmlentities(json_encode($config)) . '"');
		return parent::getOutput();
	}
}
