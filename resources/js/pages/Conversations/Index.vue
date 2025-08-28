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
// @ts-expect-error - shim de .vue cobre o tipo
import ContactList from '@/Components/ContactList.vue'

defineProps<{ contacts: any }>()
</script>
