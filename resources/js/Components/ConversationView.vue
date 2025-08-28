<template>
	<div class="flex flex-col h-full">
		<div class="flex-1 overflow-y-auto" ref="scrollEl" @scroll="onScroll">
			<DynamicScroller :items="virtualItems" :min-item-size="40" class="p-4">
				<template #default="{ item, index }">
					<DynamicScrollerItem :item="item" :active="true" :data-index="index">
						<div v-if="item.type === 'date'" class="text-center text-xs text-gray-500 my-2">{{ item.date }}</div>
						<div v-else class="max-w-[70%] rounded-lg px-3 py-2 text-sm" :class="item.data.direction === 'out' ? 'self-end bg-indigo-600 text-white' : 'self-start bg-gray-100 text-gray-900'">
							<div class="whitespace-pre-wrap break-words">{{ item.data.body }}</div>
							<div class="mt-1 text-[10px] opacity-70 flex items-center gap-2">
								<span>{{ time(item.data.created_at) }}</span>
								<span v-if="item.data.direction === 'out'">
									<template v-if="item.data.status === 'sending'">⏳</template>
									<template v-else-if="item.data.status === 'failed'">❌</template>
									<template v-else>✔️</template>
								</span>
							</div>
						</div>
					</DynamicScrollerItem>
				</template>
			</DynamicScroller>
		</div>
		<div v-if="hasMore" class="text-center p-2 border-t">
			<button @click="$emit('loadMore')" class="text-sm text-indigo-600 hover:underline">Carregar mais</button>
		</div>
	</div>
</template>

<script setup lang="ts">
import { computed, ref } from 'vue'
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
	const items = props.messages?.data ?? []
	const out: Array<{ type: 'date'; date: string } | { type: 'msg'; data: any }> = []
	let currentDate = ''
	for (const m of items) {
		const key = dateKey(m.created_at)
		if (key !== currentDate) {
			out.push({ type: 'date', date: key })
			currentDate = key
		}
		out.push({ type: 'msg', data: m })
	}
	return out
})

function onScroll() {
	const el = scrollEl.value
	if (!el) return
	if (el.scrollTop === 0) {
		// topo: solicitar mais antigas
		// evita tempestade de eventos usando setTimeout microtask
		queueMicrotask(() => {
			// emite evento de loadMore para o pai
			// pai faz router.visit(prev_page_url)
			// e mantém preserveState/Scroll
			// @ts-ignore - emit implícito
			$emit('loadMore')
		})
	}
}
</script>

<style>
@import 'vue-virtual-scroller/dist/vue-virtual-scroller.css';
</style>
