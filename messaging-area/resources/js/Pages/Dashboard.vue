<template>
    <div class="flex h-screen bg-gray-50 dark:bg-gray-900">
        <aside class="w-80 border-r border-gray-200 dark:border-gray-700 flex flex-col bg-white dark:bg-gray-800">
            <ChannelFilter
                :channels="page.props.channels || []"
                :selected-channel="page.props.selectedChannel || 'whatsapp'"
                @change="handleChannelChange"
            />

            <ConversationList
                :conversations="page.props.conversations || []"
                :selected-conversation-id="selectedConversation?.id"
                :loading="isLoading"
                @select="handleConversationSelect"
            />
        </aside>

        <main class="flex-1 flex flex-col bg-white dark:bg-gray-800">
            <template v-if="selectedConversation">
                <header class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center overflow-hidden">
                                <img
                                    v-if="selectedConversation.contact?.avatar_url"
                                    :src="selectedConversation.contact.avatar_url"
                                    :alt="selectedConversation.contact.name"
                                    class="w-10 h-10 rounded-full object-cover"
                                />
                                <span v-else class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                    {{ getInitials(selectedConversation.contact?.name || selectedConversation.name) }}
                                </span>
                            </div>

                            <div>
                                <div class="flex items-center gap-2">
                                    <h2 class="font-semibold text-gray-900 dark:text-white">
                                        {{ selectedConversation.contact?.name || selectedConversation.name }}
                                    </h2>
                                </div>
                                <div class="flex items-center gap-2">
                                    <component :is="getProviderIcon(selectedConversation.provider)" class="w-4 h-4 text-gray-500" />
                                    <span class="text-sm text-gray-500 dark:text-gray-400 capitalize">
                                        {{ getChannelLabel(selectedConversation.provider) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <button
                                v-if="selectedConversation.provider === 'email'"
                                @click="messageInputRef?.startComposing()"
                                :disabled="isSending"
                                class="px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-md hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors flex items-center gap-2 font-medium shadow-sm disabled:opacity-50"
                            >
                                <Mail class="w-4 h-4" />
                                Novo Email
                            </button>
                        </div>
                    </div>
                </header>

                <MessageList
                    ref="messageListRef"
                    :messages="messages"
                    :current-user="page.props.currentUser || {}"
                    :sender-name="selectedConversation?.contact?.name || selectedConversation?.name || ''"
                    :pagination="page.props.messages"
                    @reply="(message: Message) => replyingToMessage = message"
                    @load-more="loadMoreMessages"
                />

                <MessageInput
                    ref="messageInputRef"
                    :provider="selectedConversation.provider"
                    :sending="isSending"
                    :replying-to="replyingToMessage"
                    @send="handleSendMessage"
                    @cancel="() => replyingToMessage = null"
                    @compose="(composing: boolean) => { if (!composing) replyingToMessage = null }"
                />
            </template>

            <template v-else>
                <div class="flex-1 flex items-center justify-center bg-gray-50 dark:bg-gray-900">
                    <div class="text-center">
                        <div class="w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-6">
                            <MessageCircle class="w-10 h-10 text-gray-400" />
                        </div>
                        <h3 class="text-xl font-semibold mb-2 text-gray-900 dark:text-white">
                            Selecione uma conversa
                        </h3>
                        <p class="text-gray-500 dark:text-gray-400 max-w-sm">
                            Escolha um contato para visualizar as mensagens e iniciar uma conversa
                        </p>
                    </div>
                </div>
            </template>
        </main>
    </div>
</template>

<script setup lang="ts">
import { ref, computed, nextTick } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { MessageCircle, Mail } from 'lucide-vue-next'

import ChannelFilter from '@/components/messaging/ChannelFilter.vue'
import ConversationList from '@/components/messaging/ConversationList.vue'
import MessageList from '@/components/messaging/MessageList.vue'
import MessageInput from '@/components/messaging/MessageInput.vue'

import { useMessageFormatting } from '@/composables/messaging/useMessageFormatting'
import { useMessageSending } from '@/composables/messaging/useMessageSending'
import { useMessagePolling } from '@/composables/messaging/useMessagePolling'
import { useMessagePagination } from '@/composables/messaging/useMessagePagination'
import { useConversationSelection } from '@/composables/messaging/useConversationSelection'
import { useMessagesStore } from '@/stores/messages'

import { getProviderIcon, getProviderLabel } from '@/utils/messaging/provider'

import type {
    Message,
    Conversation,
    Provider,
    SendMessageData
} from '@/types/messaging'

const page = usePage()

const messageListRef = ref<InstanceType<typeof MessageList>>()
const messageInputRef = ref<InstanceType<typeof MessageInput>>()
const replyingToMessage = ref<Message | null>(null)
const selectedConversation = computed<Conversation | null>(() => page.props.selectedConversation || null)

const { getInitials } = useMessageFormatting()
const { isSending, sendMessage } = useMessageSending()
const messagesStore = useMessagesStore()

const { handleChannelChange, handleConversationSelect, isLoading } = useConversationSelection(messageListRef)
const { loadMoreMessages } = useMessagePagination(selectedConversation)
useMessagePolling(selectedConversation)


const messages = computed<Message[]>(() => messagesStore.conversationMessages)

function getChannelLabel(provider: Provider): string {
    return getProviderLabel(provider)
}

async function handleSendMessage(messageData: SendMessageData): Promise<void> {
    if (!selectedConversation.value) return

    await sendMessage(selectedConversation.value, messageData, replyingToMessage)

    await nextTick(() => {
        messageListRef.value?.scrollToBottom()
    })
}
</script>
