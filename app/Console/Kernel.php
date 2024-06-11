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
        Commands\NewCurrency::class,
        Commands\OrderInterest::class,
        Commands\ExecuteCurrency::class,
        Commands\GetKline_FifteenMin::class,
        Commands\GetKline_Daily::class,
        Commands\GetKline_FiveMin::class,
        Commands\GetKline_Hourly::class,
        Commands\GetKline_Monthly::class,
        Commands\GetKline_ThirtyMin::class,
        Commands\GetKline_Weekly::class,
        Commands\Robot::class,
        Commands\UpdateFund::class,
        Commands\Micro_order::class,
        Commands\Micro_send::class,
        Commands\User_ip::class,
        Commands\Daily_report::class,
        Commands\Agent_report::class,
        Commands\Clear_gold::class,
        Commands\Futures::class

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
     /*   $schedule->command('update_user_fund')->daily()->appendOutputTo('./storage/logs/update_user_fund.log'); //更新秒合约资产
      */  $schedule->command('order_interest')->dailyAt('00:01'); // 结算利息
        $schedule->command('new_currency_order')->everyMinute(); // 新币解冻
        $schedule->command('daily_report')->everyFiveMinutes(); // 日报表
        $schedule->command('agent_report')->everyFiveMinutes(); // 代理报表
        $schedule->command('clear_gold')->everyMinute(); // 清理体验金

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
