<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Controller;

use OCA\DAVC\Service\ServicesTemplateService;
use OCA\DAVC\Utile\Validator;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\Attribute\FrontpageRoute;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

/**
 * @psalm-api
 */
class AdminTemplateController extends Controller {

	public function __construct(
		string $appName,
		IRequest $request,
		private ServicesTemplateService $templateService,
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * list all service discovery templates
	 *
	 * @return DataResponse
	 */
	#[FrontpageRoute(verb: 'GET', url: '/admin/templates')]
	public function list(): DataResponse {

		return new DataResponse($this->templateService->list());
	}

	/**
	 * create a service discovery template
	 *
	 * @param string $domain email domain the template applies to
	 * @param array $connection connection settings
	 *
	 * @return DataResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/admin/templates/create')]
	public function create(string $domain, array $connection = []): DataResponse {

		$domain = trim($domain);
		if (!Validator::fqdn($domain)) {
			return new DataResponse('Invalid domain provided.', Http::STATUS_BAD_REQUEST);
		}
		if (!$this->templateService->create($domain, $connection)) {
			return new DataResponse('Failed to create service template.', Http::STATUS_INTERNAL_SERVER_ERROR);
		}
		return new DataResponse($this->templateService->list());
	}

	/**
	 * modify a service discovery template
	 *
	 * @param string $id service template id
	 * @param string $domain email domain the template applies to
	 * @param array $connection connection settings
	 *
	 * @return DataResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/admin/templates/modify')]
	public function modify(string $id, string $domain, array $connection = []): DataResponse {

		$domain = trim($domain);
		if ($id === '') {
			return new DataResponse('Invalid template id.', Http::STATUS_BAD_REQUEST);
		}
		if (!Validator::fqdn($domain)) {
			return new DataResponse('Invalid domain provided.', Http::STATUS_BAD_REQUEST);
		}
		$this->templateService->modify($id, $domain, $connection);
		return new DataResponse($this->templateService->list());
	}

	/**
	 * delete a service discovery template
	 *
	 * @param string $id service template id
	 *
	 * @return DataResponse
	 */
	#[FrontpageRoute(verb: 'POST', url: '/admin/templates/delete')]
	public function destroy(string $id): DataResponse {

		if ($id === '') {
			return new DataResponse('Invalid template id.', Http::STATUS_BAD_REQUEST);
		}
		$this->templateService->delete($id);
		return new DataResponse($this->templateService->list());
	}
}
