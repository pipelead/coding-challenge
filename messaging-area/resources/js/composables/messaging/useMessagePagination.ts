import { ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import { useMessagesStore } from '@/stores/messages.ts'

export function useMessagePagination(selectedConversation: any) {
    const page = usePage()
    const messagesStore = useMessagesStore()

    const isLoadingMore = ref(false)

    async function loadMoreMessages(): Promise<void> {
        if (!selectedConversation.value || isLoadingMore.value) {
            return
        }

        const messages = page.props.messages as any
        if (!messages?.next_page_url) {
            return
        }

        isLoadingMore.value = true

        router.get('/', {
            channel: page.props.selectedChannel || 'whatsapp',
            conversation_id: selectedConversation.value.id,
            page: new URL(messages.next_page_url).searchParams.get('page')
        }, {
            preserveScroll: true,
            preserveState: true,
            only: ['messages'],
            replace: false,
            onSuccess: (response) => {
                const newMessages = response.props.messages?.data || []
                messagesStore.addMessages(newMessages)
            },
            onError: () => {
                console.error('Failed to load more messages.')
            },
            onFinish: () => {
                isLoadingMore.value = false
            }
        })
    }

    return {
        isLoadingMore,
        loadMoreMessages
    }
}
