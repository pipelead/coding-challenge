<template>
	<form @submit.prevent="onSubmit" class="flex items-end gap-2 p-3 border-t border-gray-200">
		<select v-model="channel" class="border rounded px-2 py-1 text-sm">
			<option value="whatsapp">WhatsApp</option>
			<option value="messenger">Messenger</option>
			<option value="email">Email</option>
		</select>
		<textarea
			v-model="body"
			class="flex-1 border rounded px-3 py-2 text-sm"
			rows="2"
			placeholder="Digite uma mensagem..."
		></textarea>
		<button :disabled="sending || !body.trim()" class="bg-indigo-600 disabled:bg-indigo-300 text-white px-3 py-2 rounded text-sm flex items-center gap-2">
			<span v-if="sending" class="animate-spin inline-block h-4 w-4 border-2 border-white/50 border-t-white rounded-full"></span>
			<span>Enviar</span>
		</button>
	</form>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps<{ contactId: number }>()

const sending = ref(false)
const channel = ref<'whatsapp' | 'messenger' | 'email'>('whatsapp')
const body = ref('')

function onSubmit() {
	if (!body.value.trim()) return
	sending.value = true
	router.post(
		route('messages.store'),
		{ contact_id: props.contactId, channel_slug: channel.value, body: body.value },
		{
			onFinish: () => {
				sending.value = false
				body.value = ''
			},
		}
	)
}
</script>
