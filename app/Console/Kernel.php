<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
         Commands\Inspire::class,
         Commands\RenewalReminder::class,
         Commands\AssignMenus::class,
         Commands\TestCommand::class,
         Commands\CheckAbandoned::class,
         Commands\CronTest::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->command("cron:test")
            ->dailyAt('13:00')
            ->sendOutputTo("storage/logs/cronTest.log")
            ->emailOutputTo("ahhmed@mail.ru");

        //Completed automation on 09/29/2016 at 06:01

         $schedule->command('inspire')
            ->everyFiveMinutes()
            ->sendOutputTo("storage/logs/inspireTest.log")
            ->emailOutputTo("ahhmed@mail.ru");


//        $schedule->command('renewal:reminder')
//            ->thursdays()->at('23:59');
//            ->fridays()->at('19:00')
//            ->sendOutputTo("storage/logs/renewalReminder.log")
 //           ->emailOutputTo('ahhmed@mail.ru');

        $schedule->command('check:abandoned')
            ->cron("*/15 * * * *")->withoutOverlapping()
            ->sendOutputTo("storage/logs/checkAbandoned.log")
            ->emailOutputTo("ahhmed@mail.ru");
    }
}
