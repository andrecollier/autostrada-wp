<?php

namespace Sircon\Library\Mail;

class VCalendar {

	public static function send(string $recipient, string $subject, string $body, VCalendarEvent $event, array $headers = []) {
		$mime_boundary = "----Meeting Booking----" . md5(time());

		$headers['MIME-Version'] = "1.0";
		$headers['Content-Type'] = "multipart/alternative; boundary=\"$mime_boundary\"";
		$headers['Content-class'] = "urn:content-classes:calendarmessage";

		$message = "--$mime_boundary\r\n";

		$message .= "Content-Type: text/html; charset=utf-8\r\n";
		$message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
		$message .= $body;

		$message .= "\r\n--$mime_boundary--\r\n";

		$message .= "Content-Type: text/calendar;name=\"meeting.ics\";method=REQUEST;charset=utf-8\r\n";
		$message .= "Content-Type: text/calendar;name=\"meeting.ics\";method=REQUEST\r\n";
		$message .= "Content-Transfer-Encoding: 8bit\n\n";
		$message .= $event;

		mail($recipient, $subject, $message, $headers);
	}
}
