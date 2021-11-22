<?php

namespace App\Console\Commands\Bot;

use App\Services\Admin\ResponseAggregateService;
use App\Services\Bot\ResponseInfoService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Constant;
use DB;

/**
 * 集計コマンド
 * Class AggregateCommand
 * @package App\Console\Commands\Bot
 */
class AggregateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:aggregate {mode=1} {date?}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'chat bot response information aggregate command.';
    /**
     * モード：日
     */
    const MODE_DAY = 1;
    /**
     * モード：全て
     */
    const MODE_ALL = 2;
    /**
     * @var ResponseInfoService
     */
    private $info_service;
    /**
     * @var ResponseAggregateService
     */
    private $aggregate_service;
    /**
     * @var integer 処理モード
     */
    private $mode;
    /**
     * @var string 処理対象日付
     */
    private $date;

    /**
     * AggregateCommand constructor.
     * @param ResponseInfoService $info_service
     * @param ResponseAggregateService $aggregate_service
     */
    public function __construct(ResponseInfoService $info_service, ResponseAggregateService $aggregate_service)
    {
        parent::__construct();
        $this->info_service = $info_service;
        $this->aggregate_service = $aggregate_service;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function handle()
    {
        $this->mode = $this->argument('mode');
        $this->date = $this->argument('date') ?? Carbon::yesterday()->format('Ymd');
        if (Carbon::hasFormat($this->date, 'Ymd') === FALSE) {
            $this->error("Invalid Date Format [yyyymmdd] : [{$this->date}]");
            return false;
        }
        //
        $this->info('Response Information Aggregate Start');
        //
        switch ($this->mode) {
            case self::MODE_ALL:
                //全て
                $db_result = $this->info_service->getRepository()->filterDate()->getDbResult();
                foreach ($db_result->getGenerator() as $row) {
                    $this->aggregate($row['date']);
                }
                break;
            case self::MODE_DAY:
                //日指定
                $this->aggregate($this->date);
                break;
        }
    }

    /**
     * 集計実行
     * @param string $date 日付の文字列(Ymd)
     * @return bool
     * @throws \Exception
     */
    private function aggregate($date)
    {
        $this->output->write("[{$date}] Aggregating... ");
        DB::beginTransaction();
        try {
            //削除
            $this->aggregate_service->getRepository()->setParams(['aggregate_date' => $date])->filterByParams()->deleteByQuery();
            //基準・種類でループ
            $bases = Constant::getConstArray('aggregate.base', true);
            $types = Constant::getConstArray('aggregate.type', true);
            foreach ($bases as $base) {
                foreach ($types as $type) {
                    $aggregate = $this->info_service->getAggregate($base, $type, $date);
                    foreach ($aggregate as $row) {
                        $this->aggregate_service->getRepository()->create($row);
                    }
                }
            }
            $this->info('DONE');
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('ERROR');
            $this->error($e->getMessage());
            $this->aggregate_service->getRepository()->create([
                'aggregate_date' => $date,
                'aggregate_type' => config('const.aggregate.type.error.id'),
                'total_value' => 0,
            ]);
            return false;
        }

    }
}