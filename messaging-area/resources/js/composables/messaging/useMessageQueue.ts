import type { Message, TempMessage, Provider } from '@/types/messaging.ts'

interface CreateTempMessageData {
    content: string
    subject?: string
    provider: Provider
}

export function useMessageQueue() {
    const createTempMessage = (data: CreateTempMessageData): TempMessage => {
        const tempId = `temp_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`

        return {
            id: tempId,
            content: data.content,
            subject: data.subject,
            created_at: new Date().toISOString(),
            isFromUser: false,
            status: 'sending',
            provider: data.provider,
            error_message: null
        }
    }

    const mergeMessages = (serverMessages: Message[], localMessages: Message[]): Message[] => {
        const combined = [...serverMessages, ...localMessages]
        const uniqueMessages = combined.filter((message, index, arr) => {
            const firstIndex = arr.findIndex(m => m.id === message.id)
            return firstIndex === index
        })

        return uniqueMessages.sort((a, b) => {
            const timeA = new Date(a.created_at).getTime()
            const timeB = new Date(b.created_at).getTime()
            return timeA - timeB
        })
    }

    const updateMessage = (
        messages: Message[],
        messageId: string | number,
        updates: Partial<Message>
    ): void => {
        const index = messages.findIndex(m => m.id === messageId)
        if (index !== -1) {
            Object.assign(messages[index], updates)
        }
    }

    return {
        createTempMessage,
        mergeMessages,
        updateMessage
    }
}
