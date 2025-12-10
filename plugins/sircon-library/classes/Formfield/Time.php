<?php

namespace Sircon\Library\Formfield;

class Time extends Input {

	private $locale = null;

	private $min_time = null;

	private $max_time = null;

	public function __construct(string $name, string $label, string $value = '') {
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

	public function setMinTime(?string $min_time): Formfield {
		$this->min_time = $min_time;
		return $this;
	}

	public function setMaxTime(?string $max_time): Formfield {
		$this->max_time = $max_time;
		return $this;
	}

	public function getOutput(): string {
		$values = explode(':', $this->getValue());
		$config = [
			'enableTime'    => true,
			'noCalendar'    => true,
			'time_24hr'     => true,
			'defaultHour'   => intval($values[0] ?? 12),
			'defaultMinute' => intval($values[1] ?? 0),
			'timeFormat'    => 'H:i',
			'locale'        => $this->locale
		];

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
