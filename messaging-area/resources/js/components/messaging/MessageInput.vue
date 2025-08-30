<script setup lang="ts">
import { ref, watch } from 'vue'
import { Send } from 'lucide-vue-next'
import type { Message, Provider, SendMessageData } from '@/types/messaging.ts'

interface Props {
    provider: Provider
    sending?: boolean
    replyingTo?: Message | null
}

interface Emits {
    (event: 'send', data: SendMessageData): void
    (event: 'cancel'): void
    (event: 'compose', composing: boolean): void
}

const props = withDefaults(defineProps<Props>(), {
    sending: false,
    replyingTo: null
})

const emit = defineEmits<Emits>()

const content = ref<string>('')
const subject = ref<string>('')
const isComposing = ref<boolean>(false)

function handleSend(): void {
    if (!content.value.trim() || props.sending) return

    const messageData: SendMessageData = {
        content: content.value
    }

    if (props.provider === 'email') {
        messageData.subject = subject.value
        if (props.replyingTo) {
            messageData.reply_to_message_id = props.replyingTo.id as number
        }
    }

    emit('send', messageData)
}

function handleCancel(): void {
    resetForm()
    emit('cancel')
}

function resetForm(): void {
    content.value = ''
    subject.value = ''
    isComposing.value = false
}

function startComposing(): void {
    isComposing.value = true
    emit('compose', true)
}

watch(() => props.sending, (newSending, oldSending) => {
    if (oldSending && !newSending) {
        resetForm()
    }
})

watch(() => props.replyingTo, (newReply) => {
    if (newReply && props.provider === 'email') {
        isComposing.value = true
        if (newReply.subject) {
            subject.value = newReply.subject.startsWith('Re:') ? newReply.subject : `Re: ${newReply.subject}`
        }
    }
})

defineExpose({
    startComposing,
    resetForm
})
</script>

<template>
    <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
        <template v-if="provider === 'email'">
            <div v-if="isComposing" class="space-y-4">
                <div
                    v-if="replyingTo"
                    class="text-sm text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 p-3 rounded-lg border border-gray-200 dark:border-gray-700"
                >
                    <strong>Respondendo para:</strong> {{ replyingTo.subject || 'Sem assunto' }}
                </div>

                <input
                    v-model="subject"
                    placeholder="Assunto do email"
                    :disabled="sending"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-colors text-gray-900 dark:text-white placeholder-gray-500 disabled:opacity-50"
                />

                <textarea
                    v-model="content"
                    placeholder="Digite sua mensagem..."
                    rows="6"
                    :disabled="sending"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-colors text-gray-900 dark:text-white placeholder-gray-500 resize-none disabled:opacity-50"
                />

                <div class="flex gap-2 justify-end">
                    <button
                        @click="handleCancel"
                        :disabled="sending"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md text-sm transition-colors font-medium disabled:opacity-50"
                    >
                        Cancelar
                    </button>
                    <button
                        @click="handleSend"
                        :disabled="sending || !content.trim()"
                        class="px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-md hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors text-sm flex items-center gap-2 font-medium shadow-sm disabled:opacity-50"
                    >
                        <div v-if="sending" class="animate-spin rounded-full h-4 w-4 border border-white dark:border-gray-900 border-t-transparent"></div>
                        <Send v-else class="w-4 h-4" />
                        {{ sending ? 'Enviando...' : (replyingTo ? 'Responder' : 'Enviar Email') }}
                    </button>
                </div>
            </div>

            <div v-else class="flex gap-3">
        <textarea
            v-model="content"
            placeholder="Digite uma resposta rápida..."
            rows="2"
            :disabled="sending"
            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-colors text-gray-900 dark:text-white placeholder-gray-500 resize-none disabled:opacity-50"
        />
                <button
                    @click="handleSend"
                    :disabled="sending || !content.trim()"
                    class="px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-md hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors self-end shadow-sm disabled:opacity-50 flex items-center gap-2"
                >
                    <div v-if="sending" class="animate-spin rounded-full h-4 w-4 border border-white dark:border-gray-900 border-t-transparent"></div>
                    <Send v-else class="w-4 h-4" />
                </button>
            </div>
        </template>

        <template v-else>
            <div class="flex gap-3">
                <input
                    v-model="content"
                    type="text"
                    placeholder="Digite sua mensagem..."
                    :disabled="sending"
                    @keydown.enter="handleSend"
                    class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-full bg-white dark:bg-gray-800 text-sm focus:outline-none focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-colors text-gray-900 dark:text-white placeholder-gray-500 disabled:opacity-50"
                />
                <button
                    @click="handleSend"
                    :disabled="sending || !content.trim()"
                    class="px-4 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-full hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors shadow-sm disabled:opacity-50 flex items-center gap-2"
                >
                    <div v-if="sending" class="animate-spin rounded-full h-4 w-4 border border-white dark:border-gray-900 border-t-transparent"></div>
                    <Send v-else class="w-5 h-5" />
                </button>
            </div>
        </template>
    </div>
</template>
