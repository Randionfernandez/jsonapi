<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SparseFieldsArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function specifics_fields_can_be_requested_in_the_article_index(): void
    {
        $article = Article::factory()->create();
        // articles?fields[articles]=title,slug    // según json:api
        $url = route('api.v1.articles.index', [
            'fields' => ['articles' => 'title,slug']
        ]);

        $this->getJson($url)->assertJsonFragment([
            'title' => $article->title,
            'slug' => $article->slug,
        ])->assertJsonMissing([
            'content' => $article->content,
        ])->assertJsonMissing([
            'content' => null
        ]);
    }

    /** @test */
    public function specifics_fields_can_be_requested_in_the_article_show(): void
    {
        $article = Article::factory()->create();

        // articles/{article}?fields[articles]=title,slug    // según json:api
        $url = route('api.v1.articles.show', [
            'article' => $article,
            'fields' => [
                'articles' => 'title,slug',
            ]
        ]);

//        dd(urldecode($url));
        $this->getJson($url)->assertJsonFragment([
            'title' => $article->title,
            'slug' => $article->slug,
        ])->assertJsonMissing([
            'content' => $article->content,
        ])->assertJsonMissing([
            'content' => null
        ]);
    }

    /** @test */
    public function route_key_must_be_added_automatically_in_the_article_index(): void
    {
        $article = Article::factory()->create();
        // articles?fields[articles]=title,slug    // según json:api
        $url = route('api.v1.articles.index', [
            'fields' => ['articles' => 'title']
        ]);

        $this->getJson($url)->assertJsonFragment([
            'title' => $article->title,
        ])->assertJsonMissing([
            'slug' => $article->slug,
            'content' => $article->content,
        ]);
    }

    /** @test */
    public function route_key_must_be_added_automatically_in_the_article_show(): void
    {
        $article = Article::factory()->create();
        // articles?fields[articles]=title,slug    // según json:api
        $url = route('api.v1.articles.show', [
            'article'=> $article,
            'fields' => ['articles' => 'title']
        ]);

        $this->getJson($url)->assertJsonFragment([
            'title' => $article->title,
        ])->assertJsonMissing([
            'slug' => $article->slug,
            'content' => $article->content,
        ]);
    }
}
