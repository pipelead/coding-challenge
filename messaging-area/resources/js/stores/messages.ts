import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import type { Message } from '@/types/messaging'

export const useMessagesStore = defineStore('messages', () => {
    const messages = ref<Message[]>([])
    const currentConversationId = ref<number | null>(null)

    const conversationMessages = computed(() => {
        if (!currentConversationId.value) return []
        return messages.value
            .filter(m => m.conversation_id === currentConversationId.value)
            .sort((a, b) => new Date(a.created_at).getTime() - new Date(b.created_at).getTime())
    })

    function setConversation(conversationId: number, initialMessages: Message[]) {
        currentConversationId.value = conversationId

        messages.value = messages.value.filter(m => m.conversation_id !== conversationId)

        messages.value.push(...initialMessages)
    }

    function addMessages(newMessages: Message[]) {
        if (!currentConversationId.value) return

        for (const newMessage of newMessages) {
            const existingIndex = messages.value.findIndex(m => m.id === newMessage.id)

            if (existingIndex !== -1) {
                messages.value[existingIndex] = { ...messages.value[existingIndex], ...newMessage }
                continue;
            }

            messages.value.push(newMessage)
        }
    }

    function addLocalMessage(message: Message) {
        messages.value.push(message)
    }

    function updateMessage(messageId: string | number, updates: Partial<Message>) {
        const index = messages.value.findIndex(m => m.id === messageId)
        if (index !== -1) {
            Object.assign(messages.value[index], updates)
        }
    }

    return {
        conversationMessages,
        setConversation,
        addMessages,
        addLocalMessage,
        updateMessage,
    }
})
