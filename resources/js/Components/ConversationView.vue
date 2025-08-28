<template>
	<div class="flex flex-col h-full">
		<div class="flex-1 overflow-y-auto scroll-smooth" ref="scrollEl" @scroll="onScroll">
			<DynamicScroller :items="virtualItems" :min-item-size="40" key-field="key" class="p-4">
				<template #default="{ item, index }">
					<DynamicScrollerItem :item="item" :active="true" :data-index="index">
						<div v-if="item.type === 'date'" class="text-center text-xs text-gray-500 my-2">{{ item.date }}</div>
						<div v-else class="flex">
							<div class="max-w-[70%] rounded-lg px-3 py-2 text-sm" :class="item.align === 'end' ? 'ml-auto bg-indigo-600 text-white' : 'mr-auto bg-gray-100 text-gray-900'">
								<div class="flex items-center gap-2">
									<span v-if="item.data.channel?.name" class="text-[10px] px-1.5 py-0.5 rounded bg-black/10 text-black/70">{{ item.data.channel.name }}</span>
									<div class="whitespace-pre-wrap break-words">{{ item.data.body }}</div>
								</div>
								<div class="mt-1 text-[10px] opacity-70 flex items-center gap-2">
									<span>{{ time(item.data.created_at) }}</span>
									<span v-if="item.data.direction === 'out'">
										<template v-if="item.data.status === 'sending'">⏳</template>
										<template v-else-if="item.data.status === 'failed'">❌</template>
										<template v-else>✔️</template>
									</span>
								</div>
							</div>
						</div>
					</DynamicScrollerItem>
				</template>
			</DynamicScroller>
			<div v-if="(messages?.data?.length ?? 0) === 0" class="text-center text-sm text-gray-500 py-6">Sem mensagens ainda</div>
		</div>
		<div v-if="hasMore" class="text-center p-2 border-t">
			<button @click="$emit('loadMore')" class="text-sm text-indigo-600 hover:underline">Carregar mais</button>
		</div>
	</div>
</template>

<script setup lang="ts">
import { computed, ref, onMounted, nextTick, watch } from 'vue'
import { DynamicScroller, DynamicScrollerItem } from 'vue-virtual-scroller'

const props = defineProps<{
	messages: any
	hasMore: boolean
}>()

defineEmits<{ (e: 'loadMore'): void }>()

const scrollEl = ref<HTMLDivElement | null>(null)

function dateKey(s: string): string {
	const d = new Date(s)
	return d.toLocaleDateString()
}

function time(s: string): string {
	const d = new Date(s)
	return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

const virtualItems = computed(() => {
	const items = [...(props.messages?.data ?? [])].reverse()
	const out: Array<
		{ type: 'date'; key: string; date: string } |
		{ type: 'msg'; key: string; align: 'start' | 'end'; data: any }
	> = []
	let currentDate = ''
	for (const m of items) {
		const key = dateKey(m.created_at)
		if (key !== currentDate) {
			out.push({ type: 'date', key: `date-${key}`, date: key })
			currentDate = key
		}
		out.push({ type: 'msg', key: `msg-${m.id}`, align: m.direction === 'out' ? 'end' : 'start', data: m })
	}
	return out
})

function onScroll() {
	const el = scrollEl.value
	if (!el) return
	if (el.scrollTop === 0) {
		queueMicrotask(() => {
			// @ts-expect-error - emit implícito
			$emit('loadMore')
		})
	}
}

function scrollToBottom() {
	nextTick(() => {
		const el = scrollEl.value
		if (!el) return
		el.scrollTo({ top: el.scrollHeight, behavior: 'smooth' })
	})
}

onMounted(() => {
	setTimeout(() => {
		scrollToBottom()
	}, 100)
})

watch(virtualItems, () => {
	scrollToBottom()
})
</script>

<style>
@import 'vue-virtual-scroller/dist/vue-virtual-scroller.css';
</style>
