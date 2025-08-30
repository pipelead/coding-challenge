import type { SendMessageData } from '@/types/messaging.ts'

export async function sendMessageAPI(
    conversationId: number,
    payload: SendMessageData
): Promise<{ message_id: number }> {
    const csrfToken = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement)?.content || ''

    const response = await fetch(`/conversations/${conversationId}/messages`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })

    const data = await response.json()

    if (!response.ok) {
        throw new Error(data.error || 'Erro ao enviar mensagem')
    }

    return data
}
