<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class Agregation extends Command
{
    protected $signature = 'command:agregation';
    protected $description = 'Сборка основной таблицы';


    public function handle()
    {
        // метрики
        $start = microtime(true);
        $memory = memory_get_usage();
        ini_set('memory_limit', '512M');


        $date_start = date('Y-m-d');
        $date_end = date('Y-m-d', time()+86400*31);

        // выборка доменов +30 дней до освобождения
        $domainRu = DB::table('domain_import_ru')
            ->whereBetween('date_free', [$date_start, $date_end]);
        $domainRf = DB::table('domain_import_rf')
            ->whereBetween('date_free', [$date_start, $date_end]);
        $domains = DB::table('domain_import_rf')
            ->whereBetween('date_free', [$date_start, $date_end])
            ->union($domainRu)
            ->union($domainRf)
            ->get();

          $cnt = 0;
          $data = [];

          DB::table('agregations')->truncate();
          foreach($domains as $key => $domain){
            unset($domains[$key]);
            $exp = explode('.', $domain->domain);
                    // атрибуты домены (цифры и тире)
                    $isDigits = preg_match("/\d+/", $exp[0]);
                    $isTire = stripos($exp[0], '-');
              $litera_attr = 'без цифр и тире';
              if ($isDigits) $litera_attr = 'есть цифры';
              if ($isTire) $litera_attr = 'есть тире';
              if ($isDigits and $isTire) $litera_attr = 'есть цифры и тире';

            $data[] = [
                'domain' => $domain->domain,
                'zone_id' => $domain->zone_id,
                'registrator_id' => $domain->registrator_id,
                'litera_count' => iconv_strlen($exp[0]),
                'litera_attr' => $litera_attr,
                'age' => date('Y')-date('Y', strtotime($domain->date_create)),
                'date_create' => $domain->date_create,
                'date_paid' => $domain->date_paid,
                'date_free' => $domain->date_free,
            ];

              $cnt++;
              if ($cnt == 1000) {  // 1000 - оптимально
                  DB::table('agregations')->insert($data);
                  $cnt = 0;
                  $data = [];
              }
         }
        DB::table('agregations')->insert($data); // добивка остатка массива




        // метрики
        $time = (microtime(true) - $start);
        echo 'Время выполнения: '.$time.PHP_EOL;
        $memory = memory_get_usage()-$memory;
        echo 'Скушано памяти: '.round($memory/1000000, 2).' МБ'.PHP_EOL;
    }


}
