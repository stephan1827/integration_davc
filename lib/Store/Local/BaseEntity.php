<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Store\Local;

use OCP\AppFramework\Db\Entity;

/**
 * Base entity for chronicled data store entities.
 *
 * Declares the accessors shared by all entities handled by {@see BaseStore}
 * so that they resolve as defined magic methods when an entity is referenced
 * through this base type.
 *
 * @method getUid(): string
 * @method setUid(string $uid): void
 * @method getSid(): int
 * @method setSid(int $sid): void
 * @method getCid(): int
 * @method setCid(int $cid): void
 * @method getUuid(): string
 * @method setUuid(string $uuid): void
 */
abstract class BaseEntity extends Entity {
}
