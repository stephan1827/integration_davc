<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Store\Common\Filters;

class FilterBase implements IFilter {

	protected array $attributes = [];
	protected array $conditions = [];

	/**
	 *
	 * @since 1.0.0
	 *
	 * @return array<string>
	 */
	#[\Override]
	public function attributes(): array {
		return $this->attributes;
	}

	/**
	 *
	 * @since 1.0.0
	 */
	#[\Override]
	public function comparators(): FilterComparisonOperator {
		return new FilterComparisonOperator;
	}

	/**
	 *
	 * @since 1.0.0
	 */
	#[\Override]
	public function conjunctions(): FilterConjunctionOperator {
		return new FilterConjunctionOperator;
	}

	/**
	 *
	 * @since 1.0.0
	 *
	 */
	#[\Override]
	public function condition(string $attribute, mixed $value, FilterComparisonOperator $comparator = FilterComparisonOperator::EQ, FilterConjunctionOperator $conjunction = FilterConjunctionOperator::AND): void {
		if (!isset($this->properties[$attribute])) {
			$this->conditions[] = [
				'attribute' => $attribute,
				'value' => $value,
				'comparator' => $comparator,
				'conjunction' => $conjunction,
			];
		}
	}

	/**
	 *
	 * @since 1.0.0
	 *
	 * @return array<int, array{attribute:string, value:mixed, comparator:FilterComparisonOperator, conjunction:FilterConjunctionOperator}>
	 */
	#[\Override]
	public function conditions(): array {
		return $this->conditions;
	}

}
