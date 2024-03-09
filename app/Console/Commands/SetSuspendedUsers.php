<?php

namespace App\Console\Commands;

use App\Models\Suspend;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SetSuspendedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SetSuspendedUsers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'set suspended users';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $suspended_ids = Suspend::where([
            ['starts_at', '<=', Carbon::now()],
            ['ends_at', '>=', Carbon::now()],
        ])->with(['user'])->get()->pluck('user.id')->toArray();

        \Cache::put('suspended_users', json_encode($suspended_ids));
        return Command::SUCCESS;
    }
}
