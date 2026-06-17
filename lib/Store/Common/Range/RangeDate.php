<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Store\Common\Range;

use DateTime;
use DateTimeInterface;

class RangeDate implements IRangeDate {

	public function __construct(
		protected ?DateTimeInterface $start = null,
		protected ?DateTimeInterface $end = null,
	) {

		if ($start === null) {
			$start = new DateTime();
		}
		if ($end === null) {
			$end = new DateTime();
		}
		$this->start = $start;
		$this->end = $end;

	}

	#[\Override]
	public function type(): string {
		return 'date';
	}

	#[\Override]
	public function getStart(): DateTimeInterface {
		return $this->start;
	}

	#[\Override]
	public function setStart(DateTimeInterface $value): void {
		$this->start = $value;
	}

	#[\Override]
	public function getEnd(): DateTimeInterface {
		return $this->end;
	}

	#[\Override]
	public function setEnd(DateTimeInterface $value): void {
		$this->end = $value;
	}

}
