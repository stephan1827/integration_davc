<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Tests\Database;

use OCA\DAVC\Store\Local\ServicesTemplateStore;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;
use OCP\Server;
use PHPUnit\Framework\Attributes\Group;
use Test\TestCase;

#[Group('DB')]
class ServicesTemplateStoreTest extends TestCase {

	private IDBConnection $db;
	private ServicesTemplateStore $store;
	/** @var string[] */
	private array $ids = ['davc-test-tpl-1', 'davc-test-tpl-2'];

	protected function setUp(): void {
		parent::setUp();

		$this->db = Server::get(IDBConnection::class);
		$this->store = new ServicesTemplateStore($this->db);

		$this->purgeTestData();
	}

	protected function tearDown(): void {
		parent::tearDown();

		$this->purgeTestData();
	}

	private function purgeTestData(): void {
		$qb = $this->db->getQueryBuilder();
		$qb->delete('davc_service_templates')
			->where($qb->expr()->in('id', $qb->createNamedParameter($this->ids, IQueryBuilder::PARAM_STR_ARRAY)))
			->executeStatement();
	}

	public function testCreateAndListAndFetchByDomain(): void {
		$this->store->create('davc-test-tpl-1', 'one.example.test', ['location_host' => 'dav.one.example.test', 'location_protocol' => 'https']);
		$this->store->create('davc-test-tpl-2', 'two.example.test', ['location_host' => 'dav.two.example.test']);

		$all = $this->store->list();
		$this->assertIsArray($all);
		$ours = array_values(array_filter($all, fn (array $row): bool => in_array($row['id'], $this->ids, true)));
		$this->assertCount(2, $ours);

		$byDomain = $this->store->fetchByDomain('one.example.test');
		$this->assertCount(1, $byDomain);
		$connection = json_decode($byDomain[0]['connection'], true);
		$this->assertSame('dav.one.example.test', $connection['location_host']);
	}

	public function testModifyAndDelete(): void {
		$this->store->create('davc-test-tpl-1', 'one.example.test', ['location_host' => 'dav.one.example.test']);

		$this->store->modify('davc-test-tpl-1', 'one.example.test', ['location_host' => 'changed.example.test']);
		$rows = $this->store->fetchById('davc-test-tpl-1');
		$this->assertCount(1, $rows);
		$connection = json_decode($rows[0]['connection'], true);
		$this->assertSame('changed.example.test', $connection['location_host']);

		$this->store->delete('davc-test-tpl-1');
		$this->assertCount(0, $this->store->fetchById('davc-test-tpl-1'));
	}
}
