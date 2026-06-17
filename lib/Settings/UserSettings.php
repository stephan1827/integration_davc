<?php

declare(strict_types=1);

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\DAVC\Settings;

use OCA\DAVC\AppInfo\Application;
use OCA\DAVC\Service\ConfigurationService;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IInitialState;
use OCP\Settings\ISettings;
use OCP\Util;

/**
 * @psalm-api
 */
class UserSettings implements ISettings {

	public function __construct(
		private readonly IInitialState $initialStateService,
		private readonly ConfigurationService $configurationService,
	) {
	}

	#[\Override]
	public function getForm(): TemplateResponse {
		Util::addScript(Application::APP_ID, Application::APP_ID . '-UserSettings');

		// retrieve system configuration
		$configuration['system_contacts'] = $this->configurationService->isContactsAppAvailable();
		$configuration['system_events'] = $this->configurationService->isCalendarAppAvailable();
		// administrator transport security policy
		$configuration['force_certificate_verification'] = $this->configurationService->getForceCertificateVerification();
		$configuration['forbid_insecure_http'] = $this->configurationService->getForbidInsecureHttp();

		$this->initialStateService->provideInitialState('system-configuration', $configuration);

		return new TemplateResponse(Application::APP_ID, 'UserSettings');
	}

	#[\Override]
	public function getSection(): string {
		return 'connected-accounts';
	}

	#[\Override]
	public function getPriority(): int {
		return 20;
	}
}
