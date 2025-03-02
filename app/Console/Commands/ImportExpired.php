<?php

namespace App\Console\Commands;

use App\Models\DomainImportExpired;
use Faker\Provider\ar_EG\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Exception;
use App\Console\Commands\EmailAdmin;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;


class ImportExpired extends Command
{

    protected $signature = 'command:ImportExpired';
    protected $description = 'Импорт данных с expired';

    private $url = 'https://expired.ru/lists/expired.csv';


    public function handle()
    {
        ini_set('memory_limit', '768M');
        // метрики
        $start = microtime(true);
        $memory = memory_get_usage();

        // получаем файл
        self::getFile();
        // пишем в базу изменения
        self::insertUpdate();

        // метрики
        $time = (microtime(true) - $start);
        echo 'Время выполнения: '.$time.PHP_EOL;
        $memory = memory_get_usage()-$memory;
        echo 'Скушано памяти: '.round($memory/1000000, 2).' МБ'.PHP_EOL;
    }


    private function insertUpdate(){
        // TODO добавить exeption
        // разбираем файл
        $rows = file(storage_path('app/expired.csv'), FILE_IGNORE_NEW_LINES);
        $data = [];
        $dateload = date('Y-m-d H:i:s');
        foreach($rows as $key => $row){
            unset($rows[$key]);
            if ($key == 0) continue;
            $t = explode(';', $row);
                // данные о самом домене
                $ext = explode('.', $t[0]);
                // пропускаем домены 3-го уровня
                if (count($ext) > 2) continue;
                $zone = [
                    'ru' => 1,
                    'рф' => 2,
                    'su' => 3,
                ];

            // todo !!! добавить рандомизацию данных в небольших пределах
            $data[] = [
                'id' => $t[0],
                'dateload' => $dateload,
                'zone' => $zone[end($ext)],
                'stavka' => $t[1] == 'yes' ? 1 : null,
                'tic' => $t[2],
                'iks' => $t[3],
                'alexa' => $t[4],
                'links' => $t[5],
                'sw' => $t[6],
                'li' => $t[7],
                'mydrop' => $t[8],
                'ur' => $t[9] == 'yes' ? 1 : null,
                'vozrast' => $t[10],
                'date_create' => $t[11],
                'date_free' => $t[12],
            ];
        }
        $inserts = array_chunk($data, 3000);
        DomainImportExpired::truncate();
        foreach($inserts  as $insert){
            DB::table('Domain_import_expired')->insert($insert);
        }
    }

    private function getFile(){
        try {
            $response = Http::retry(3, 3000)->get($this->url);
            Storage::disk('local')->put('expired.csv', $response);
        }
        catch (Exception $e) {
           EmailAdmin::sendEmail(
               $e->getTraceAsString(),
               'ОШИБКА: не могу получить файл expired.csv',
               ''
           );
        }
    }

}
