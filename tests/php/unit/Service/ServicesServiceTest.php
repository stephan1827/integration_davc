<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Tests\Unit\Service;

use OCA\DAVC\Service\ServicesService;
use OCA\DAVC\Store\Common\Filters\FilterBase;
use OCA\DAVC\Store\Common\Filters\FilterComparisonOperator;
use OCA\DAVC\Store\Common\Filters\FilterConjunctionOperator;
use OCA\DAVC\Store\Common\Filters\IFilter;
use OCA\DAVC\Store\Common\Sort\ISort;
use OCA\DAVC\Store\Local\ServiceEntity;
use OCA\DAVC\Store\Local\ServicesStore;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ServicesServiceTest extends TestCase {
	private ServicesStore&MockObject $store;

	private ServicesService $service;

	protected function setUp(): void {
		parent::setUp();

		$this->store = $this->createMock(ServicesStore::class);
		$this->service = new ServicesService($this->store);
	}

	public function testListDelegatesToStore(): void {
		$filter = $this->createMock(IFilter::class);
		$sort = $this->createMock(ISort::class);
		$services = [3 => new ServiceEntity()];

		$this->store->expects($this->once())
			->method('list')
			->with($filter, $sort)
			->willReturn($services);

		$this->assertSame($services, $this->service->list($filter, $sort));
	}

	public function testListFilterDelegatesToStore(): void {
		$filter = new FilterBase();

		$this->store->expects($this->once())
			->method('listFilter')
			->willReturn($filter);

		$this->assertSame($filter, $this->service->listFilter());
	}

	public function testListSortDelegatesToStore(): void {
		$sort = $this->createMock(ISort::class);

		$this->store->expects($this->once())
			->method('listSort')
			->willReturn($sort);

		$this->assertSame($sort, $this->service->listSort());
	}

	public function testFetchByUserIdBuildsUidFilter(): void {
		$filter = new FilterBase();
		$services = [4 => new ServiceEntity()];

		$this->store->expects($this->once())
			->method('listFilter')
			->willReturn($filter);
		$this->store->expects($this->once())
			->method('list')
			->with($this->callback(function (IFilter $passedFilter) use ($filter): bool {
				return $passedFilter === $filter
					&& $passedFilter->conditions() === [[
						'attribute' => 'uid',
						'value' => 'user-1',
						'comparator' => FilterComparisonOperator::EQ,
						'conjunction' => FilterConjunctionOperator::AND,
					]];
			}))
			->willReturn($services);

		$this->assertSame($services, $this->service->fetchByUserId('user-1'));
	}

	public function testFetchByUserIdAndServiceIdReturnsMatchingService(): void {
		$filter = new FilterBase();
		$service = new ServiceEntity();
		$service->setId(5);

		$this->store->expects($this->once())
			->method('listFilter')
			->willReturn($filter);
		$this->store->expects($this->once())
			->method('list')
			->with($this->callback(function (IFilter $passedFilter) use ($filter): bool {
				$conditions = $passedFilter->conditions();

				return $passedFilter === $filter
					&& count($conditions) === 2
					&& $conditions[0]['attribute'] === 'uid'
					&& $conditions[0]['value'] === 'user-1'
					&& $conditions[1]['attribute'] === 'id'
					&& $conditions[1]['value'] === 5;
			}))
			->willReturn([5 => $service]);

		$this->assertSame($service, $this->service->fetchByUserIdAndServiceId('user-1', 5));
	}

	public function testFetchByUserIdAndServiceIdReturnsNullWhenMissing(): void {
		$filter = new FilterBase();

		$this->store->expects($this->once())
			->method('listFilter')
			->willReturn($filter);
		$this->store->expects($this->once())
			->method('list')
			->with($filter)
			->willReturn([]);

		$this->assertNull($this->service->fetchByUserIdAndServiceId('user-1', 5));
	}

	public function testFreshReturnsNewServiceEntity(): void {
		$service = $this->service->fresh();

		$this->assertInstanceOf(ServiceEntity::class, $service);
		$this->assertNotSame($service, $this->service->fresh());
	}

	public function testFetchDelegatesToStore(): void {
		$service = new ServiceEntity();
		$service->setId(9);

		$this->store->expects($this->once())
			->method('fetch')
			->with(9)
			->willReturn($service);

		$this->assertSame($service, $this->service->fetch(9));
	}

	public function testDepositCreatesWhenServiceHasNoId(): void {
		$service = new ServiceEntity();

		$this->store->expects($this->once())
			->method('create')
			->with($this->callback(function (ServiceEntity $entity): bool {
				return $entity->getUid() === 'user-1';
			}))
			->willReturn($service);
		$this->store->expects($this->never())
			->method('modify');

		$this->assertSame($service, $this->service->deposit('user-1', $service));
	}

	public function testDepositModifiesWhenServiceHasId(): void {
		$service = new ServiceEntity();
		$service->setId(12);

		$this->store->expects($this->once())
			->method('modify')
			->with('user-1', $service)
			->willReturn($service);
		$this->store->expects($this->never())
			->method('create');

		$this->assertSame($service, $this->service->deposit('user-1', $service));
	}

	public function testCreateAssignsUidBeforePersisting(): void {
		$service = new ServiceEntity();

		$this->store->expects($this->once())
			->method('create')
			->with($this->callback(function (ServiceEntity $entity): bool {
				return $entity->getUid() === 'user-1';
			}))
			->willReturnCallback(static fn (ServiceEntity $entity): ServiceEntity => $entity);

		$created = $this->service->create('user-1', $service);

		$this->assertSame($service, $created);
		$this->assertSame('user-1', $service->getUid());
	}

	public function testModifyScopesUpdateByUserId(): void {
		$service = new ServiceEntity();
		$service->setId(5);

		$this->store->expects($this->once())
			->method('modify')
			->with('user-1', $service)
			->willReturn($service);

		$this->assertSame($service, $this->service->modify('user-1', $service));
	}

	public function testDeleteScopesDeletionByUserId(): void {
		$service = new ServiceEntity();
		$service->setId(7);
		$service->setUid('user-1');

		$this->store->expects($this->once())
			->method('delete')
			->with('user-1', $service)
			->willReturn($service);

		$this->service->delete('user-1', $service);
	}
}
