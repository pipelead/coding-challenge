<template>
	<div class="h-screen grid grid-cols-12">
		<aside class="col-span-4 border-r overflow-y-auto">
			<ContactList :contacts="contacts" :active-id="contact.id" />
		</aside>
		<section class="col-span-8 flex flex-col h-screen">
			<header class="p-4 border-b">
				<h1 class="font-semibold text-gray-900">{{ contact.name }}</h1>
			</header>
			<div class="flex-1 overflow-y-auto">
				<ConversationView :messages="messages" :has-more="messages.next_page_url !== null" @loadMore="loadMore" />
			</div>
			<MessageInput :contact-id="contact.id" />
		</section>
	</div>
</template>

<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { onMounted, onBeforeUnmount, ref } from 'vue'
// @ts-expect-error - shim de .vue cobre o tipo
import ContactList from '../../Components/ContactList.vue'
import ConversationView from '../../Components/ConversationView.vue'
import MessageInput from '../../Components/MessageInput.vue'

const props = defineProps<{ contact: any; messages: any; contacts: any }>()

function loadMore() {
	if (!props.messages.prev_page_url) return
	router.visit(props.messages.prev_page_url, { preserveScroll: true, preserveState: true, only: ['messages'] })
}

const lastId = ref<number>(props.messages?.data?.[0]?.id ?? 0)
let timer: number | undefined
const ACTIVE_MS = 2000
const IDLE_MS = 10000

function computeInterval() {
	return document.hidden ? IDLE_MS : ACTIVE_MS
}

async function fetchUpdates() {
	if (document.hidden) return
	try {
		const res = await fetch(`/conversations/${props.contact.id}/messages/updates?last_id=${lastId.value}`)
		const json = await res.json()
		const items = Array.isArray(json?.data) ? json.data : []
		if (items.length > 0) {
			// Atualiza lastId e refaz visita parcial para manter paginação e ordenação
			lastId.value = Math.max(lastId.value, ...items.map((m: any) => m.id))
			router.visit(window.location.href, { preserveScroll: true, preserveState: true, only: ['messages'], replace: true })
		}
	} catch (e) {
		// silencioso; próximo tick tenta novamente
	}
}

function startPolling() {
	stopPolling()
	const interval = computeInterval()
	timer = window.setInterval(fetchUpdates, interval) as unknown as number
}

function stopPolling() {
	if (timer) {
		clearInterval(timer)
		timer = undefined
	}
}

function handleVisibility() {
	startPolling()
}

onMounted(() => {
	startPolling()
	document.addEventListener('visibilitychange', handleVisibility)
})

onBeforeUnmount(() => {
	stopPolling()
	document.removeEventListener('visibilitychange', handleVisibility)
})
</script>
