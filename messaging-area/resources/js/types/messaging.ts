export type MessageStatus =
    | 'pending'
    | 'sent'
    | 'delivered'
    | 'read'
    | 'failed'
    | 'sending'
    | 'queued'

export type Provider = 'whatsapp' | 'messenger' | 'email'

export interface Contact {
    id: number
    name: string
    email: string
    phone?: string
    avatar_path?: string
    avatar_url?: string
}

export interface Message {
    id: number | string
    content: string
    subject?: string
    created_at: string
    isFromUser: boolean
    status: MessageStatus
    provider: Provider
    error_message?: string | null
    conversation_id: number
}

export interface Conversation {
    id: number
    name: string
    avatar?: string
    lastMessage: string
    lastMessageTime: string
    unreadCount: number
    unreadMessagesCount?: number
    hasUnreadMessages?: boolean
    provider: Provider
    contact?: Contact
}

export interface Channel {
    value: Provider
    label: string
    icon: string
    color: string
    supports_read_receipts: boolean
}

export type TempMessage = Omit<Message, 'id'> & {
    id: string
}

export type SendMessageData = Pick<Message, 'content' | 'subject'> & {
    reply_to_message_id?: number
}

export interface ProviderConfig {
    label: string
    color: string
    bgColor: string
    icon: string
}

export const providerConfigs: Record<Provider, ProviderConfig> = {
    whatsapp: {
        label: 'WhatsApp',
        color: '#25D366',
        bgColor: 'bg-green-500',
        icon: 'MessageCircle'
    },
    messenger: {
        label: 'Messenger',
        color: '#0078FF',
        bgColor: 'bg-blue-500',
        icon: 'MessageCircle'
    },
    email: {
        label: 'Email',
        color: '#6B7280',
        bgColor: 'bg-gray-500',
        icon: 'Mail'
    }
}
