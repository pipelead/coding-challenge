import { defineStore } from 'pinia'

export const useConversationsStore = defineStore('conversations', {
	state: () => ({
		contacts: null as any,
		activeContactId: null as number | null,
		messages: null as any,
	}),
	actions: {
		setContacts(payload: any) {
			this.contacts = payload
		},
		setActiveContact(id: number | null) {
			this.activeContactId = id
		},
		setMessages(payload: any) {
			this.messages = payload
		},
	},
})
