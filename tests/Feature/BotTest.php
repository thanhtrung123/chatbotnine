<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class BotTest extends TestCase
{

    /**
     * フィードバック　はい
     */
    public function testFeedback1()
    {
        $res = $this->post('/api/bot', [
            'msg' => 'はい',
            'status' => 'select_feedback',
        ]);

        $this->assertJsonStringEqualsJsonString(json_encode([
            'feedback' => true
            ]), $res->content());
    }

    /**
     * フィードバック　いいえ
     */
    public function testFeedback2()
    {
        $res = $this->post('/api/bot', [
            'msg' => 'いいえ',
            'status' => 'select_feedback',
        ]);

        $this->assertJsonStringEqualsJsonString(json_encode([
            'feedback' => false
            ]), $res->content());
    }

    /**
     * ?
     */
    public function testHearback()
    {
        $res = $this->post('/api/bot', [
            'msg' => 'いいえ',
            'status' => 'select',
        ]);
        $res->assertJsonStructure([
            'num' => 1,
            'qa',
            'hear_back_flg'
        ]);
    }
    
}