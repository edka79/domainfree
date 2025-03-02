<?php

namespace App\Services;

use App\Models\UsersNobody;
use Illuminate\Support\Facades\DB;

class NobodyService
{
    // Генерация нового хэша и создание записи
    public function generateHash()
    {
        $hash = md5(rand(111, 9999999999) . time());
        $id = UsersNobody::insertGetId(['hash' => $hash]);
        return $id . ':' . $hash;
    }

    // Проверка хэша
    public function verifyHash($hash = null)
    {
        if (!$hash) {
            $hash = request()->header('nobody');
        }

        $hashParts = explode(':', $hash);
        if (count($hashParts) != 2) {
            return 0;
        }

        $userId = $hashParts[0] ?? 0;
        $userHash = $hashParts[1] ?? '';

        $user = UsersNobody::where('id', $userId)->first();

        if ($user && $user->hash === $userHash) {
            return $user->id;
        }

        return 0;
    }
}
