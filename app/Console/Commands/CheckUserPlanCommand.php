<?php

namespace App\Console\Commands;

use App\Jobs\CheckUserPlan;
use Illuminate\Console\Command;

class CheckUserPlanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check-user-plan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check users subscriptions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        CheckUserPlan::dispatch();
        return 1;
    }
}
