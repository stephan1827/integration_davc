<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Tests\Database;

use OCA\DAVC\Store\Local\ServiceEntity;
use OCA\DAVC\Store\Local\ServicesStore;
use OCP\IDBConnection;
use OCP\Server;
use PHPUnit\Framework\Attributes\Group;
use Test\TestCase;

#[Group('DB')]
class ServicesStoreTest extends TestCase {

	private IDBConnection $db;
	private ServicesStore $store;
	private string $userId = 'test-user';

	protected function setUp(): void {
		parent::setUp();

		$this->db = Server::get(IDBConnection::class);
		$this->store = new ServicesStore($this->db);

		$this->purgeTestData();
	}

	protected function tearDown(): void {
		parent::tearDown();

		$this->purgeTestData();
	}

	private function purgeTestData(): void {
		$qb = $this->db->getQueryBuilder();
		$qb->delete('davc_services')
			->where(
				$qb->expr()->eq('uid', $qb->createNamedParameter($this->userId))
			)->executeStatement();
	}

	public function testCreate(): void {
		$service = $this->createServiceEntity($this->userId, 'alpha.example.test');
		$created = $this->store->create($service);

		$this->assertInstanceOf(ServiceEntity::class, $created);
		$this->assertGreaterThan(0, $created->getId());
		$this->assertSame($this->userId, $created->getUid());
	}

	public function testModify(): void {
		$service = $this->createServiceEntity($this->userId, 'alpha.example.test');
		$service = $this->store->create($service);

		$service->setLabel('service-modified');
		$mutated = $this->store->modify($this->userId, $service);

		$this->assertInstanceOf(ServiceEntity::class, $mutated);
		$this->assertGreaterThan(0, $mutated->getId());
		$this->assertEquals(0, count($mutated->getUpdatedFields()));
		$this->assertSame($this->userId, $mutated->getUid());
		$this->assertSame('service-modified', $mutated->getLabel());
	}

	public function testDelete(): void {
		$service = $this->createServiceEntity($this->userId, 'alpha.example.test');
		$service = $this->store->create($service);

		$mutated = $this->store->delete($this->userId, $service);
		$this->assertInstanceOf(ServiceEntity::class, $mutated);
		$this->assertGreaterThan(0, $mutated->getId());
		$this->assertSame($this->userId, $mutated->getUid());
	}

	public function testFetch(): void {
		$service = $this->createServiceEntity($this->userId, 'alpha.example.test');
		$service = $this->store->create($service);

		$fetched = $this->store->fetch((int)$service->getId());
		$this->assertInstanceOf(ServiceEntity::class, $fetched);
		$this->assertGreaterThan(0, $fetched->getId());
		$this->assertSame($this->userId, $fetched->getUid());
		$this->assertSame('alpha.example.test', $fetched->getLocationHost());
		$this->assertSame('service-' . $this->userId, $fetched->getLabel());
	}

	public function testList(): void {
		$this->purgeTestData();

		$service1 = $this->createServiceEntity($this->userId, 'alpha.example.test');
		$service2 = $this->createServiceEntity($this->userId, 'beta.example.test');
		$this->store->create($service1);
		$this->store->create($service2);

		// test list without filter
		$services = $this->store->list();
		$this->assertIsArray($services);
		$this->assertGreaterThanOrEqual(2, count($services));

		// test list with filter
		$filter = $this->store->listFilter();
		$filter->condition('uid', $this->userId);

		$services = $this->store->list($filter);
		$this->assertIsArray($services);
		$this->assertCount(2, $services);
	}

	public function testDeleteByUser(): void {
		$this->purgeTestData();

		$service1 = $this->createServiceEntity($this->userId, 'alpha.example.test');
		$service2 = $this->createServiceEntity($this->userId, 'beta.example.test');
		$this->store->create($service1);
		$this->store->create($service2);

		$this->store->deleteByUser($this->userId);

		$filter = $this->store->listFilter();
		$filter->condition('uid', $this->userId);
		$services = $this->store->list($filter);
		$this->assertIsArray($services);
		$this->assertCount(0, $services);
	}

	private function createServiceEntity(string $uid, string $host): ServiceEntity {
		$service = new ServiceEntity();
		$service->setUid($uid);
		$service->setUuid(sprintf('12345678-1234-1234-1234-%012d', random_int(1, 999999)));
		$service->setLabel('service-' . $uid);
		$service->setLocationProtocol('https');
		$service->setLocationHost($host);
		$service->setLocationPort(443);
		$service->setLocationPath('/remote.php/dav');
		$service->setLocationSecurity(true);
		$service->setAuth('BA');
		$service->setBauthId('user@example.test');
		$service->setBauthSecret('secret');

		return $service;
	}
}
