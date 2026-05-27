<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Providers\DAV;

class Constants {
	public const DAV_PROPERTY_OWNER = '{DAV:}owner';
	public const DAV_PROPERTY_DISPLAYNAME = '{DAV:}displayname';
	public const DAV_PROPERTY_ADDRESSBOOK_ENABLED = '{http://owncloud.org/ns}enabled';
	public const DAV_PROPERTY_CALENDAR_ENABLED = '{http://owncloud.org/ns}calendar-enabled';
	public const DAV_PROPERTY_CALENDAR_COMPONENT_SET = '{urn:ietf:params:xml:ns:caldav}supported-calendar-component-set';
	public const DAV_PROPERTY_COLOR = '{http://apple.com/ns/ical/}calendar-color';
}
