<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Artisan;

/**
 * Class ClearCommand
 * @package App\Console\Commands
 */
class ClearCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'All Cache Clear';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $out = $this->getOutput();
        $commands = [
            'clear-compiled' => [],
            'cache:clear' => [],
            'config:clear' => [],
            'debugbar:clear' => [],
            'route:clear' => [],
            'view:clear' => [],
            'event:clear' => [],
            'optimize:clear' => [],
        ];
        foreach ($commands as $command => $params) {
            $this->line("CALL [{$command}] ");
            try {
                Artisan::call($command, $params, $out);
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }
    }


}
