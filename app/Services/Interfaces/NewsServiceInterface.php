<?php

namespace App\Services\Interfaces;

use Illuminate\Support\Collection;

interface NewsServiceInterface
{
    public function fetchArticles(): ?Collection;

    public function formatResponse(array $data): ?Collection;
}
