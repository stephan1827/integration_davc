<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Settings;

use OCP\IL10N;
use OCP\IURLGenerator;
use OCP\Settings\IIconSection;

/**
 * @psalm-api
 */
class UserSection implements IIconSection {

	public function __construct(
		private readonly IURLGenerator $urlGenerator,
		private readonly IL10N $l,
	) {
	}

	#[\Override]
	public function getID(): string {
		return 'connected-accounts'; //or a generic id if feasible
	}

	#[\Override]
	public function getName(): string {
		return $this->l->t('Connected accounts');
	}

	#[\Override]
	public function getPriority(): int {
		return 70;
	}

	#[\Override]
	public function getIcon(): ?string {
		return $this->urlGenerator->imagePath('core', 'categories/integration.svg');
	}

}
