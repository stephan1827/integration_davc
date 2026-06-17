<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Store\Common\Sort;

class SortBase implements ISort {

	protected array $attributes = [];
	protected array $conditions = [];

	public function attributes(): array {
		return $this->attributes;
	}

	public function condition(string $attribute, bool $direction): void {
		if (isset($this->attributes[$attribute])) {
			$this->conditions[$attribute] = [
				'attribute' => $attribute,
				'direction' => $direction,
			];
		}
	}

	public function conditions(): array {
		return $this->conditions;
	}

}
