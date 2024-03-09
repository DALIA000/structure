<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class AgainCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'again';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'i use this command to refresh database and seed it again and install passport too';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Artisan::call('migrate:fresh --seed');
        $this->info(Artisan::output());
        Artisan::call('passport:install');
        $this->info(Artisan::output());
        Artisan::call('optimize:clear');
        $this->info(Artisan::output());

        return 0;
    }
}
