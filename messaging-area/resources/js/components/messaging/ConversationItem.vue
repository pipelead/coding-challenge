<script setup lang="ts">
import { computed } from 'vue'
import { useMessageFormatting } from '@/composables/messaging/useMessageFormatting.ts'
import { getProviderIcon, getProviderBgColor } from '@/utils/messaging/provider.ts'
import type { Conversation } from '@/types/messaging.ts'

interface Props {
    conversation: Conversation
    selected: boolean
}

interface Emits {
    (event: 'select', conversationId: number): void
    (event: 'mark-read', conversationId: number): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const { getInitials, formatTime } = useMessageFormatting()

const unreadCount = computed(() => {
    return props.conversation.unreadMessagesCount ?? props.conversation.unreadCount
})

function handleClick(): void {
    emit('select', props.conversation.id)

    if (unreadCount.value > 0) {
        emit('mark-read', props.conversation.id)
    }
}
</script>

<template>
    <div
        :class="[
      'p-3 mb-2 cursor-pointer transition-colors hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm',
      selected
        ? 'bg-gray-50 dark:bg-gray-700 border-gray-300 dark:border-gray-600'
        : 'bg-white dark:bg-gray-800'
    ]"
        @click="handleClick"
    >
        <div class="flex items-start gap-3">
            <div class="relative">
                <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center overflow-hidden">
                    <img
                        v-if="conversation.avatar"
                        :src="conversation.avatar"
                        :alt="conversation.name"
                        class="w-10 h-10 rounded-full object-cover"
                    />
                    <span v-else class="text-sm font-medium text-gray-600 dark:text-gray-300">
            {{ getInitials(conversation.name) }}
          </span>
                </div>
                <div
                    :class="[
                        'absolute -bottom-0.5 -right-0.5 w-4 h-4 rounded-full border-2 border-white dark:border-gray-800 flex items-center justify-center',
                        getProviderBgColor(conversation.provider)
                      ]"
                >
                    <component
                        :is="getProviderIcon(conversation.provider)"
                        class="w-2.5 h-2.5 text-white"
                    />
                </div>
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-1">
                    <h3 class="font-semibold text-sm truncate text-gray-900 dark:text-white">
                        {{ conversation.name }}
                    </h3>
                    <div class="flex items-center gap-1">
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                          {{ formatTime(conversation.lastMessageTime) }}
                        </span>
                        <span
                            v-if="unreadCount > 0"
                            class="bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full font-medium min-w-[1.25rem] flex items-center justify-center"
                        >
                          {{ unreadCount }}
                        </span>
                    </div>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-300 truncate">
                    {{ conversation.lastMessage || 'Sem mensagens' }}
                </p>
            </div>
        </div>
    </div>
</template>
