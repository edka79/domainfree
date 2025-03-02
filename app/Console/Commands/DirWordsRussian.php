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


class DirWordsRussian extends Command
{

    protected $signature = 'command:DirWordsRussian';
    protected $description = 'Словарь русских слов';


    public function handle()
    {
        ini_set('memory_limit', '6048M');
        $fp = file(storage_path().'/app/russian.txt');
        $data = [];
        foreach($fp as $item){
            $data[] = [
                'word' => strtolower(trim($item)),
                'alias_ya' => strtolower(self::translit_ya(trim($item))),
            ];
        }
        $upsert = array_chunk($data, 500);

        \App\Models\DirWordsRussian::truncate();
        foreach($upsert as $items){
            \App\Models\DirWordsRussian::insert($items);
            //dd();
        }

    }

    private function translit_ya($value)
    {
        $converter = array(
            'а' => 'a',    'б' => 'b',    'в' => 'v',    'г' => 'g',    'д' => 'd',
            'е' => 'e',    'ё' => 'yo',    'ж' => 'zh',   'з' => 'z',    'и' => 'i',
            'й' => 'y',    'к' => 'k',    'л' => 'l',    'м' => 'm',    'н' => 'n',
            'о' => 'o',    'п' => 'p',    'р' => 'r',    'с' => 's',    'т' => 't',
            'у' => 'u',    'ф' => 'f',    'х' => 'h',    'ц' => 'c',    'ч' => 'ch',
            'ш' => 'sh',   'щ' => 'sch',  'ь' => '',     'ы' => 'y',    'ъ' => '',
            'э' => 'e',    'ю' => 'yu',   'я' => 'ya'
        );

        $value = strtr($value, $converter);
        return strtolower($value);
    }


}
