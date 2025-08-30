<?php

namespace App\Modules\Messaging\Extractors;

use App\Modules\Messaging\Services\ChannelService;
use Illuminate\Support\Collection;

readonly class DashboardDataExtractor
{
    public function __construct(
        private readonly ChannelService $channelService
    ) {
    }

    public function extract(
        Collection $conversations,
        ?array $conversationData,
        string $selectedChannel
    ): array {
        $data = [
            'conversations' => $conversations->toArray(),
            'channels' => $this->channelService->getChannelInfo(),
            'selectedChannel' => $selectedChannel,
            'selectedConversation' => null,
            'messages' => null,
        ];

        if ($conversationData) {
            $data['selectedConversation'] = $conversationData['conversation']->toArray();
            $data['messages'] = $conversationData['messages'];
        }

        return $data;
    }
}
