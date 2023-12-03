<?php

namespace App\Traits;

trait Filterable
{
    public function scopeFilter($query, $filter): void
    {
        $filter->apply($query);
    }
}
