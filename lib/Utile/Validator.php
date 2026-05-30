<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Utile;

class Validator {

	private const _fqdn = '/(?=^.{1,254}$)(^(?:(?!\d|-)[a-z0-9\-]{1,63}(?<!-)\.)+(?:[a-z]{2,})$)/i';
	private const _ip4 = '/^(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\\.(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/';
	private const _ip6 = '/^(([0-9a-fA-F]{1,4}:){7,7}[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,7}:|([0-9a-fA-F]{1,4}:){1,6}:[0-9a-fA-F]{1,4}|([0-9a-fA-F]{1,4}:){1,5}(:[0-9a-fA-F]{1,4}){1,2}|([0-9a-fA-F]{1,4}:){1,4}(:[0-9a-fA-F]{1,4}){1,3}|([0-9a-fA-F]{1,4}:){1,3}(:[0-9a-fA-F]{1,4}){1,4}|([0-9a-fA-F]{1,4}:){1,2}(:[0-9a-fA-F]{1,4}){1,5}|[0-9a-fA-F]{1,4}:((:[0-9a-fA-F]{1,4}){1,6})|:((:[0-9a-fA-F]{1,4}){1,7}|:)|fe80:(:[0-9a-fA-F]{0,4}){0,4}%[0-9a-zA-Z]{1,}|::(ffff(:0{1,4}){0,1}:){0,1}((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])|([0-9a-fA-F]{1,4}:){1,4}:((25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9])\\.){3,3}(25[0-5]|(2[0-4]|1{0,1}[0-9]){0,1}[0-9]))$/';
	private const _username = '/^[\x20-\x39\x3B-\x7E]+$/';
	private const _password = '/^[\x21-\x7E]+$/';

	/**
	 * validate fully quntified domain name
	 *
	 * @param string $fqdn - FQDN to validate
	 *
	 * @return bool
	 */
	public static function fqdn(string $fqdn): bool {

		return (!empty($fqdn) && preg_match(self::_fqdn, $fqdn) > 0);
	}

	/**
	 * validate IPv4 address
	 *
	 * @param string $ip - IPv4 address to validate
	 *
	 * @return bool
	 */
	public static function ip4(string $ip): bool {

		return (!empty($ip) && preg_match(self::_ip4, $ip) > 0);
	}

	/**
	 * validate IPv6 address
	 *
	 * @param string $ip - IPv6 address to validate
	 *
	 * @return bool
	 */
	public static function ip6(string $ip): bool {

		return (!empty($ip) && preg_match(self::_ip6, $ip) > 0);
	}

	/**
	 * validate host
	 *
	 * @param string $host - FQDN/IPv4/IPv6 address to validate
	 *
	 * @return bool
	 */
	public static function host(string $host): bool {

		if ($host === 'localhost') {
			return true;
		}

		if (self::fqdn($host)) {
			return true;
		}

		if (self::ip4($host)) {
			return true;
		}

		if (self::ip6($host)) {
			return true;
		}

		return false;
	}

	/**
	 * validate email address
	 *
	 * @param string $address - email address to validate
	 *
	 * @return bool
	 */
	public static function email(string $address): bool {

		return (!empty($address) && filter_var($address, FILTER_VALIDATE_EMAIL));
	}

	/**
	 * validate username
	 *
	 * @param string $username - username to validate
	 *
	 * @return bool
	 */
	public static function username(string $username): bool {
		if (trim($username) === '') {
			return false;
		}

		if (trim($username) !== $username) {
			return false;
		}

		if (str_contains($username, '@')) {
			return self::email($username);
		}

		return preg_match(self::_username, $username) > 0;
	}

	/**
	 * validate password
	 *
	 * @param string $password - password to validate
	 *
	 * @return bool
	 */
	public static function password(string $password): bool {

		return (!empty($password) && preg_match(self::_password, $password) > 0);
	}
}
