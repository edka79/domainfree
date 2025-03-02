<?php

namespace App\Console\Commands;

use App\Models\Expired;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Agregation;

class AgregationAddData extends Command
{
    protected $signature = 'command:agregationAddData';
    protected $description = 'Обогащение основной таблицы дополнительными данными';


    public function handle()
    {
        // метрики
        $start = microtime(true);
        $memory = memory_get_usage();
        ini_set('memory_limit', '768M');

        // добавляем признаки словарного слова и т.д.
        // todo !!! не будем обогащать данными из expipred, будем их просто джойнить

        // обогащение данными из expipred
        DB::statement("UPDATE agregations agr
            SET agr.expired_iks = nvl((select iks from domain_import_expired exp where exp.id = agr.domain), 0),
                agr.expired_links = nvl((
                    select (CASE
                            WHEN links > 0 and links <= 5 THEN links+1
                            WHEN links > 5 and links <= 15 THEN links+2
                            WHEN links > 15 and links <= 30 THEN links+3
                            WHEN links > 30 and links <= 60 THEN links+5
                            WHEN links > 60 and links <= 100 THEN links+7
                            WHEN links > 100 and links <= 300 THEN links+11
                            WHEN links > 300 THEN links+19
                        END) as links
                        from domain_import_expired exp where exp.id = agr.domain
                ), 0)");


        // обогащение данными: Домены-ключевики по Russian словарю
        foreach(DB::select("select ag.id as ag_id, ag.domain, ag.zone_id, nvl(ru_word.word, ru_word2.word) as word, nvl(ru_word.id, ru_word2.id) as word_id
            from agregations ag
                  left join dir_words_russian ru_word on ru_word.alias_ya = replace(ag.domain, '.ru', '') and ag.zone_id = 1
                  left join dir_words_russian ru_word2 on ru_word2.word = replace(ag.domain, '.рф', '') and ag.zone_id = 2
               where (ru_word.word != '' or ru_word2.alias_ya != '')
               group by ag.id") as $item){
            Agregation::where('id', $item->ag_id)->update([
                'is_keyword' => 'Да',
                'keyword_word' => $item->word,
            ]);
        }

        // обогащение данными: Домены-ключевики по English словарю
        foreach(DB::select("select ag.id as ag_id, ag.domain, english.translate as word
                from agregations ag
                     left join dir_words_english english on english.word = replace(ag.domain, '.ru', '')
                where ag.zone_id = 1
                    and english.word != ''
                group by ag.id") as $item){
            Agregation::where('id', $item->ag_id)->update([
                'is_keyword' => 'Да',
                'keyword_word' => 'EN: '.$item->word,
            ]);
        }



        // метрики
        $time = (microtime(true) - $start);
        echo 'Время выполнения: '.$time.PHP_EOL;
        $memory = memory_get_usage()-$memory;
        echo 'Скушано памяти: '.round($memory/1000000, 2).' МБ'.PHP_EOL;
    }



}
