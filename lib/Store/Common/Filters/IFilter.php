<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Store\Common\Filters;

interface IFilter {

	/**
	 * List of attributes that are available for filtering
	 *
	 * @return array<string>
	 */
	public function attributes(): array;

	/**
	 * List of comparators that are available for filtering
	 *
	 * @return list<FilterComparisonOperator>
	 */
	public function comparators(): array;

	/**
	 * List of conjunctions that are available for filtering
	 *
	 * @return list<FilterConjunctionOperator>
	 */
	public function conjunctions(): array;

	/**
	 * Adds a condition to the filter
	 */
	public function condition(string $attribute, mixed $value, FilterComparisonOperator $comparator = FilterComparisonOperator::EQ, FilterConjunctionOperator $conjunction = FilterConjunctionOperator::AND): void;

	/**
	 * List of conditions that have been added to the filter
	 *
	 * @return array<int, array{attribute:string, value:mixed, comparator:FilterComparisonOperator, conjunction:FilterConjunctionOperator}>
	 */
	public function conditions(): array;

}
