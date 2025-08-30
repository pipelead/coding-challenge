import { ref, nextTick } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import type { Provider } from '@/types/messaging.ts'
import { useMessagesStore } from '@/stores/messages.ts'

export function useConversationSelection(messageListRef: any) {
    const page = usePage()
    const messagesStore = useMessagesStore()

    const isLoading = ref<boolean>(false)

    function handleChannelChange(channel: Provider): void {
        router.get('/', { channel, conversation_id: null }, {
            preserveState: false,
            preserveScroll: false,
            replace: true
        })
    }

    function handleConversationSelect(conversationId: number): void {
        isLoading.value = true

        router.get('/', {
            channel: page.props.selectedChannel || 'whatsapp',
            conversation_id: conversationId
        }, {
            preserveState: false,
            onSuccess: (page) => {
                const serverMessages = page.props.messages?.data || []
                messagesStore.setConversation(conversationId, serverMessages)

                nextTick(() => {
                    messageListRef.value?.scrollToBottom()
                })
            },
            onFinish: () => isLoading.value = false
        })
    }

    return {
        isLoading,
        handleChannelChange,
        handleConversationSelect
    }
}
