<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Collection;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function getArticleFields(): Collection
    {
        return collect([
            'api_id',
            'hash',
            'title',
            'published_at',
            'url',
            'category',
            'image_url',
            'source',
        ]);
    }
}
