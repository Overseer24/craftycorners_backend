<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
//        $schedule->call(function () {
//            $this->checkUpcomingSchedules();
//        })->everyMinute();
    }

//    protected function  checkUpcomingSchedules(): void
//    {
//        $schedules = Schedule::where('start', '>', now())
//            ->where('start', '<', now()->addMinutes(5))
//            ->get();
//
//        foreach ($schedules as $schedule) {
//            $user = $schedule->user;
//            $user->notify(new ScheduleNotification($schedule));
//        }
//    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
