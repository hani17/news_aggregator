<?php

namespace App\Filters\Interfaces;

use Illuminate\Database\Eloquent\Builder;

interface BaseFilterInterface
{
    public function apply(Builder $query): void;
}
