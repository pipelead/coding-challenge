import { watch, computed } from 'vue'
import { usePage, usePoll } from '@inertiajs/vue3'
import type { Conversation, Message } from '@/types/messaging.ts'
import { useMessagesStore } from '@/stores/messages.ts'

export function useMessagePolling(selectedConversation: any) {
    const page = usePage()
    const messagesStore = useMessagesStore()

    const serverMessages = computed<Message[]>(() => {
        const messages = page.props.messages as any
        return messages?.data || []
    })

    usePoll(5000, {
        only: ['conversations', 'messages']
    }, {
        autoStart: true
    })

    watch(serverMessages, (newMessages) => {
        if (selectedConversation.value && newMessages.length > 0) {
            messagesStore.addMessages(newMessages)
        }
    }, { deep: true })

    return {
        serverMessages
    }
}
