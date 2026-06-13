<!--
 - SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 - SPDX-License-Identifier: AGPL-3.0-or-later
-->

<script setup lang="ts">
import axios from '@nextcloud/axios'
import { showError, showSuccess } from '@nextcloud/dialogs'
import { translate as t } from '@nextcloud/l10n'
import { generateUrl } from '@nextcloud/router'
import { computed, onMounted, ref } from 'vue'
import NcButton from '@nextcloud/vue/components/NcButton'
import NcDialog from '@nextcloud/vue/components/NcDialog'
import NcSelect from '@nextcloud/vue/components/NcSelect'
import NcTextField from '@nextcloud/vue/components/NcTextField'
import CheckIcon from 'vue-material-design-icons/Check.vue'
import CloseIcon from 'vue-material-design-icons/Close.vue'
import DeleteIcon from 'vue-material-design-icons/Delete.vue'
import PencilIcon from 'vue-material-design-icons/Pencil.vue'
import PlusIcon from 'vue-material-design-icons/Plus.vue'

interface Connection {
	location_host?: string
	location_protocol?: string
	location_port?: string | number
	location_path?: string
	[key: string]: unknown
}

interface Template {
	id: string
	domain: string
	connection: Connection
}

const baseUrl = generateUrl('/apps/integration_davc/admin/templates')
const protocolOptions = ['https', 'http']

const templates = ref<Template[]>([])
const editing = ref<Template | null>(null)

const dialogTitle = computed(() => editing.value?.id
	? t('integration_davc', 'Edit template')
	: t('integration_davc', 'Add template'))

onMounted(() => {
	fetchTemplates()
})

async function fetchTemplates(): Promise<void> {
	try {
		const response = await axios.get(baseUrl)
		templates.value = response.data
	} catch (error) {
		showError(t('integration_davc', 'Failed to load service templates'))
	}
}

function addTemplate(): void {
	editing.value = { id: '', domain: '', connection: { location_protocol: 'https' } }
}

function editTemplate(template: Template): void {
	editing.value = {
		id: template.id,
		domain: template.domain,
		connection: { ...template.connection },
	}
}

function cancelEdit(): void {
	editing.value = null
}

function onDialogToggle(value: boolean): void {
	if (!value) {
		cancelEdit()
	}
}

function endpointSummary(template: Template): string {
	const protocol = template.connection.location_protocol || 'https'
	const host = template.connection.location_host || '—'
	const port = template.connection.location_port ? ':' + template.connection.location_port : ''
	const path = template.connection.location_path || ''
	return `${protocol}://${host}${port}${path}`
}

async function saveTemplate(): Promise<void> {
	if (editing.value === null) {
		return
	}
	const isNew = editing.value.id === ''
	const payload = {
		id: editing.value.id,
		domain: editing.value.domain,
		connection: editing.value.connection,
	}
	try {
		const response = await axios.post(`${baseUrl}/${isNew ? 'create' : 'modify'}`, payload)
		templates.value = response.data
		editing.value = null
		showSuccess(t('integration_davc', 'Service template saved'))
	} catch (error) {
		showError(t('integration_davc', 'Failed to save service template'))
	}
}

async function deleteTemplate(template: Template): Promise<void> {
	try {
		const response = await axios.post(`${baseUrl}/delete`, { id: template.id })
		templates.value = response.data
	} catch (error) {
		showError(t('integration_davc', 'Failed to delete service template'))
	}
}
</script>

<template>
	<div class="davc-templates">
		<h3 class="davc-templates__title">
			{{ t('integration_davc', 'Service discovery templates') }}
		</h3>
		<p class="settings-hint">
			{{ t('integration_davc', 'Define endpoint configuration for email domains. When a user connects using an email address, the matching template fills in the server details automatically.') }}
		</p>

		<ul v-if="templates.length > 0" class="davc-templates__list">
			<li v-for="template in templates" :key="template.id" class="davc-templates__item">
				<span class="davc-templates__domain">{{ template.domain }}</span>
				<span class="davc-templates__endpoint">{{ endpointSummary(template) }}</span>
				<NcButton variant="tertiary" :aria-label="t('integration_davc', 'Edit')" @click="editTemplate(template)">
					<template #icon>
						<PencilIcon :size="20" />
					</template>
				</NcButton>
				<NcButton variant="tertiary" :aria-label="t('integration_davc', 'Delete')" @click="deleteTemplate(template)">
					<template #icon>
						<DeleteIcon :size="20" />
					</template>
				</NcButton>
			</li>
		</ul>
		<p v-else class="davc-templates__empty">
			{{ t('integration_davc', 'No service templates defined') }}
		</p>

		<NcButton @click="addTemplate()">
			<template #icon>
				<PlusIcon :size="20" />
			</template>
			{{ t('integration_davc', 'Add template') }}
		</NcButton>

		<NcDialog
			v-if="editing !== null"
			:open="true"
			:name="dialogTitle"
			size="normal"
			@update:open="onDialogToggle">
			<div class="davc-templates__form">
				<NcTextField
					v-model="editing.domain"
					:label="t('integration_davc', 'Email domain')"
					autocomplete="off"
					autocorrect="off"
					autocapitalize="none"
					:placeholder="t('integration_davc', 'example.com')" />
				<NcTextField
					v-model="editing.connection.location_host"
					:label="t('integration_davc', 'Service address')"
					autocomplete="off"
					autocorrect="off"
					autocapitalize="none"
					:placeholder="t('integration_davc', 'Domain or IP address')" />
				<div class="davc-templates__form-field">
					<label for="davc-template-protocol">
						{{ t('integration_davc', 'Service protocol') }}
					</label>
					<NcSelect
						v-model="editing.connection.location_protocol"
						inputId="davc-template-protocol"
						:options="protocolOptions"
						:clearable="false" />
				</div>
				<NcTextField
					v-model="editing.connection.location_port"
					:label="t('integration_davc', 'Service port')"
					autocomplete="off"
					autocorrect="off"
					autocapitalize="none"
					:placeholder="t('integration_davc', 'Leave empty for default. http (80) https (443)')" />
				<NcTextField
					v-model="editing.connection.location_path"
					:label="t('integration_davc', 'Service path')"
					autocomplete="off"
					autocorrect="off"
					autocapitalize="none"
					:placeholder="t('integration_davc', 'Leave empty for default path (/.well-known/caldav)')" />
			</div>
			<template #actions>
				<NcButton @click="cancelEdit()">
					<template #icon>
						<CloseIcon :size="20" />
					</template>
					{{ t('integration_davc', 'Cancel') }}
				</NcButton>
				<NcButton variant="primary" @click="saveTemplate()">
					<template #icon>
						<CheckIcon :size="20" />
					</template>
					{{ t('integration_davc', 'Save') }}
				</NcButton>
			</template>
		</NcDialog>
	</div>
</template>

<style scoped lang="scss">
.davc-templates {
	margin-top: 30px;
	padding-top: 20px;
	border-top: 1px solid var(--color-border);

	.davc-templates__title {
		font-size: 18px;
		font-weight: bold;
		margin-bottom: 8px;
	}

	.davc-templates__list {
		list-style: none;
		padding: 0;
		margin: 12px 0;

		.davc-templates__item {
			display: flex;
			align-items: center;
			gap: 12px;
			padding: 8px 0;
			border-bottom: 1px solid var(--color-border);

			.davc-templates__domain {
				font-weight: 500;
				min-width: 200px;
			}

			.davc-templates__endpoint {
				flex: 1;
				color: var(--color-text-maxcontrast);
				font-size: 13px;
			}
		}
	}

	.davc-templates__empty {
		color: var(--color-text-maxcontrast);
		margin: 12px 0;
	}

	.davc-templates__form {
		display: flex;
		flex-direction: column;
		gap: 16px;
		padding: 8px 0;

		.davc-templates__form-field {
			display: flex;
			flex-direction: column;
			gap: 4px;

			label {
				font-weight: 500;
			}
		}
	}
}
</style>
