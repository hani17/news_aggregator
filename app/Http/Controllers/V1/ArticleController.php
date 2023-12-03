<?php

namespace App\Http\Controllers\V1;

use App\Filters\ArticleFilter;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index(ArticleFilter $filter)
    {
        return ArticleResource::collection(
            Article::filter($filter)
                ->with(['source', 'category'])
                ->latest()
                ->paginate()
        );
    }
}
