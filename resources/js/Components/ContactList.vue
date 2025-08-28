<template>
	<div class="divide-y divide-gray-200">
		<div
			v-for="contact in contacts.data"
			:key="contact.id"
			class="flex items-center gap-3 p-3 hover:bg-gray-50 cursor-pointer"
		>
			<div class="h-10 w-10 flex items-center justify-center rounded-full bg-gray-200 text-gray-600 font-medium">
				{{ initials(contact.name) }}
			</div>
			<Link
				:href="route('conversations.show', { contact: contact.id })"
				class="flex-1 min-w-0"
			>
				<div class="flex items-center justify-between">
					<p class="font-medium text-gray-900 truncate" :class="{ 'text-indigo-600': contact.id === activeId }">
						{{ contact.name }}
					</p>
					<span v-if="contact.unread_count > 0" class="ml-2 inline-flex items-center justify-center rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold px-2 py-0.5">
						{{ contact.unread_count }}
					</span>
				</div>
				<p class="text-sm text-gray-500 truncate">
					{{ lastMessagePreview(contact) }}
				</p>
			</Link>
		</div>
	</div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

defineProps<{
	contacts: any
	activeId?: number | null
}>()

function initials(name: string): string {
	return name
		.split(' ')
		.map((n) => n[0])
		.join('')
		.slice(0, 2)
		.toUpperCase()
}

function lastMessagePreview(contact: any): string {
	const msg = Array.isArray(contact.messages) && contact.messages.length > 0 ? contact.messages[0] : null
	return msg?.body ?? ''
}
</script>
