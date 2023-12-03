<?php

namespace App\Services;

class BaseApiService
{
    public function createHash(array $values): string
    {
        return sha1(implode('', $values));
    }
}
