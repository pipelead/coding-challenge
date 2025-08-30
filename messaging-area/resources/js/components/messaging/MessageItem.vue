<script setup lang="ts">
import { Check, CheckCheck, Clock, AlertCircle, Reply } from 'lucide-vue-next'
import { useMessageFormatting } from '@/composables/messaging/useMessageFormatting.ts'
import type { Message, Contact } from '@/types/messaging.ts'

interface Props {
    message: Message
    currentUser: Contact
    senderName: string
}

interface Emits {
    (event: 'reply', message: Message): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const { getInitials, formatTime } = useMessageFormatting()

function getStatusIcon(status: string) {
    const iconMap: Record<string, any> = {
        pending: Clock,
        sent: Check,
        delivered: CheckCheck,
        read: CheckCheck,
        failed: AlertCircle
    }
    return iconMap[status] || Clock
}

function getStatusIconClass(status: string): string {
    const classMap: Record<string, string> = {
        pending: 'w-3 h-3 text-yellow-500',
        sent: 'w-3 h-3 text-gray-400',
        delivered: 'w-3 h-3 text-gray-400',
        read: 'w-3 h-3 text-blue-500',
        failed: 'w-3 h-3 text-red-500'
    }
    return classMap[status] || 'w-3 h-3 text-gray-400'
}

function handleReply() {
    emit('reply', props.message)
}
</script>

<template>
    <div
        v-if="message.provider === 'email'"
        class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-md transition-shadow mb-3"
    >
        <div class="p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
            <span class="text-xs font-medium text-gray-600 dark:text-gray-300">
              {{ message.isFromUser ? getInitials(senderName) : getInitials(currentUser.name) }}
            </span>
                    </div>
                    <div>
                        <div class="font-medium text-sm text-gray-900 dark:text-white">
                            {{ message.isFromUser ? senderName : currentUser.name }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ formatTime(message.created_at) }}
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <component
                        v-if="!message.isFromUser"
                        :is="getStatusIcon(message.status)"
                        :class="getStatusIconClass(message.status)"
                    />
                    <button
                        @click="handleReply"
                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded transition-colors"
                        title="Responder"
                    >
                        <Reply class="w-4 h-4" />
                    </button>
                </div>
            </div>

            <div
                v-if="message.subject"
                class="font-semibold mb-4 text-base text-gray-900 dark:text-white border-b border-gray-100 dark:border-gray-700 pb-2"
            >
                {{ message.subject }}
            </div>

            <div class="prose prose-sm max-w-none dark:prose-invert">
                <p class="whitespace-pre-wrap text-gray-700 dark:text-gray-300 leading-relaxed">
                    {{ message.content }}
                </p>
            </div>
        </div>
    </div>

    <div
        v-else
        :class="['flex mb-3', message.isFromUser ? 'justify-start' : 'justify-end']"
    >
        <div
            :class="[
        'max-w-[70%] rounded-2xl px-4 py-3 shadow-sm relative',
        message.isFromUser
          ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700'
          : 'bg-gray-900 dark:bg-gray-700 text-white'
      ]"
        >
            <div
                v-if="!message.isFromUser && message.status === 'pending'"
                class="absolute -top-2 -right-2 w-6 h-6 bg-yellow-500 rounded-full flex items-center justify-center"
            >
                <div class="animate-spin rounded-full h-3 w-3 border border-white border-t-transparent"></div>
            </div>

            <p class="text-sm leading-relaxed">{{ message.content }}</p>
            <div class="flex items-center justify-between mt-2 gap-2">
                <span class="text-xs opacity-70">{{ formatTime(message.created_at) }}</span>
                <div v-if="!message.isFromUser" class="flex items-center">
                    <component
                        :is="getStatusIcon(message.status)"
                        :class="getStatusIconClass(message.status)"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
