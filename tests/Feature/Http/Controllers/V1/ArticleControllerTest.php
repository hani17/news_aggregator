<?php

namespace Tests\Feature\Http\Controllers\V1;

use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleControllerTest extends TestCase
{
    use RefreshDatabase;

    const ARTICLES_URL = 'api/v1/articles';

    public function test_it_fetches_articles_successfully(): void
    {
        Article::factory()->count(10)->create();

        $this->getJson(self::ARTICLES_URL)
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'published_at',
                        'url',
                        'image_url',
                        'category',
                        'source',
                    ],
                ],
            ]);
    }

    public function test_it_filter_articles_using_category(): void
    {
        $category = Category::factory()
            ->has(Article::factory()->count(10))
            ->create();

        $res = $this->getJson(self::ARTICLES_URL.'/?category='.$category->id)
            ->assertOk();

        $articles = $res->json('data');

        foreach ($articles as $article) {
            $this->assertEquals($article['category']['id'], $category->id);
        }
    }

    public function test_it_filter_articles_using_source(): void
    {
        $source = Source::factory()
            ->has(Article::factory()->count(10))
            ->create();

        $res = $this->getJson(self::ARTICLES_URL.'/?source='.$source->id)
            ->assertOk();

        $articles = $res->json('data');

        foreach ($articles as $article) {
            $this->assertEquals($article['source']['id'], $source->id);
        }
    }

    public function test_it_filter_articles_using_date(): void
    {
        $dateTimeInFuture = Carbon::now()->addWeek();
        $dateTimeInPast = Carbon::now()->subWeek();

        Article::factory()->count(5)->create([
            'published_at' => $dateTimeInFuture,
        ]);

        Article::factory()->count(5)->create([
            'published_at' => $dateTimeInPast,
        ]);

        $res = $this->getJson(self::ARTICLES_URL.'/?date='.$dateTimeInFuture->format('Y-m-d'))
            ->assertOk();

        $articles = $res->json('data');

        $this->assertCount(5, $articles);
    }

    public function test_it_gets_articles_using_search(): void
    {
        $searchArticle = Article::factory()->create();
        Article::factory()->count(5)->create();

        $res = $this->getJson(self::ARTICLES_URL.'/?q='.$searchArticle->title)
            ->assertOk();

        $article = $res->json('data.0');

        $this->assertEquals($searchArticle->title, $article['title']);
    }
}
