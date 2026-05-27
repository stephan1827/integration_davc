<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Models\Contacts;

class Collection {

	public string $userId = '';
	public int|null $serviceId = null;
	public int|null $localId = null;
	public string|null $localSignature = null;
	public string|null $remoteId = null;
	public string|null $remoteSignature = null;
	public string|null $uuid = null;
	public string|null $label = null;
	public string|null $description = null;
	public int|null $priority = null;
	public bool|null $visible = null;
	public string|null $color = null;

}
