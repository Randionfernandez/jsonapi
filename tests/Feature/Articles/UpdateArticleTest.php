<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UpdateArticleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_update_articles(): void
    {
  //      $this->withoutJsonApiDocumentFormatting();

        $article = Article::factory()->create();

        $response = $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Actualizado artículo',
            'slug' => 'actualizado-articulo',
            'content' => 'Contenido actualizado del artículo'
        ])->assertOk();;


        // La respuesta tendrá un header 'Location' con la ruta al artículo actualizado, según especificación json:api
        $response->assertHeader(
            'Location',
            route('api.v1.articles.show', $article)
        );

//        $response->assertExactJson([
//            'data' => [
//                'type' => 'articles',
//                'id' => (string) $article->getRouteKey(),
//                'attributes' => [
//                    'title' => (string) $article->title,
//                    'slug' => $article->slug,
//                    'content' => $article->content,
//                ],
//                'links' => [
//                    'self' => route('api.v1.articles.show', $article)
//                ]
//            ],
//        ]);

        //Test redundante, ejemplo de uso del objeto AssertableJson y sus métodos
        //Ver 'Fluent JSON Testing' en 'HTTP Test' de la documentación oficial
//        $response->assertJson(function (AssertableJson $json) use ($article) {
//            $json->has('data');
//            $json->hasAll(['data.attributes.title', 'data.attributes.slug']);
//            $json->hasAny(['data', 'data.attributes', 'title']);
//            $json->where('data.attributes.title', $article->title);
//            $json->whereNot('data.attributes.slug', $article->slug . 'KO');
//            $json->missing('atributo_no_existente');
//            $json->missingAll(['atributo_no_existente', 'otro']);
//            $json->etc();   // no entiendo su comportamiento, según indica la documentación
//        });
    }


    /** @test */
    public function title_is_required()
    {
        $article= Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del artículo'
        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function title_must_be_at_least_4_characters()
    {
        $article= Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'ABC',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del artículo'
        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required()
    {
        $article= Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Nuevo artículo',
            'content' => 'Contenido del artículo'
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required()
    {
        $article= Article::factory()->create();

        $this->patchJson(route('api.v1.articles.update', $article), [
            'title' => 'Nuevo artículo',
            'slug' => 'nuevo-articulo',
        ])->assertJsonApiValidationErrors('content');
    }

}
