<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Service;

use OCA\DAVC\Store\Local\ServicesTemplateStore;
use OCA\DAVC\Utile\UUID;

class ServicesTemplateService {
	private ServicesTemplateStore $_Store;

	public function __construct(ServicesTemplateStore $store) {

		$this->_Store = $store;

	}

	public function findByDomain(string $domain): array {

		return $this->_Store->fetchByDomain($domain);
	}

	/**
	 * list all service templates with decoded connection settings
	 *
	 * @since Release 1.0.0
	 *
	 * @return array
	 */
	public function list(): array {

		return array_map(static function (array $template): array {
			$template['connection'] = json_decode((string)$template['connection'], true) ?: [];
			return $template;
		}, $this->_Store->list());
	}

	/**
	 * create a new service template
	 *
	 * @since Release 1.0.0
	 *
	 * @param string $domain service domain
	 * @param array $connection connection settings
	 *
	 * @return bool
	 */
	public function create(string $domain, array $connection): bool {

		return $this->_Store->create(UUID::v4(), $domain, $connection);
	}

	/**
	 * modify an existing service template
	 *
	 * @since Release 1.0.0
	 *
	 * @param string $id service template id
	 * @param string $domain service domain
	 * @param array $connection connection settings
	 *
	 * @return bool
	 */
	public function modify(string $id, string $domain, array $connection): bool {

		return $this->_Store->modify($id, $domain, $connection);
	}

	/**
	 * delete a service template
	 *
	 * @since Release 1.0.0
	 *
	 * @param string $id service template id
	 *
	 * @return bool
	 */
	public function delete(string $id): bool {

		return $this->_Store->delete($id);
	}

}
