<?php

namespace App\Console\Commands;

use App\Models\DirWordsEnglish;
use App\Models\DomainImportExpired;
use App\Models\DomainImportRu;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Http;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class Temp extends Command
{

    protected $signature = 'command:Temp';
    protected $description = 'Временный контроллер для выполнения любых команд';


    public function handle()
    {

        dd('STOP');

        ini_set('memory_limit', '6048M');
        $fp = file(storage_path().'/app/enrus.txt');
        $data = [];
        foreach($fp as $key => $item){
            if ($key % 2 === 0) {
                $data[$key] = [
                    'word' => mb_strtolower(trim($item)),
                    'translate' => '',
                ];
            }
            else {
                $data[$key-1]['translate'] = trim($item);
            }
        }

        $upsert = array_chunk($data, 5000);
        foreach($upsert as $items){
            DirWordsEnglish::insert($items);
            //dd();
        }

    }



}
