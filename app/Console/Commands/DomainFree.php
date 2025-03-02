<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class DomainFree extends Command
{

    protected $signature = 'command:DomainFree';
    protected $description = 'Свободные домены';


    public function handle()
    {
        ini_set('memory_limit', '768M');
        // метрики
        $start = microtime(true);
        $memory = memory_get_usage();


        \App\Models\DomainFree::truncate();

        // ru и рф домены по Russian словарю
        DB::statement("insert into domain_free (domain, word, zone, word_type, litera_count, litera_attr)
            select
                concat(ru_word.alias_ya, '.ru') as domain, ru_word.word as word, 'ru' as zone, 'русский' as word_type, length(ru_word.alias_ya) as litera_count,
                (CASE WHEN locate('-', ru_word.alias_ya) > 0 THEN 'есть тире' ELSE 'без тире' END) as litera_attr
            from dir_words_russian ru_word
                left join domain_import_ru ru on ru_word.alias_ya = ru.domain_short
            where ru_word.alias_ya != ''
              and ru.domain is null
            group by ru_word.alias_ya
            UNION
            select
                concat(ru_word.word, '.рф') as domain, ru_word.word as word, 'рф' as zone, 'русский' as word_type, length(ru_word.word)/2 as litera_count,
                (CASE WHEN locate('-', ru_word.word) > 0 THEN 'есть тире' ELSE 'без тире' END) as litera_attr
            from dir_words_russian ru_word
                left join domain_import_rf rf on ru_word.word = rf.domain_short
            where ru_word.alias_ya != ''
              and rf.domain is null
            group by ru_word.alias_ya;");


        // ru домены по English словарю
        DB::statement("insert into domain_free (domain, word, zone, word_type, litera_count, litera_attr, translate)
            select
                concat(replace(en_word.word, ' ', ''), '.ru') as domain, en_word.word as word, 'ru' as zone, 'английский' as word_type, length(replace(en_word.word, ' ', '')) as litera_count,
                'без тире' as litera_attr, en_word.translate as translate
            from dir_words_english en_word
                 left join domain_import_ru ru on replace(en_word.word, ' ', '') = ru.domain_short
            where
                ru.domain is null
                and en_word.word like '% %'
                and length(en_word.word) < 15
            UNION
            select
                concat(replace(en_word.word, ' ', '-'), '.ru') as domain, en_word.word as word, 'ru' as zone, 'английский' as word_type, length(replace(en_word.word, ' ', '-')) as litera_count,
                'есть тире' as litera_attr, en_word.translate as translate
            from dir_words_english en_word
                 left join domain_import_ru ru on replace(en_word.word, ' ', '-') = ru.domain_short
            where
                ru.domain is null
              and en_word.word like '% %'
              and length(en_word.word) < 15
            UNION
            select
                concat(replace(en_word.word, ' ', '-'), '.ru') as domain, en_word.word as word, 'ru' as zone, 'английский' as word_type, length(en_word.word) as litera_count,
                'без тире' as litera_attr, en_word.translate as translate
            from dir_words_english en_word
                left join domain_import_ru ru on en_word.word = ru.domain_short
            where
                ru.domain is null
              and en_word.word not like '% %';");



        // метрики
        $time = (microtime(true) - $start);
        echo 'Время выполнения: '.$time.PHP_EOL;
        $memory = memory_get_usage()-$memory;
        echo 'Скушано памяти: '.round($memory/1000000, 2).' МБ'.PHP_EOL;

    }

}
