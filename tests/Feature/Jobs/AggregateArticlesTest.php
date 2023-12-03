<?php

namespace Tests\Feature\Jobs;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Jobs\AggregateArticles;
use App\Services\AggregatorService;
use Mockery;
use Tests\TestCase;

class AggregateArticlesTest extends TestCase
{
    public function test_it_calls_aggregator_service_fetch_and_save_articles_method(): void
    {
        $service = $this->instance(
            AggregatorService::class,
            Mockery::mock(AggregatorService::class, function (Mockery\MockInterface $mock) {
                $mock->shouldReceive('fetchAndSaveArticles')->once();
            })
        );

        $job = new AggregateArticles();
        $job->handle();
    }
}
