<?php

namespace App\Filters;

use App\Filters\Interfaces\BaseFilterInterface;
use Illuminate\Database\Eloquent\Builder;

class ArticleFilter implements BaseFilterInterface
{
    public function apply(Builder $query): void
    {
        if ($q = request()->input('q')) {
            $query->where('title', 'LIKE', '%' . $q . '%');
        }

        if ($source = request()->input('source')) {
            $query->whereSourceId($source);
        }

        if ($category = request()->input('category')) {
            $query->whereCategoryId($category);
        }

        if ($date = request()->input('date')) {
            $query->whereDate('published_at', $date);
        }
    }
}
