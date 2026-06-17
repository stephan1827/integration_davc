<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Store\Common\Sort;

interface ISort {

	/**
	 * List of attributes that can be used for sorting
	 *
	 * @return array<string,bool>
	 */
	public function attributes(): array;

	/**
	 * add a sorting condition
	 *
	 * @param string $attribute attribute name
	 * @param bool $direction true for ascending, false for descending
	 */
	public function condition(string $attribute, bool $direction): void;

	/**
	 * retrieve sorting conditions
	 *
	 * @return array<string,array{attribute:string,direction:bool}>
	 */
	public function conditions(): array;

}
