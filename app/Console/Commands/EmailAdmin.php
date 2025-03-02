<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;


class EmailAdmin extends Command
{

    protected $signature = 'command:EmailAdmin';
    protected $description = 'Отправка Email админу';

    public function handle($subj, $mess)
    {

    }

    static function sendEmail($error, $subj, $mess = '')
    {
        Mail::raw($mess.PHP_EOL.PHP_EOL.'--------- ERROR MESSAGE ---------'.PHP_EOL.PHP_EOL.$error, function (Message $message) use ($subj, $mess) {
            $message->to(getenv('MAIL_ADMIN'))
                ->subject($subj)
                ->from(getenv('MAIL_FROM_ADDRESS'));
        });
    }

}
