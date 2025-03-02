<?php

namespace App\Http\Controllers;

use App\Services\NobodyService;
use Illuminate\Http\Request;

class NobodyController extends Controller
{
    protected $nobodyService;

    public function __construct(NobodyService $nobodyService)
    {
        $this->nobodyService = $nobodyService;
    }

    // Генерация нового хэша
    public function index()
    {
        return $this->nobodyService->generateHash();
    }

    // Проверка хэша (статический метод)
    public static function HashVerify($hash = null)
    {
        // Используем сервис через контейнер Laravel
        return app(NobodyService::class)->verifyHash($hash);
    }
}
