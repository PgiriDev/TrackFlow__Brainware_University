<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;

class FaceCryptoService
{
    public static function encryptVector(array $vector): string
    {
        return Crypt::encryptString(json_encode($vector));
    }

    public static function decryptVector(string $encrypted): array
    {
        return json_decode(Crypt::decryptString($encrypted), true);
    }
}
