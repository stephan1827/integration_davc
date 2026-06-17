<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Store\Common\Range;

interface IRangeTally extends IRange {

	/**
	 * range anchor type (relative or absolute)
	 */
	public function anchor(): RangeAnchorType;

	/**
	 * position within the range
	 */
	public function getPosition(): string|int;

	/**
	 * set the position within the range
	 */
	public function setPosition(string|int $value): void;

	/**
	 * get count within the range
	 */
	public function getCount(): int;

	/**
	 * set the count within the range
	 */
	public function setCount(int $value): void;

}
