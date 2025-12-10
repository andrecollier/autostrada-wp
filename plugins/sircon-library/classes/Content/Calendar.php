<?php

namespace Sircon\Library\Content;

use Sircon\Library\Library;

class Calendar {

	private $timestamp;

	private $currentDay = 0;

	private $showWeekNumbers = false;

	private $id = '';

	private $events = [];

	public function __construct(string $id, ?int $timestamp = null) {
		$this->id = $id;
		$this->timestamp = strtotime('first day of this month midnight', $timestamp ?? time());
		$this->events = apply_filters("sircon_library_calendar_$id", [], $this->timestamp);
	}

	public function showWeekNumbers(bool $showWeekNumbers = true): self {
		$this->showWeekNumbers = $showWeekNumbers;

		return $this;
	}

	public function getConfig(): array {
		return [
			'display' => [
				'weekNumbers' => $this->showWeekNumbers,
			]
		];
	}

	public function __toString(): string {
		ob_start();
		$next = strtotime('first day of next month', $this->timestamp);
		$prev = strtotime('first day of last month', $this->timestamp);
		?>
		<div class="sircon-calendar" data-calendar-id="<?= $this->id; ?>" data-timestamp="<?= $this->timestamp; ?>" data-config="<?= htmlentities(json_encode($this->getConfig())); ?>">
			<div class="header">
				<div class="inner">
					<a class="nav nav-prev" data-timestamp="<?= $prev ?>"><?= file_get_contents(Library::ASSETS_PATH . "/icons/navigate_before.svg"); ?></a>
					<span class="title"><?= ucfirst(wp_date('F Y', $this->timestamp)); ?></span>
					<a class="nav nav-next" data-timestamp="<?= $next ?>"><?= file_get_contents(Library::ASSETS_PATH . "/icons/navigate_next.svg"); ?></a>
				</div>
			</div>
			<table>
				<thead>
					<tr>
						<?php if ($this->showWeekNumbers) { ?>
							<td class="label week"><?= __('Week', 'sircon-library'); ?></td>
						<?php } ?>
						<td class="label"><?= __('Mo', 'sircon-library'); ?></td>
						<td class="label"><?= __('Tu', 'sircon-library'); ?></td>
						<td class="label"><?= __('We', 'sircon-library'); ?></td>
						<td class="label"><?= __('Th', 'sircon-library'); ?></td>
						<td class="label"><?= __('Fr', 'sircon-library'); ?></td>
						<td class="label"><?= __('Sa', 'sircon-library'); ?></td>
						<td class="label"><?= __('Su', 'sircon-library'); ?></td>
					</tr>
				</thead>
				<tbody class="dates">
					<?php
					$daysInMonth = date('t', $this->timestamp);
					$numOfweeks = ($daysInMonth % 7 == 0 ? 0 : 1) + intval($daysInMonth / 7);

					$monthEndingDay = date('N', strtotime(date('Y-m', $this->timestamp) . '-' . $daysInMonth));
					$monthStartDay = date('N', strtotime(date('Y-m-01', $this->timestamp)));

					if ($monthEndingDay < $monthStartDay) {
						$numOfweeks++;
					}

					for ($i = 0; $i < $numOfweeks; $i++) {
						?>
						<tr>
							<?php
							if ($this->showWeekNumbers) { ?>
								<td class="label week"><?= date('W', strtotime(date('Y-m-01', $this->timestamp) . " + $i weeks")); ?></td>
							<?php }
							for ($j = 1; $j <= 7; $j++) {
								$this->printDay($i * 7 + $j);
							}
							?>
							</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</div>
		<?php
		return ob_get_clean();
	}

	private function printDay(int $cellNumber) {
		$daysInMonth = date('t', $this->timestamp);
		if ($this->currentDay == 0) {
			$firstDayOfTheWeek = date('N', strtotime(date('Y-m', $this->timestamp) . '-01'));

			if (intval($cellNumber) == intval($firstDayOfTheWeek)) {
				$this->currentDay = 1;
				$cellContent = $this->currentDay;
			}
		} else {
			$this->currentDay++;
			$cellContent = $this->currentDay;
		}

		$currentDate = null;

		$classes = 'day';
		$attributes = '';

		if (($this->currentDay != 0) && ($this->currentDay <= $daysInMonth)) {
			$currentDate = date('Y-m-d', strtotime(date('Y-m', $this->timestamp) . '-' . ($this->currentDay)));
			$attributes .= 'data-date="' . $currentDate . '" ';
		} else {
			$cellContent = '';
		}

		if (array_key_exists($currentDate, $this->events)) {
			$classes .= ' ' . implode(' ', $this->events[$currentDate] ?? []);
		}

		?>
		<td <?= $attributes; ?> class="<?= $classes; ?>"><?= $cellContent; ?></td>
		<?php
	}

	public static function ajax(): void {
		$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
		$timestamp = filter_input(INPUT_GET, 'timestamp', FILTER_SANITIZE_NUMBER_INT);

		$config = json_decode(filter_input(INPUT_GET, 'config'), true) ?? [];

		$Calendar = new Calendar($id, $timestamp);
		$Calendar->showWeekNumbers($config['display']['weekNumbers'] ?? false);

		echo $Calendar;
		wp_die();
	}

	public static function addAjaxActions(): void {
		add_action('wp_ajax_sircon_library_calendar', [__CLASS__, 'ajax']);
		add_action('wp_ajax_nopriv_sircon_library_calendar', [__CLASS__, 'ajax']);
	}
}
