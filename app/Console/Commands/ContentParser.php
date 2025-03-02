<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use App\Models\Content;
use App\Models\DomainImportRu;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Http;
use Exception;


class ContentParser extends Command
{

    protected $signature = 'command:ContentParser';
    protected $description = 'Парсинг контента (автономный с встроенной логикой)';

    private $date_window_start;
    private $date_window_end;

    // TODO Добавить еще парсинг всех внутренних анкоров ссылок с сайта

    public function handle()
    {
        ini_set('memory_limit', '768M');
        // сохраняем только домены, которые попадают в следующее ОКНО дат
        // дата начала окна = -6 месяцев от date_free
        // длина окна = 15 дней
        $this->date_window_start = date('Y-m-d H:i:s', time()+(86400*30*3));
        $this->date_window_end = date('Y-m-d H:i:s', time()+(86400*30*6)+(86400*15));

        $domains = DomainImportRu::leftJoin('contents', 'domain_import_ru.domain', '=', 'contents.domain')
            ->whereNull('contents.szie')
            ->select('domain_import_ru.domain', 'contents.size', 'domain_import_ru.date_free')
            ->where('date_free', '>=', $this->date_window_start)
            ->where('date_free', '<=', $this->date_window_end)
            ->inRandomOrder()
            ->limit(100000)
            ->get();

    //dd(1);
        foreach($domains as $domain){
            echo $domain->domain.PHP_EOL;
            self::getDirect($domain);
        }
    }

    // Прямое скачивание сайта (используется в первую очередь)
    private function getDirect($domain)
    {
        $res = false;
        try {
            $ch = curl_init($domain->domain);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
            $res = curl_exec($ch);
            curl_close($ch);
        } catch (\Exception $e){
            //
            echo 'error'.PHP_EOL;
        }

        if ($res) {
            $body = $res;
            try {
                // windows-1251
                if(stripos($body, 'windows-1251"') !== false or stripos($body, 'cp1251"') !== false){
                    $body = iconv('Windows-1251', 'UTF-8', $body);
                }
            } catch (\Exception $e){
                $body = '';
            }
            // get title
            preg_match_all("/(\<title\>)(.*?)(\<\/title\>)/s", $body, $title);
            $title = !empty($title[2][0]) ? trim($title[2][0]) : '';
            $title = preg_replace('/\&.{1,10}\;/', ' ', $title); // special symbols replace
            $title = preg_replace('/\ {2,50}/', ' ', $title); // replace spaces
            // strip all tags for body
            $body = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $body);
            $body = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $body);
            $body = preg_replace("/\s+/u", " ", $body);
            $body = preg_replace(['/<[^>]*>/','/\s+/'],' ', $body);
            $body = preg_replace('/\&.{1,10}\;/', ' ', $body); // special symbols replace
            $body = preg_replace('/\ {2,50}/', ' ', $body); // replace spaces
            $body = trim($body);
        }

        Content::upsert([
            'domain' => $domain->domain,
            'title' => $title ?? '',
            'content' => !empty($body) ? $body : '',
            'size' => !empty($body) ? strlen($body) : 0,
        ], ['domain']);

    }

}
