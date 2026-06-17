<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Store\Common\Range;

use DateTimeInterface;

interface IRangeDate extends IRange {

	/**
	 * get start date of the range
	 */
	public function getStart(): DateTimeInterface;

	/**
	 * set start date of the range
	 */
	public function setStart(DateTimeInterface $value): void;

	/**
	 * get end date of the range
	 */
	public function getEnd(): DateTimeInterface;

	/**
	 * set end date of the range
	 */
	public function setEnd(DateTimeInterface $value): void;

}
