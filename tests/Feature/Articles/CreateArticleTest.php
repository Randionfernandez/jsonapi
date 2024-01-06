<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Testing\TestResponse;
use Tests\MakesJsonApiRequests;
use Tests\TestCase;


class CreateArticleTest extends TestCase
{
    use RefreshDatabase, MakesJsonApiRequests;

    /**
     * @test
     */
    public function can_create_articles(): void
    {
        $this->withoutExceptionHandling();

        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'Nuevo artículo',
                    'slug' => 'nuevo-articulo',
                    'content' => 'Contenido del artículo'
                ]
            ]
        ]);

        $article = Article::first();

        // La respuesta tendrá un header 'Location' con la ruta al artículo creado, según especificación json:api
        $response->assertHeader(
            'Location',
            route('api.v1.articles.show', $article)
        );

        $response->assertStatus(201);  // assertCreated() es un alias de este assert
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
        $response->assertJson(function (AssertableJson $json) use ($article) {
            $json->has('data');
            $json->hasAll(['data.attributes.title', 'data.attributes.slug']);
            $json->hasAny(['data', 'data.attributes', 'title']);
            $json->where('data.attributes.title', $article->title);
            $json->whereNot('data.attributes.slug', $article->slug . 'KO');
            $json->missing('atributo_no_existente');
            $json->missingAll(['atributo_no_existente', 'otro']);
            $json->etc();   // no entiendo su comportamiento, según indica la documentación
        });
    }


    // PROBANDO ERRORES DE VALIDACIÓN

    /** @test */
    public function title_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
//                    'title' => 'Nuevo artículo',
                    'slug' => 'nuevo-articulo',
                    'content' => 'Contenido del artículo'
                ]
            ]
        ]);

        $response->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required()
    {
        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'Nuevo artículo',
//                    'slug' => 'nuevo-articulo',
                    'content' => 'Contenido del artículo'
                ]
            ]
        ]);

        $response->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required()
    {

        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'Nuevo artículo',
                    'slug' => 'nuevo-articulo',
//                    'content' => 'Contenido del artículo'
                ]
            ]
        ]);

        $response->assertJsonApiValidationErrors('content');
    }

    /** @test */
    public function title_must_be_at_least_4_characters()
    {

        $response = $this->postJson(route('api.v1.articles.create'), [
            'data' => [
                'type' => 'articles',
                'attributes' => [
                    'title' => 'ABC',
                    'slug' => 'nuevo-articulo',
                    'content' => 'Contenido del artículo'
                ]
            ]
        ]);

        $response->assertJsonApiValidationErrors('title');
    }
}
