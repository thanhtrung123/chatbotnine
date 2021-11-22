<?php

namespace App\Http\Controllers\Bot;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Bot\SpeechService;

/**
 * チャットボットAPIコントローラー
 * Class ApiController
 * @package App\Http\Controllers\Bot
 */
class SpeechController extends Controller
{
     /**
     * @var SpeechService
     */
    private $speech_service;
    
    /**
     * ApiController constructor.
     * @param BotService $bot_service
     * @param LearningService $learning_service
     */
    public function __construct(SpeechService $speech_service)
    {
        $this->speech_service = $speech_service;
    }

    /**
     * Get Speech to text
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function uploadSpeech(Request $request)
    {
        // Check API Azure
        if (!empty(env('API_SPEECH_ENABLE', false))) {
            // Get file audio
            $blob_input = $request->file('audio-blob');
            // Get content text
            $result_content = $this->speech_service->getMessageAudio($blob_input);
            if ($result_content['status'] == true) {
                return response(['status' => true, 'message' => $result_content['message']]);
            }
            return response(['status' => false, 'message' => $result_content['message']]);
        }
        return response(['status' => false, 'message' => '音声の入力に失敗しました']);
    }
}