<script setup lang="ts">
import ConversationItem from '@/components/messaging/ConversationItem.vue'
import type { Conversation } from '@/types/messaging.ts'

interface Props {
    conversations: Conversation[]
    selectedConversationId?: number
    loading?: boolean
}

interface Emits {
    (event: 'select', conversationId: number): void
    (event: 'mark-read', conversationId: number): void
}

const props = withDefaults(defineProps<Props>(), {
    loading: false
})

const emit = defineEmits<Emits>()

function handleSelect(conversationId: number) {
    emit('select', conversationId)
}

function handleMarkRead(conversationId: number) {
    emit('mark-read', conversationId)
}
</script>

<template>
    <div class="flex-1 overflow-y-auto">
        <div v-if="loading" class="p-4">
            <div class="animate-pulse space-y-3">
                <div v-for="i in 5" :key="i" class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-200 dark:bg-gray-600 rounded-full"></div>
                    <div class="flex-1">
                        <div class="h-4 bg-gray-200 dark:bg-gray-600 rounded w-3/4 mb-2"></div>
                        <div class="h-3 bg-gray-200 dark:bg-gray-600 rounded w-1/2"></div>
                    </div>
                </div>
            </div>
        </div>

        <div v-else-if="!conversations || conversations.length === 0" class="p-4 text-center text-gray-500">
            <p>Nenhuma conversa encontrada</p>
        </div>

        <div v-else class="p-2">
            <ConversationItem
                v-for="conversation in conversations"
                :key="conversation.id"
                :conversation="conversation"
                :selected="selectedConversationId === conversation.id"
                @select="handleSelect"
                @mark-read="handleMarkRead"
            />
        </div>
    </div>
</template>
