<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FilterArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_filter_articles_by_title(): void
    {
        Article::factory()->create([
            'title' => "Aprende Laravel Desde Cero"
        ]);

        Article::factory()->create([
            'title' => "Other Article"
        ]);

        // articles?filter[title]=Laravel    // según json:api
        $url = route('api.v1.articles.index', [
            'filter' => [
                'title' => 'Laravel',
            ]
        ]);
//        dump(urldecode($url));

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee("Aprende Laravel Desde Cero")
            ->assertDontSee("Other Article");
    }

    /** @test */
    public function can_filter_articles_by_content(): void
    {
        Article::factory()->create([
            'content' => "Aprende Laravel Desde Cero"
        ]);

        Article::factory()->create([
            'content' => "Other Article"
        ]);

        // articles?filter[content]=Laravel    // según json:api
        $url = route('api.v1.articles.index', [
            'filter' => [
                'content' => 'Laravel',
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee("Aprende Laravel Desde Cero")
            ->assertDontSee("Other Article");
    }

    /** @test */
    public function can_filter_articles_by_year(): void
    {
        Article::factory()->create([
            'title' => "Aprende Laravel from 2021",
            'created_at' => now()->year(2021),
        ]);

        Article::factory()->create([
            'title' => "Article from 2022",
            'created_at'=> now()->year(2022),
        ]);

        // articles?filter[year]=2021    // según json:api
        $url = route('api.v1.articles.index', [
            'filter' => [
                'year' => '2021',
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(1, 'data')
            ->assertSee("Aprende Laravel from 2021")
            ->assertDontSee("Article from 2022");
    }

    /** @test */
    public function can_filter_articles_by_month(): void
    {
        Article::factory()->create([
            'title' => "Aprende Laravel from month 3",
            'created_at' => now()->month(3),
        ]);

        Article::factory()->create([
            'title' => "Otro Aprende Laravel from month 3",
            'created_at' => now()->month(3),
        ]);

        Article::factory()->create([
            'title' => "Article from month 1",
            'created_at'=> now()->month(1),
        ]);

        // articles?filter[month]=3    // según json:api
        $url = route('api.v1.articles.index', [
            'filter' => [
                'month' => '3',
            ]
        ]);

        $this->getJson($url)
            ->assertJsonCount(2, 'data')
            ->assertSee("Aprende Laravel from month 3")
            ->assertSee("Otro Aprende Laravel from month 3")
            ->assertDontSee("Article from month 1");
    }

    /** @test */
    public function cannot_filter_articles_by_unknown_filters(): void
    {
        Article::factory(2)->create();

        // articles?filter[unknown]=filter    // según json:api
        $url = route('api.v1.articles.index', [
            'filter' => [
                'unknown' => 'filter',
            ]
        ]);

        $this->getJson($url)
            ->assertStatus(400); // Mala petición
    }
}
