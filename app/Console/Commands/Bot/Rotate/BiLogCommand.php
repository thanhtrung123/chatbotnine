<?php

namespace App\Console\Commands\Bot\Rotate;

use App\Services\File\ZipService;
use Illuminate\Console\Command;
use Carbon\Carbon;

/**
 * BIツール用ログローテート
 * Class BiLogCommand
 * @package App\Console\Commands\Bot\Rotate
 */
class BiLogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bot:rotate:bi-log';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bi-Log Csv File Rotate.';

    private $today;
    private $zip_service;

    /**
     * BiLogCommand constructor.
     * @param ZipService $zip_service
     */
    public function __construct(ZipService $zip_service)
    {
        $this->today = Carbon::today();
        $this->zip_service = $zip_service;
        parent::__construct();
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $this->info(__('Bi-Log Csv File Rotate Start.'));
        $this->zipCompress();
    }

    /**
     * ZIP圧縮
     */
    private function zipCompress()
    {

        $this->line(__("Make Zip... "));
        preg_match('/{(.+)}/', config('bot.bi_log.path'), $match);
        $pattern = storage_path() . '/' . str_replace($match[0], '*', config('bot.bi_log.path'));
        $ym = $this->today->format('Ym');
        $ym_p = $this->today->modify('-1 month')->format('Ym');
        $files = $this->zip_service->setPattern($pattern, "/{$ym}|{$ym_p}/")->getFiles();
        $this->zip_service->clearFiles();
        if (empty($files)) $this->info('NONE');
        foreach ($files as $file) {
            if (!preg_match('/(\d{4})(\d{2})/', $file, $match)) continue;
            $zip_path = storage_path() . '/' . config('bot.bi_log.rotate_dir') . "/{$match[1]}/{$match[2]}.zip";
            $this->zip_service->addFile($file)->compress($zip_path)->clearFiles();
            if ($this->zip_service->getResult() == $this->zip_service::RESULT_DONE) {
                $this->info("[{$zip_path}] Success.");
            } elseif ($this->zip_service->getResult() == $this->zip_service::RESULT_FAIL) {
                $this->error("[{$zip_path}] Failed.");
            }
        }
    }


}