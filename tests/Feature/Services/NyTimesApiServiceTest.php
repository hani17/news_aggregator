<?php

namespace Tests\Feature\Services;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\NyTimesApiService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class NyTimesApiServiceTest extends TestCase
{

    protected $body;

    public function setUp(): void
    {
        parent::setUp();
        $this->body = file_get_contents(base_path('tests/Fixtures/Helpers/nytimes_response_body.json'));
    }

    public function test_it_fetches_articles_from_ny_times_api_successfully(): void
    {
        Http::fake([
            'https://api.nytimes.com/svc/search/v2/*' => Http::response($this->body, Response::HTTP_OK)
        ]);
        Log::shouldReceive('error')->never();

        $service = new NyTimesApiService();
        $article = $service->fetchArticles()->first();

        $this->getArticleFields()->each(fn($i) => $this->assertArrayHasKey($i, $article));
    }

    public function test_it_calculates_correct_hash_from_ny_times_api_id(): void
    {
        Http::fake([
            'https://api.nytimes.com/svc/search/v2/*' => Http::response($this->body, Response::HTTP_OK)
        ]);
        Log::shouldReceive('error')->never();

        $service = new NyTimesApiService();
        $article = $service->fetchArticles()->first();

        $this->assertEquals(sha1($article['api_id']), $article['hash']);
    }

    public function test_it_logs_error_and_return_null_from_fetchArticles_when_http_error_happens(): void
    {
        Http::fake([
            'https://api.nytimes.com/svc/search/v2/*' => Http::response($this->body, Response::HTTP_INTERNAL_SERVER_ERROR)
        ]);
        Log::shouldReceive('error');

        $service = new NyTimesApiService();
        $articles = $service->fetchArticles();

        $this->assertNull($articles);
    }
}
