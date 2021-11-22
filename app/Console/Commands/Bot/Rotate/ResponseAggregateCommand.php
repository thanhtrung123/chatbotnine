<?php

namespace App\Console\Commands\Bot\Rotate;

use App\Exports\CsvExport;
use App\Repositories\DbResult;
use App\Repositories\RepositoryInterface;
use App\Services\File\ZipService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Services\Bot\ResponseInfoService;
use App\Services\Admin\ResponseAggregateService;
use DB;

/**
 * 応答ログ・集計結果ログローテート
 * Class ResponseAggregateCommand
 * @package App\Console\Commands\Bot\Rotate
 */
class ResponseAggregateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:rotate:response';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ResponseInfo And ResponseAggregate Data Rotate.';
    private $info_service;
    private $aggregate_service;
    private $today;
    private $zip_service;

    /**
     * ResponseAggregateCommand constructor.
     * @param ResponseInfoService $info_service
     * @param ResponseAggregateService $aggregate_service
     * @param ZipService $zip_service
     */
    public function __construct(ResponseInfoService $info_service, ResponseAggregateService $aggregate_service, ZipService $zip_service)
    {
        $this->info_service = $info_service;
        $this->aggregate_service = $aggregate_service;
        $this->zip_service = $zip_service;
        $this->today = Carbon::today();
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $this->info(__('Response Info & Aggregate Data Rotate Start.'));
        DB::beginTransaction();
        try {
            $this->rotateExec($this->info_service->getRepository(), config('bot.aggregate.info_max_month'), 'info');
            $this->rotateExec($this->aggregate_service->getRepository(), config('bot.aggregate.aggregate_max_month'), 'aggregate');
            $this->zipCompress();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage());
        }
    }

    /**
     * @param RepositoryInterface $repo
     * @param int $month
     * @param string $prefix
     * @return bool
     */
    private function rotateExec($repo, $month, $prefix)
    {
        $this->output->write(__("[{$prefix}] Data Rotate... "));
        $date = Carbon::today()->subMonth($month)->day(1);
        /* @var DbResult $db_result */
        $db_result = $repo->setParams(['rotate_date' => $date->format('Ymd')])->filterByParams()->getDbResult();
        if ($db_result->getCount() == 0) {
            $this->warn(__('NONE'));
            return false;
        }
        $data = $db_result->getArray();
        $csv_export = new CsvExport($data, array_keys((array)$data[0]));
        foreach ($db_result->getGenerator() as $row) {
            $repo->deleteOneById($row['id']);
        }
        $csv_path = "rotate/{$prefix}_{$this->today->format('Ymd')}.csv";
        $csv_export->store($csv_path);
        $this->info(__('DONE'));
        return true;
    }

    /**
     *
     */
    private function zipCompress()
    {
        $this->output->write(__("Make BackUp Zip... "));
        $dir = storage_path() . "/app/rotate/";
        $zip_path = $dir . "{$this->today->format('Ymd')}.zip";
        $pattern = $dir . "*_{$this->today->format('Ymd')}.csv";
        $ret = $this->zip_service->setPattern($pattern)->compress($zip_path)->getResult();
        switch ($ret) {
            case $this->zip_service::RESULT_NONE:
                $this->warn('NONE');
                break;
            case $this->zip_service::RESULT_DONE:
                $this->info('DONE');
                break;
            case $this->zip_service::RESULT_FAIL:
                $this->error('Make Zip Failed!');
                break;
        }
    }


}