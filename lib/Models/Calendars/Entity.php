<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Models\Calendars;

class Entity {

	public int|null $localCollectionId = null;
	public int|null $localEntityId = null;
	public string|null $localSignature = null;
	public string|null $remoteCollectionId = null;
	public string|null $remoteEntityId = null;
	public string|null $remoteSignature = null;
	public string|null $correlationSignature = null;
	public string|null $uuid = null;
	public string|null $data = null;

}
