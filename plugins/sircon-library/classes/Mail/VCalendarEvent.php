<?php

namespace Sircon\Library\Mail;

class VCalendarEvent {

	private $title;
	private $description;
	private $start;
	private $end;
	private $organiser;
	private $location;

	/**
	 * @param string $title
	 * @param string $description
	 * @param integer $start UNIX timestamp
	 * @param integer $end UNIX timestamp
	 * @param string $organiser Email adress for the organiser
	 * @param string $location
	 */
	public function __construct(string $title, string $description, int $start, int $end, string $organiser = '', string $location = '') {
		$this->title = $title;
		$this->description = $description;
		$this->start = $start;
		$this->end = $end;
		$this->organiser = $organiser;
		$this->location = $location;
	}

	public function __toString() {
		$vcal =  "BEGIN:VCALENDAR\r\n";
		$vcal .= "PRODID:-//Microsoft Corporation//Outlook 11.0 MIMEDIR//EN\r\n";
		$vcal .= "VERSION:2.0\r\n";
		$vcal .= "METHOD:PUBLISH\r\n";
		$vcal .= "BEGIN:VEVENT\r\n";
		$vcal .= "ORGANIZER:MAILTO:{$this->organiser}\r\n";
		$vcal .= "DTSTART:" . gmdate("Ymd\THis\Z", $this->start) . "\r\n";
		$vcal .= "DTEND:" . gmdate("Ymd\THis\Z", $this->end) . "\r\n";
		$vcal .= "LOCATION:{$this->location}\r\n";
		$vcal .= "TRANSP:OPAQUE\r\n";
		$vcal .= "SEQUENCE:0\r\n";
		$vcal .= "UID:" . date("Ymd") . "T" . date("His") . "-" . rand() . substr($this->organiser, strpos($this->organiser, '@')) . "\r\n";
		$vcal .= "DTSTAMP:" . gmdate("Ymd\THis\Z") . "\r\n";
		$vcal .= "DESCRIPTION:{$this->description}\r\n";
		$vcal .= "SUMMARY:{$this->title}\r\n";
		$vcal .= "PRIORITY:5\r\n";
		$vcal .= "CLASS:PUBLIC\r\n";
		$vcal .= "END:VEVENT\r\n";
		$vcal .= "END:VCALENDAR\r\n";

		return $vcal;
	}
}
