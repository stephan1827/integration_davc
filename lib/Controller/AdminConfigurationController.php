<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Controller;

use OCA\DAVC\Service\ConfigurationService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

/**
 * @psalm-api
 */
class AdminConfigurationController extends Controller {

	public function __construct(
		string $appName,
		IRequest $request,
		private readonly ConfigurationService $ConfigurationService,
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * handles save configuration requests
	 *
	 * @param array $values key/value pairs to save
	 *
	 * @return DataResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/admin-configuration')]
	public function depositConfiguration(array $values): DataResponse {

		$this->ConfigurationService->depositSystem($values);

		return new DataResponse(true);
	}
}
