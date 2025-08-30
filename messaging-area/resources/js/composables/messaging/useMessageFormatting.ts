export function useMessageFormatting() {
    function getInitials(name: string): string {
        if (!name) return '?'
        return name.split(' ')
            .map(n => n.charAt(0))
            .join('')
            .toUpperCase()
            .substring(0, 2)
    }

    function formatTime(date: string): string {
        try {
            const time = new Date(date)
            return time.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })
        } catch (error) {
            return '--:--'
        }
    }

    function formatDate(date: string): string {
        try {
            const formattedDate = new Date(date)

            return formattedDate.toLocaleDateString('pt-BR', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            })
        } catch {
            return 'Data inválida'
        }
    }

    return {
        getInitials,
        formatTime,
        formatDate
    }
}
