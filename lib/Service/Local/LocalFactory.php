<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Service\Local;

use OCA\DAVC\Store\Local\ContactStore;
use OCA\DAVC\Store\Local\EventStore;
use OCP\Server;

class LocalFactory {
	/**
	 * instance of the local contact service
	 */
	public function contactsService(): LocalContactsService {
		$service = new LocalContactsService();
		$service->initialize($this->contactsStore());
		return $service;
	}

	/**
	 * instance of the local event service
	 */
	public function eventsService(): LocalEventsService {
		$service = new LocalEventsService();
		$service->initialize($this->eventsStore());
		return $service;
	}

	/**
	 * instance of the local contact store
	 *
	 * @return ContactStore
	 */
	public function contactsStore(): ContactStore {
		return Server::get(ContactStore::class);
	}

	/**
	 * instance of the local event store
	 *
	 * @return EventStore
	 */
	public function eventsStore(): EventStore {
		return Server::get(EventStore::class);
	}

}
