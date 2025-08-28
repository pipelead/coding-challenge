<template>
    <div class="h-screen grid grid-cols-12">
        <aside class="col-span-4 border-r overflow-y-auto">
            <ContactList :contacts="contacts" :active-id="null" />
        </aside>
        <main class="col-span-8 flex items-center justify-center text-gray-500">
            Selecione um contato à esquerda
        </main>
    </div>
</template>

<script setup lang="ts">
// @ts-expect-error - shim .vue
import ContactList from '@/Components/ContactList.vue'
import { onMounted } from 'vue'
import { useConversationsStore } from '@/stores/conversations'

const props = defineProps<{ contacts: any }>()

const store = useConversationsStore()

onMounted(() => {
	store.setContacts(props.contacts)
	store.setActiveContact(null)
})
</script>
