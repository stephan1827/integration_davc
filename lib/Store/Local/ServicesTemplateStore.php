<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Store\Local;

use OCP\IDBConnection;

class ServicesTemplateStore {

	protected IDBConnection $_Store;
	protected string $_EntityTable = 'davc_service_templates';

	public function __construct(IDBConnection $store) {
		$this->_Store = $store;
	}

	/**
	 * normalise blob columns to strings
	 *
	 * On PostgreSQL (and Oracle) text/blob columns can be returned as stream
	 * resources rather than strings, so decode them before returning rows.
	 *
	 * @param array $rows
	 *
	 * @return array
	 */
	private function decodeRows(array $rows): array {
		foreach ($rows as &$row) {
			if (isset($row['connection']) && is_resource($row['connection'])) {
				$row['connection'] = stream_get_contents($row['connection']);
			}
		}
		return $rows;
	}

	/**
	 * retrieve service templates
	 *
	 * @since Release 1.0.0
	 */
	public function fetchById(string $id): array {
		// construct data store command
		$cmd = $this->_Store->getQueryBuilder();
		$cmd->select('*')
			->from($this->_EntityTable)
			->where($cmd->expr()->eq('id', $cmd->createNamedParameter($id)));
		// execute command
		$rs = $cmd->executeQuery()->fetchAll();
		$cmd->executeQuery()->closeCursor();
		// return result or null
		if (is_array($rs) && count($rs) > 0) {
			return $this->decodeRows($rs);
		} else {
			return [];
		}
	}

	/**
	 * retrieve all service templates from data store
	 *
	 * @since Release 1.0.0
	 *
	 * @return array
	 */
	public function list(): array {
		// construct data store command
		$cmd = $this->_Store->getQueryBuilder();
		$cmd->select('*')
			->from($this->_EntityTable);
		// execute command
		$result = $cmd->executeQuery();
		$rs = $result->fetchAll();
		$result->closeCursor();
		// return result
		return is_array($rs) ? $this->decodeRows($rs) : [];
	}

	/**
	 * retrieve service templates for specific domain from data store
	 *
	 * @since Release 1.0.0
	 *
	 * @param string $domain configured service domain
	 *
	 * @return array
	 */
	public function fetchByDomain(string $domain): array {
		// construct data store command
		$cmd = $this->_Store->getQueryBuilder();
		$cmd->select('*')
			->from($this->_EntityTable)
			->where($cmd->expr()->eq('domain', $cmd->createNamedParameter($domain)));
		// execute command
		$rs = $cmd->executeQuery()->fetchAll();
		$cmd->executeQuery()->closeCursor();
		// return result or null
		if (is_array($rs) && count($rs) > 0) {
			return $this->decodeRows($rs);
		} else {
			return [];
		}
	}

	/**
	 * create service templates for a specific domain in the data store
	 *
	 * @since Release 1.0.0
	 *
	 * @param string $id configured service template ID
	 * @param string $domain configured service domain
	 * @param array $data service template data
	 *
	 * @return bool
	 */
	public function create(string $id, string $domain, array $data): bool {
		// construct data store command
		$cmd = $this->_Store->getQueryBuilder();
		$cmd->insert($this->_EntityTable)
			->values([
				'id' => $cmd->createNamedParameter($id),
				'domain' => $cmd->createNamedParameter($domain),
				'connection' => $cmd->createNamedParameter(json_encode($data)),
			]);
		// execute command
		try {
			return $cmd->executeStatement() > 0;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * modify service templates for a specific domain in the data store
	 *
	 * @since Release 1.0.0
	 *
	 * @param string $id configured service template ID
	 * @param string $domain configured service domain
	 * @param array $data service template data
	 *
	 * @return bool
	 */
	public function modify(string $id, string $domain, array $data): bool {
		// construct data store command
		$cmd = $this->_Store->getQueryBuilder();
		$cmd->update($this->_EntityTable)
			->set('domain', $cmd->createNamedParameter($domain))
			->set('connection', $cmd->createNamedParameter(json_encode($data)))
			->where($cmd->expr()->eq('id', $cmd->createNamedParameter($id)));
		// execute command
		try {
			return $cmd->executeStatement() > 0;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * delete service template for a specific ID from the data store
	 *
	 * @since Release 1.0.0
	 *
	 * @param string $id configured service template ID
	 *
	 * @return bool
	 */
	public function delete(string $id): bool {
		// construct data store command
		$cmd = $this->_Store->getQueryBuilder();
		$cmd->delete($this->_EntityTable)
			->where($cmd->expr()->eq('id', $cmd->createNamedParameter($id)));
		// execute command
		try {
			return $cmd->executeStatement() > 0;
		} catch (\Exception $e) {
			return false;
		}
	}

}
