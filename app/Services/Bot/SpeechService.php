<?php

namespace App\Services\Bot;

use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use App\Services\Bot\MorphBaseService;
use GuzzleHttp\Exception\RequestException;

/**
 * チャットボット用サービス
 * Class SpeechService
 * @package App\Services\Bot
 */
class SpeechService
{
    /**
     * @var client
     */
    private $client;
    
    //プロパティ
    /**
     * @var MorphService
     */
    private $morph_service;
    
    /**
     * SpeechService constructor.
     */
    public function __construct(MorphBaseService $morph_service) 
    {
        $this->client = new \GuzzleHttp\Client(['verify' => false]);
        $this->morph_service = $morph_service->getMorphService();
    }

    /**
     * Get message from file audio
     */
    public function getMessageAudio($blob_input)
    {
        $message = "音声の入力に失敗しました";
        $url_speech_azure = config('bot.speech.url_speech_azure');
        try {
            // Send request GuzzleHttp
            $audio_request = $this->client->request('POST', $url_speech_azure, [
                'headers' => [
                    'Content-Type' => 'audio/wav; codecs=audio/pcm; samplerate=' . config('bot.speech.sampling_rate'),
                    'Accept'  => 'application/json;text/xml',
                    'Ocp-Apim-Subscription-Key' => env('API_SPEECH_SUBSCRIPTION'),
                ],
                'body' => fopen($blob_input, "r"),
                'timeout' => config('bot.speech.timeout')
            ]);
            // Check reponse HTTPs
            if ($audio_request->getStatusCode() == 200) {
                // Get text audio
                $audio_content = json_decode($audio_request->getBody()->getContents())->DisplayText ?? '';
                // Filer Mecab
                $morph_messages = $this->morph_service->setMessage($audio_content)->execute()->getResult();
                $filter = $filter_del = [];
                foreach ($morph_messages as $idx => $morph_message) {
                    if ($morph_message->getPos() == config('bot.morph.default.keywords.filler')) {
                        $filter[] = $morph_message->getSurfaceForm() ?? null;
                        $filter_del[] = '';
                    }
                }
                $message = str_replace($filter, $filter_del, $audio_content);
                return array('status' => true, 'message' => $message);
            }
            return array('status' => false, 'message' => $message);
        } catch (RequestException $e) {
            return array('status' => false, 'message' => $message);
        }
    }
}