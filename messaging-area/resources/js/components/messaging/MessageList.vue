<template>
    <div class="flex flex-col h-full">
        <div class="flex-1 overflow-y-auto scroll-smooth" ref="scrollEl" @scroll="onScroll">
            <DynamicScroller :items="virtualItems" :min-item-size="40" key-field="key" class="p-4">
                <template #default="{ item, index }">
                    <DynamicScrollerItem :item="item" :active="true" :data-index="index">
                        <div v-if="item.type === 'date'" class="text-center text-xs text-gray-500 my-2">{{ item.date }}</div>
                        <div v-else>
                            <MessageItem
                                :message="item.message"
                                :current-user="currentUser"
                                :sender-name="senderName"
                                @reply="handleReply"
                            />
                        </div>
                    </DynamicScrollerItem>
                </template>
            </DynamicScroller>
            <div v-if="(!messages || messages.length === 0)" class="text-center text-sm text-gray-500 py-6">Sem mensagens ainda</div>
        </div>
        <div v-if="pagination?.next_page_url" class="text-center p-2 border-t">
            <button @click="$emit('load-more')" class="text-sm text-indigo-600 hover:underline">Carregar mais</button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, nextTick, watch } from 'vue'
import { DynamicScroller, DynamicScrollerItem } from 'vue-virtual-scroller'
import MessageItem from './MessageItem.vue'
import { useMessageFormatting } from '@/composables/messaging/useMessageFormatting.ts'
import type { Contact, Message } from '@/types/messaging.ts'

interface Props {
    messages: Message[]
    currentUser: Contact
    senderName: string
    pagination: any
}

interface Emits {
    (event: 'reply', message: Message): void
    (event: 'load-more'): void
}

const props = defineProps<Props>()
const emit = defineEmits<Emits>()

const { formatDate } = useMessageFormatting()

const scrollEl = ref<HTMLDivElement | null>(null)
const isLoadingMore = ref(false)
const previousScrollHeight = ref(0)


function dateKey(timestamp: string): string {
    return formatDate(timestamp)
}

const virtualItems = computed(() => {
    const items = props.messages
    const out: Array<
        { type: 'date'; key: string; date: string } |
        { type: 'message'; key: string; message: Message }
    > = []
    let currentDate = ''

    for (const message of items) {
        const key = dateKey(message.created_at)
        if (key !== currentDate) {
            out.push({ type: 'date', key: `date-${key}`, date: key })
            currentDate = key
        }
        out.push({ type: 'message', key: `message-${message.id}`, message })
    }
    return out
})

function onScroll() {
    const el = scrollEl.value
    if (!el) return
    if (el.scrollTop === 0 && !isLoadingMore.value) {
        isLoadingMore.value = true
        previousScrollHeight.value = el.scrollHeight
        emit('load-more')
    }
}

function scrollToBottom() {
    nextTick(() => {
        const el = scrollEl.value
        if (!el) return
        el.scrollTo({ top: el.scrollHeight, behavior: 'smooth' })
    })
}

function handleReply(message: Message) {
    emit('reply', message)
}

onMounted(() => {
    setTimeout(() => {
        scrollToBottom()
    }, 100)
})

watch(() => virtualItems.value, (newItems, oldItems) => {
    nextTick(() => {
        const el = scrollEl.value
        if (!el) return

        if (isLoadingMore.value && oldItems && newItems.length > oldItems.length) {
            const newScrollHeight = el.scrollHeight
            const heightDifference = newScrollHeight - previousScrollHeight.value
            el.scrollTop = heightDifference
            isLoadingMore.value = false
        } else if (!oldItems) {
            scrollToBottom()
        }
    })
})

defineExpose({
    scrollToBottom
})
</script>

<style>
@import 'vue-virtual-scroller/dist/vue-virtual-scroller.css';
</style>
