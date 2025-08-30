import { ref } from 'vue'
import type { Conversation, Message, SendMessageData } from '@/types/messaging.ts'
import { useMessageQueue } from '@/composables/messaging/useMessageQueue.ts'
import { useMessagesStore } from '@/stores/messages.ts'
import { sendMessageAPI } from '@/utils/messaging/api.ts'

export function useMessageSending() {
    const { createTempMessage } = useMessageQueue()
    const messagesStore = useMessagesStore()

    const isSending = ref<boolean>(false)

    async function sendMessage(
        selectedConversation: Conversation,
        messageData: SendMessageData,
        replyingToMessage: { value: Message | null }
    ): Promise<void> {
        if (!selectedConversation || isSending.value) return

        isSending.value = true

        const tempMessage = {
            ...createTempMessage({
                content: messageData.content,
                subject: messageData.subject,
                provider: selectedConversation.provider
            }),
            conversation_id: selectedConversation.id,
            conversation: selectedConversation
        }

        messagesStore.addLocalMessage(tempMessage)

        try {
            const data = await sendMessageAPI(selectedConversation.id, messageData)

            messagesStore.updateMessage(tempMessage.id, {
                id: data.message_id,
                status: 'queued'
            })

            replyingToMessage.value = null
        } catch (error: any) {
            messagesStore.updateMessage(tempMessage.id, {
                status: 'failed',
                error_message: error.message
            })
        } finally {
            isSending.value = false
        }
    }

    return {
        isSending,
        sendMessage
    }
}
