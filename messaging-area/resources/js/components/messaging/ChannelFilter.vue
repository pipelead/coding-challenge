<script setup lang="ts">
import type { Channel, Provider } from '@/types/messaging.ts'
import { getProviderIcon, getProviderConfig } from '@/utils/messaging/provider.ts'

interface Props {
    channels: Channel[]
    selectedChannel: string
}

interface Emits {
    (event: 'change', channel: string): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()


function handleChannelChange(channel: string) {
    emit('change', channel)
}
</script>

<template>
    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
        <h1 class="text-lg font-semibold mb-3 text-gray-900 dark:text-white">
            Atendimento
        </h1>
        <div class="flex gap-2 flex-wrap">
            <button
                v-for="channel in channels"
                :key="channel.value"
                :class="[
          'px-3 py-1.5 text-sm rounded-md transition-colors font-medium flex items-center gap-1.5',
          selectedChannel === channel.value
            ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900 shadow-sm'
            : 'border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700'
        ]"
                @click="handleChannelChange(channel.value)"
            >
                <component
                    :is="getProviderIcon(channel.value)"
                    :style="{ color: getProviderConfig(channel.value).color }"
                    class="w-4 h-4"
                />
                {{ getProviderConfig(channel.value).label }}
            </button>
        </div>
    </div>
</template>
