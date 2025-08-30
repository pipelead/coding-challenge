<?php

namespace App\Modules\Messaging\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Messaging\Enums\ConversationChannelEnum;
use App\Modules\Messaging\Models\Conversation;
use App\Modules\Messaging\Requests\{IndexRequest, SendMessageRequest};
use App\Modules\Messaging\Services\ConversationService;
use App\Modules\Messaging\Extractors\DashboardDataExtractor;
use Exception, Throwable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Inertia\{Inertia, Response};


class MessagingController extends Controller
{
    public function __construct(
        private readonly ConversationService $conversationService,
        private readonly DashboardDataExtractor $dashboardExtractor
    )
    {
    }

    /**
     * @throws Throwable
     */
    public function index(IndexRequest $request): Response
    {
        $selectedChannel = $request->get('channel', ConversationChannelEnum::WhatsApp->value);
        $conversationId = $request->get('conversation_id');

        try {
            $conversations = $this->conversationService->getConversationsForChannel($selectedChannel);
            $conversationData = $this->conversationService->getConversationData($conversationId);

            $dashboardData = $this->dashboardExtractor->extract($conversations, $conversationData, $selectedChannel);
        } catch (Exception|Throwable $e) {
            Log::error('Error loading dashboard', [
                'error' => $e->getMessage(),
            ]);

            $dashboardData = [
                'conversations' => [],
                'channels' => [],
                'selectedChannel' => $selectedChannel,
                'selectedConversation' => null,
                'messages' => null,
            ];
        }

        return Inertia::render('Dashboard', $dashboardData);
    }

    public function sendMessage(SendMessageRequest $request, Conversation $conversation): JsonResponse
    {
        try {
            $message = $this->conversationService->createMessage($conversation->id, $request->validated());

            return response()->json([
                'success' => true,
                'message_id' => $message->id
            ]);

        } catch (Exception|Throwable $e) {
            Log::error('Failed to send message', [
                'conversation_id' => $conversation->id,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Ocorreu um erro ao enviar sua mensagem, tente novamente!'
            ], 500);
        }
    }
}
