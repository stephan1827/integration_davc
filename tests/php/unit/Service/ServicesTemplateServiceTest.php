<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Tests\Unit\Service;

use OCA\DAVC\Service\ServicesTemplateService;
use OCA\DAVC\Store\Local\ServicesTemplateStore;
use OCA\DAVC\Utile\UUID;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ServicesTemplateServiceTest extends TestCase {
	private ServicesTemplateStore&MockObject $store;

	private ServicesTemplateService $service;

	protected function setUp(): void {
		parent::setUp();

		$this->store = $this->createMock(ServicesTemplateStore::class);
		$this->service = new ServicesTemplateService($this->store);
	}

	public function testFindByDomainDelegatesToStore(): void {
		$rows = [['id' => 'a', 'domain' => 'example.com', 'connection' => '{}']];
		$this->store->expects($this->once())
			->method('fetchByDomain')
			->with('example.com')
			->willReturn($rows);

		$this->assertSame($rows, $this->service->findByDomain('example.com'));
	}

	public function testListDecodesConnectionJson(): void {
		$this->store->method('list')->willReturn([
			['id' => 'a', 'domain' => 'example.com', 'connection' => '{"location_host":"dav.example.com","location_protocol":"https"}'],
		]);

		$result = $this->service->list();

		$this->assertSame('dav.example.com', $result[0]['connection']['location_host']);
		$this->assertSame('https', $result[0]['connection']['location_protocol']);
	}

	public function testListReturnsEmptyConnectionForInvalidJson(): void {
		$this->store->method('list')->willReturn([
			['id' => 'a', 'domain' => 'example.com', 'connection' => 'not-json'],
		]);

		$result = $this->service->list();

		$this->assertSame([], $result[0]['connection']);
	}

	public function testCreateGeneratesUuidAndDelegates(): void {
		$connection = ['location_host' => 'dav.example.com'];
		$this->store->expects($this->once())
			->method('create')
			->with(
				$this->callback(static fn (string $id): bool => UUID::is_valid($id)),
				'example.com',
				$connection,
			)
			->willReturn(true);

		$this->assertTrue($this->service->create('example.com', $connection));
	}

	public function testModifyDelegatesToStore(): void {
		$connection = ['location_host' => 'dav.example.com'];
		$this->store->expects($this->once())
			->method('modify')
			->with('tpl-1', 'example.com', $connection)
			->willReturn(true);

		$this->assertTrue($this->service->modify('tpl-1', 'example.com', $connection));
	}

	public function testDeleteDelegatesToStore(): void {
		$this->store->expects($this->once())
			->method('delete')
			->with('tpl-1')
			->willReturn(true);

		$this->assertTrue($this->service->delete('tpl-1'));
	}
}
