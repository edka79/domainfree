<?php

namespace App\Console;

use App\Models\Cron;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        foreach (Cron::whereNull('disabled')->get()->toArray() as $item){
            $schedule->command($item['command'])
                ->cron($item['timetable'])
                ->withoutOverlapping()->runInBackground()
                ->when(function() use ($item){
                    if($item['dayweek'] != null){
                        return $item['dayweek'] == date('w') ? true : false;
                    }
                    else {
                        return true;
                    }
                })
                // Задача успешно выполнена
                ->onSuccess(function () use ($item) {
                    Cron::where('id', $item['id'])->update(['last_status' => 'ok']);
                })
                // Задача выполнена с ошибкой
                ->onFailure(function() use ($item){
                    Cron::where('id', $item['id'])->update(['last_status' => 'error']);
                    Mail::send('email.exception', [
                        'sql_essage' => 'Ошибка CRON',
                        'error_code' => $item['command'],
                        'error_code2' => '',
                        'file_error' => $item['timetable'],
                    ], function($message) use ($item){
                        $message
                            ->to(['to@mail.ru'])
                            ->from('from@mail.ru')
                            ->subject('Ошибка: '.$item['command']);
		            });
                });

        }
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
