<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteArticleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_delete_articles(): void
    {
        $article= Article::factory()->create();

        $response= $this->deleteJson(route('api.v1.articles.destroy', $article))
            ->assertNoContent();  // verifica el estado 204, No Content

        $this->assertDatabaseCount('articles', 0); // verifica que en la tabla 'articles' no existe ningún artículo
    }
}
