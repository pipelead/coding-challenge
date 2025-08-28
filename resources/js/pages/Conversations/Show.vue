<template>
	<div class="h-screen grid grid-cols-12">
		<aside class="col-span-4 border-r overflow-y-auto">
			<ContactList :contacts="contacts" :active-id="contact.id" />
		</aside>
		<section class="col-span-8 flex flex-col h-screen">
			<header class="p-4 border-b sticky top-0 bg-white z-10 flex items-center justify-between">
				<h1 class="font-semibold text-gray-900">{{ contact.name }}</h1>
				<span class="text-xs text-gray-500">{{ messages.total }} msgs</span>
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
import { onMounted, watch } from 'vue'
// @ts-expect-error - shim .vue
import ContactList from '../../Components/ContactList.vue'
import ConversationView from '../../Components/ConversationView.vue'
import MessageInput from '../../Components/MessageInput.vue'
import { useConversationsStore } from '@/stores/conversations'

const props = defineProps<{ contact: any; messages: any; contacts: any }>()

const store = useConversationsStore()

function loadMore() {
	if (!props.messages.next_page_url) return
	router.visit(props.messages.next_page_url, { preserveScroll: true, preserveState: true, only: ['messages'] })
}

onMounted(() => {
	store.setContacts(props.contacts)
	store.setActiveContact(props.contact.id)
	store.setMessages(props.messages)
})

watch(() => props.messages, (val) => {
	store.setMessages(val)
})
</script>
