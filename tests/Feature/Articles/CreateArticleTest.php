<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;


class CreateArticleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function can_create_articles(): void
    {
//        $this->withoutExceptionHandling();

        $response = $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo artículo',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del artículo'
        ])->assertCreated();

        $article = Article::first();

        // La respuesta tendrá un header 'Location' con la ruta al artículo creado, según especificación json:api
        $response->assertHeader(
            'Location',
            route('api.v1.articles.show', $article)
        );

        $response->assertStatus(201);  // assertCreated() es un alias de este assert

        $response->assertExactJson([
            'data' => [
                'type' => 'articles',
                'id' => (string) $article->getRouteKey(),
                'attributes' => [
                    'title' => (string) $article->title,
                    'slug' => $article->slug,
                    'content' => $article->content,
                ],
                'links' => [
                    'self' => route('api.v1.articles.show', $article)
                ]
            ],
        ]);

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
        $this->postJson(route('api.v1.articles.store'), [
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del artículo'
        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function title_must_be_at_least_4_characters()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'ABC',
            'slug' => 'nuevo-articulo',
            'content' => 'Contenido del artículo'
        ])->assertJsonApiValidationErrors('title');
    }

    /** @test */
    public function slug_is_required()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo artículo',
            'content' => 'Contenido del artículo'
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_be_unique()
    {
        $article= Article::factory()->create();

        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo artículo',
            'slug' => $article->slug,  // Probamos guardar un artículo con un 'slug' existente.
            'content' => 'Contenido del artículo'
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_only_must_contain_numbers_letters_and_dashes()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo artículo',
            'slug' => '$%^&',  // Caracteres no permitidos.
            'content' => 'Contenido del artículo'
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_contain_underscores()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo artículo',
            'slug' => 'with_underscore',  // Guion bajo no está permitido.
            'content' => 'Contenido del artículo'
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_start_with_dashes()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo artículo',
            'slug' => '-start-with-dashes',  // Guion bajo no está permitido.
            'content' => 'Contenido del artículo'
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_end_with_dashes()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo artículo',
            'slug' => 'start-with-dashes-',  // Guion bajo no está permitido.
            'content' => 'Contenido del artículo'
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function slug_must_not_contain_spaces()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo artículo',
            // los espacios al principio y al final de un string son eliminados por el middleware global TrimStrings
            'slug' => 'star t-with-dashes',  // espacios no están permitidos.
            'content' => 'Contenido del artículo'
        ])->assertJsonApiValidationErrors('slug');
    }

    /** @test */
    public function content_is_required()
    {
        $this->postJson(route('api.v1.articles.store'), [
            'title' => 'Nuevo artículo',
            'slug' => 'nuevo-articulo',
        ])->assertJsonApiValidationErrors('content');
    }



}
