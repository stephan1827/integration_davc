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

	#[\Override]
	public function attributes(): array {
		return $this->attributes;
	}

	#[\Override]
	public function comparators(): array {
		return FilterComparisonOperator::cases();
	}

	#[\Override]
	public function conjunctions(): array {
		return FilterConjunctionOperator::cases();
	}

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

	#[\Override]
	public function conditions(): array {
		return $this->conditions;
	}

}
