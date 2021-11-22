<?php

namespace App\Console\Commands\Bot;

use App\Services\Admin\LearningService;
use App\Services\Bot\BotTruthService;
use Illuminate\Console\Command;
use Util;

/**
 * 真理表用コマンド
 * Class TruthCommand
 * @package App\Console\Commands\Bot
 */
class TruthCommand extends Command
{
    /**
     * モード：同期
     */
    const MODE_SYNC = 'sync';
    /**
     * モード：ストップワード
     */
    const MODE_STOP = 'stop';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:truth {mode=sync} {param?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var BotTruthService
     */
    private $truth_service;
    /**
     * @var LearningService
     */
    private $learning_service;

    /**
     * TruthCommand constructor.
     * @param BotTruthService $truth_service
     * @param LearningService $learning_service
     */
    public function __construct(BotTruthService $truth_service, LearningService $learning_service)
    {
        parent::__construct();
        $this->truth_service = $truth_service;
        $this->learning_service = $learning_service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        switch ($this->argument('mode')) {
            case self::MODE_SYNC:
                $this->info('Synchronize learning data with truth table.');
                $this->sync($this->argument('param'));
                $this->info('All done.');
                break;
            case self::MODE_STOP:
                $word = $this->argument('param');
                if (empty($word)) {
                    $this->error('Stop word is empty.');
                    break;
                }
                $this->setStopWord($word);
                break;
        }
    }

    /**
     * 真理表同期
     * @param integer $id tbl_learning.id
     */
    private function sync($id = null)
    {
        $data = empty($id) ? $this->learning_service->getRepository()->getAll() : [$this->learning_service->getRepository()->getOneById($id)];
        $this->info('Create truth table.');
        foreach ($data as $idx => $row) {
            $this->output->write("[API_ID:{$row['api_id']}] processing... ");
            if ($row['auto_key_phrase_disabled'] == config('const.common.disabled.yes.id')) {
                //自動セット無効
                $this->line('SKIP');
            } else {
                //有効
                $this->learning_service->saveTruthTable($row['api_id'], $row['question']);
                $this->info('DONE');
            }


            unset($data[$idx]);
        }
        //真理表使用時　ストップワード自動セット
        $this->info('Auto set stop word.');
        $words_count = $this->truth_service->getDbService()->getWordsCount();
        $learning_count = $this->learning_service->getRepository()->getDbResult()->getCount();
        $count_border = floor($learning_count * (config('bot.truth.stop_word_rate') / 100));
        foreach ($words_count as $word => $count) {
            if ($count < $count_border) break;
            if ($this->truth_service->getDbService()->saveStopWord($word)) {
                $this->line("Set stop word [{$word}]");
            }
        }
    }

    /**
     * ストップワード設定
     * @param string $word 対象ワード
     */
    private function setStopWord($word)
    {
        if ($this->truth_service->getDbService()->saveStopWord($word)) {
            $this->line("Set stop word [{$word}]");
        }
    }

}
