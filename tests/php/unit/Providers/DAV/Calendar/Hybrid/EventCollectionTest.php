<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Tests\Unit\Providers\DAV\Calendar\Hybrid;

use OCA\DAVC\Models\Calendars\Collection;
use OCA\DAVC\Models\Calendars\Entity;
use OCA\DAVC\Providers\DAV\Calendar\Hybrid\EventCollection;
use OCA\DAVC\Providers\DAV\Calendar\Hybrid\EventEntity;
use OCA\DAVC\Service\Local\LocalEventsService;
use OCA\DAVC\Service\Remote\RemoteFactory;
use OCA\DAVC\Store\Local\Filters\EventFilter;
use OCA\DAVC\Store\Local\ServicesStore;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EventCollectionTest extends TestCase {
	private ServicesStore&MockObject $servicesStore;
	private LocalEventsService&MockObject $localService;
	private RemoteFactory&MockObject $remoteFactory;
	private Collection $collection;
	private EventCollection $sut;

	protected function setUp(): void {
		parent::setUp();

		$this->servicesStore = $this->createMock(ServicesStore::class);
		$this->localService = $this->createMock(LocalEventsService::class);
		$this->remoteFactory = $this->createMock(RemoteFactory::class);

		$this->collection = new Collection();
		$this->collection->userId = 'user1';
		$this->collection->serviceId = 1;
		$this->collection->localId = 42;
		$this->collection->uuid = 'collection-uuid';
		$this->collection->remoteId = '/remote/Calendar/';

		$this->sut = new EventCollection(
			$this->servicesStore,
			$this->localService,
			$this->remoteFactory,
			$this->collection,
		);

		$this->localService->method('entityListFilter')
			->willReturnCallback(static fn (): EventFilter => new EventFilter());
	}

	/**
	 * extract the value of a given attribute from a filter
	 */
	private function conditionValue(EventFilter $filter, string $attribute): mixed {
		foreach ($filter->conditions() as $condition) {
			if ($condition['attribute'] === $attribute) {
				return $condition['value'];
			}
		}
		return null;
	}

	public function testGetChildByUuid(): void {
		$entity = new Entity();
		$entity->uuid = 'entity-uuid';

		$this->localService->expects($this->once())
			->method('entityList')
			->willReturnCallback(function (EventFilter $filter) use ($entity): array {
				// the .ics extension is stripped before the uuid lookup
				$this->assertSame('entity-uuid', $this->conditionValue($filter, 'uuid'));
				return [$entity];
			});

		$child = $this->sut->getChild('entity-uuid.ics');

		$this->assertInstanceOf(EventEntity::class, $child);
		$this->assertSame('entity-uuid', $child->getName());
	}

	public function testGetChildFallsBackToRemoteEntityId(): void {
		$entity = new Entity();
		$entity->uuid = 'entity-uuid';
		$entity->remoteEntityId = '/remote/Calendar/submitted-id.ics';

		$this->localService->expects($this->exactly(2))
			->method('entityList')
			->willReturnCallback(function (EventFilter $filter) use ($entity): array {
				// uuid lookup yields nothing, ceid lookup resolves the entity by
				// the reconstructed full resource path (extension preserved)
				if ($this->conditionValue($filter, 'uuid') === 'submitted-id') {
					return [];
				}
				$this->assertSame('/remote/Calendar/submitted-id.ics', $this->conditionValue($filter, 'ceid'));
				return [$entity];
			});

		$child = $this->sut->getChild('submitted-id.ics');

		$this->assertInstanceOf(EventEntity::class, $child);
		$this->assertSame('entity-uuid', $child->getName());
	}

	public function testGetChildThrowsWhenMissing(): void {
		$this->localService->expects($this->exactly(2))
			->method('entityList')
			->willReturn([]);

		$this->expectException(\Sabre\DAV\Exception\NotFound::class);

		$this->sut->getChild('unknown.ics');
	}

	public function testChildExistsByUuid(): void {
		$this->localService->expects($this->once())
			->method('entityList')
			->willReturnCallback(function (EventFilter $filter): array {
				$this->assertSame('entity-uuid', $this->conditionValue($filter, 'uuid'));
				return [new Entity()];
			});

		$this->assertTrue($this->sut->childExists('entity-uuid.ics'));
	}

	public function testChildExistsFallsBackToRemoteEntityId(): void {
		$this->localService->expects($this->exactly(2))
			->method('entityList')
			->willReturnCallback(function (EventFilter $filter): array {
				if ($this->conditionValue($filter, 'uuid') === 'submitted-id') {
					return [];
				}
				$this->assertSame('/remote/Calendar/submitted-id.ics', $this->conditionValue($filter, 'ceid'));
				return [new Entity()];
			});

		$this->assertTrue($this->sut->childExists('submitted-id.ics'));
	}

	public function testChildExistsReturnsFalseWhenMissing(): void {
		$this->localService->expects($this->exactly(2))
			->method('entityList')
			->willReturn([]);

		$this->assertFalse($this->sut->childExists('unknown.ics'));
	}
}
