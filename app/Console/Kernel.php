<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\CheckUserPlan;
use App\Models\Suspend;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $filePath = __DIR__ . '/output.php';
        $schedule->command('check-user-plan')->cron('* * * * *')->appendOutputTo($filePath);

        $schedule->call(function () {
            $suspended_ids = Suspend::where([
                ['starts_at', '<=', Carbon::now()],
                ['ends_at', '>=', Carbon::now()],
            ])->with(['user'])->get()->pluck('user.id')->toArray();

            \Cache::put('suspended_users', json_encode($suspended_ids));
        })->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
