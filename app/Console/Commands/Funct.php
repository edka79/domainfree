<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;


class Funct extends Command
{

    protected $signature = 'command:Helpers';
    protected $description = 'Сборник разных функций';

    public function handle()
    {
        return 'Это сборник разных функций';
    }



    // Отправка Email админу, как правило об ошибках и сбоях
    static function sendEmail($error, $subj, $mess = '')
    {
        Mail::raw($mess.PHP_EOL.PHP_EOL.'--------- ERROR MESSAGE ---------'.PHP_EOL.PHP_EOL.$error, function (Message $message) use ($subj, $mess) {
            $message->to(getenv('MAIL_ADMIN'))
                ->subject($subj)
                ->from(getenv('MAIL_FROM_ADDRESS'));
        });
    }

    // Получаем и сохраняем на диск файлы (как правилно CSV с доменами и т.д.)
    static function getSaveFile($url, $filename, $errorMessage = 'ОШИБКА не указана'){
        try {
            $response = Http::timeout(240)->get($url);
            Storage::disk('local')->put($filename, $response);
            // если это файл .gz, то распаковываем его
            $ext = explode('.', $filename);
            if (end($ext) == 'gz'){
                $path = storage_path().'/app/';
                $zipped = file_get_contents($path.$filename);
                file_put_contents($path.str_replace('.gz', '', $filename), gzdecode($zipped));
                // и удаляем исходник архива
                unlink($path.$filename);
            }
        }
        catch (Exception $e) {
            // при ошибке получения файла отправляем email админу
            self::sendEmail(
                $e->getTraceAsString(),
                $errorMessage,
                ''
            );
        }
    }

}
