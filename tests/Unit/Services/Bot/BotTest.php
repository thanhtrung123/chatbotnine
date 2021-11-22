<?php

namespace Tests\Unit\Services\Bot;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use App\Services\Bot\BotService;

class BotTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBot1()
    {
        $mock = Mockery::mock(BotService::class);
        $mock->exec();
    }
}
