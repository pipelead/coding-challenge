import { MessageCircle, Mail } from 'lucide-vue-next'
import { providerConfigs, type Provider } from '@/types/messaging.ts'

export function getProviderConfig(provider: Provider) {
    return providerConfigs[provider] || providerConfigs.whatsapp
}

export function getProviderIcon(provider: Provider) {
    const config = getProviderConfig(provider)
    return config.icon === 'Mail' ? Mail : MessageCircle
}

export function getProviderLabel(provider: Provider) {
    return getProviderConfig(provider).label
}

export function getProviderBgColor(provider: Provider): string {
    return getProviderConfig(provider).bgColor
}
