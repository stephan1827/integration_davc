<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Providers\Calendar;

use OCA\DAVC\Store\Local\EventStore;
use OCP\Calendar\ICalendar;
use OCP\Calendar\ICalendarProvider;

class CalendarProvider implements ICalendarProvider {

	public function __construct(
		private readonly EventStore $store,
	) {
	}

	/**
	 * @return ICalendar[]
	 */
	#[\Override]
	public function getCalendars(string $principalUri, array $calendarUris = []): array {
		// principalUri is like "principals/users/admin" — extract the user ID
		$parts = explode('/', $principalUri);
		$uid = end($parts);
		if ($uid === false || $uid === '') {
			return [];
		}

		$collections = $this->store->collectionListByUser($uid);

		$calendars = [];
		foreach ($collections as $collection) {
			if (!empty($calendarUris) && !in_array($collection->getCcid(), $calendarUris, true)) {
				continue;
			}
			$calendars[] = new CalendarImpl($collection, $this->store);
		}

		return $calendars;
	}
}
