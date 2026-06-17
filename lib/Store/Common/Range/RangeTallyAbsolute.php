<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Store\Common\Range;

class RangeTallyAbsolute implements IRangeTally {

	public function __construct(
		protected string|int $position = 0,
		protected string|int $count = 32,
	) {
	}

	#[\Override]
	public function type(): string {
		return 'tally';
	}

	#[\Override]
	public function anchor(): RangeAnchorType {
		return RangeAnchorType::ABSOLUTE;
	}

	#[\Override]
	public function getPosition(): string|int {
		return $this->position;
	}

	#[\Override]
	public function setPosition(string|int $value): void {
		$this->position = $value;
	}

	#[\Override]
	public function getCount(): int {
		return $this->count;
	}

	#[\Override]
	public function setCount(int $value): void {
		$this->count = $value;
	}

}
