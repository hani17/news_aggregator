<?php

namespace Tests\Feature\Services;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\NewsApiService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class NewsApiServiceTest extends TestCase
{
    protected $body;

    public function setUp(): void
    {
        parent::setUp();
        $this->body = file_get_contents(base_path('tests/Fixtures/Helpers/news_api_response_body.json'));
    }

    public function test_it_fetches_articles_from_news_api_successfully(): void
    {
        Http::fake([
            'https://newsapi.org/v2/*' => Http::response($this->body, Response::HTTP_OK),
        ]);
        Log::shouldReceive('error')->never();

        $service = new NewsApiService();
        $article = $service->fetchArticles()->first();

        $this->getArticleFields()->each(fn ($i) => $this->assertArrayHasKey($i, $article));
    }

    public function test_it_logs_error_and_return_null_from_fetchArticles_when_http_error_happens(): void
    {
        Http::fake([
            'https://newsapi.org/v2/*' => Http::response($this->body, Response::HTTP_INTERNAL_SERVER_ERROR),
        ]);
        Log::shouldReceive('error');

        $service = new NewsApiService();
        $articles = $service->fetchArticles();

        $this->assertNull($articles);
    }
}
