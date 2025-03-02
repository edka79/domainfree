<?php

namespace App\Console\Commands;

use App\Models\DomainImportExpired;
use App\Models\DomainDirRegistrator;
use App\Models\DomainImportRu;
use App\Models\DomainDirZone;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;



class Ru extends Command
{
    protected $signature = 'command:ru';
    protected $description = 'Все домены рунета (RU, РФ, SU)';

    protected $zones = [];
    protected $registrators = [];

    public function handle()
    {
        ini_set('memory_limit', '1024M');
        // метрики
        $start = microtime(true);
        $memory = memory_get_usage();

        // зоны домена
        foreach(DomainDirZone::all() as $item){
            $this->zones[$item->zone] = $item->id;
        }
        // регистраторы домена
        foreach(DomainDirRegistrator::all() as $item){
            $this->registrators[$item->name] = $item->id;
        }

        // получаем файлы со списком доменов: RU, РФ, SU и запускаем импорты в базу
        Funct::getSaveFile('https://ru-tld.ru/files/RU_Domains_ru-tld.ru.gz', 'RU_Domains_ru-tld.ru.gz', 'ОШИБКА не могу получить файл https://ru-tld.ru/files/RU_Domains_ru-tld.ru.gz');
           self::importRuDomain('RU_Domains_ru-tld.ru', 'domain_import_ru');
        Funct::getSaveFile('https://ru-tld.ru/files/RF_Domains_ru-tld.ru.gz', 'RF_Domains_ru-tld.ru.gz', 'ОШИБКА не могу получить файл https://ru-tld.ru/files/RF_Domains_ru-tld.ru.gz');
           self::importRuDomain('RF_Domains_ru-tld.ru', 'domain_import_rf');
        Funct::getSaveFile('https://ru-tld.ru/files/SU_Domains_ru-tld.ru.gz', 'SU_Domains_ru-tld.ru.gz', 'ОШИБКА не могу получить файл https://ru-tld.ru/files/SU_Domains_ru-tld.ru.gz');
           self::importRuDomain('SU_Domains_ru-tld.ru', 'domain_import_su');

        // метрики
        $time = (microtime(true) - $start);
        echo 'Время выполнения: '.$time.PHP_EOL;
        $memory = memory_get_usage()-$memory;
        echo 'Скушано памяти: '.round($memory/1000000, 2).' МБ'.PHP_EOL;
    }

    // импорт данных в таблицы
    private function importRuDomain($file, $table){
        $fp = fopen(storage_path().'/app/'.$file, "r");

       // todo Добавить проверку на размер файла

        DB::table($table)->truncate();

        $cnt = 0;
        $data = [];
        while (($item = fgets($fp, 4096)) !== false){
            $row = explode('	', trim($item));
            $row[0] = strtolower($row[0]);
            $row[1] = strtolower($row[1]);

            // определяем зону домена и получаем ее ID
            $domain = explode('.', $row[0]);
            $zone_id = $this->zones[end($domain)];

            if (isset($this->registrators[$row[1]])){
                $reg_id = $this->registrators[$row[1]];
            }
            else {
                $this->registrators[$row[1]] = DomainDirRegistrator::insertGetId([ 'name' => $row[1] ]);
                $reg_id = $this->registrators[$row[1]];
            }

            // если это РФ домен, декодируем имя домена из пуникода
            $domain = $zone_id != 2 ? $row[0] : idn_to_utf8($row[0]);

            $data[] = [
                'domain' => $domain,
                'domain_short' => str_replace(['.ru', '.рф', '.su'], '', $domain),
                'registrator_id' => $reg_id,
                'zone_id' => $zone_id,
                'date_create' => date('Y-m-d', strtotime($row[2])),
                'date_paid' => date('Y-m-d', strtotime($row[3])),
                'date_free' => date('Y-m-d', strtotime($row[4])),
            ];

            $cnt++;
            if ($cnt == 1000) {  // 1000 - оптимально
                DB::table($table)->insert($data);
                $cnt = 0;
                $data = [];
            }
        }
        DB::table($table)->insert($data); // добивка остатка массива

        fclose($fp);
    }


}
