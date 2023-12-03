<?php

namespace App\Services;

use Illuminate\Support\Arr;

class BaseApiService
{

    public function createHash(array $values): string
    {
        return sha1(implode('', $values));
    }
}
