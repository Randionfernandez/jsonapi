<?php

namespace Tests\Feature\Articles;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaginateArticlesTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_paginate_articles(): void
    {
        // Paso 1: Creamos 6 articles usando factories
        $articles = Article::factory(6)->create();

        // Paso 2: obtenemos la url de la petición cumpliendo json:api
        $url = route('api.v1.articles.index', ['page' => [
            'size' => 2,
            'number' => 2
        ]]);
//        dd(urldecode($url));   //Muestra la ruta obtenida y aborta el script

        // Paso 3: Creamos la petición get, solo necesita pasarle la url, recordemos que las cabeceras
        // se añaden automáticamente gracias al trait MakesJsonApiRequests
        $response = $this->getJson($url);

        // El array de articles comienza en 0. Luego com size=2 y number=2 deberíamos obtener los artículos
        // 2 y 3. No deberíamos ver los artículo 0, 1, 4 y 5
        $response->assertSee([
            $articles[2]->title,
            $articles[3]->title,
        ]);
        $response->assertDontSee([
            $articles[0]->title,
            $articles[1]->title,
            $articles[4]->title,
            $articles[5]->title,
        ]);
        //Paso 4
        // La estructura de la respuesta incluirá los links indicados a continuación.
        //Podemos comprobar esto haciendo un
        //dd($response);
        $response->assertJsonStructure([
            'links' => ['first', 'last', 'prev', 'next']
        ]);

        // Paso 5. Guardamos en variable el string asociado al primer enlace,
        // y lo mismo con los otros tres que también deben figurar en la respuesta
        $firstLink = urldecode($response->json('links.first'));
        $lastLink = urldecode($response->json('links.last'));
        $prevLink = urldecode($response->json('links.prev'));
        $nextLink = urldecode($response->json('links.next'));

        // En los enlaces, segundo parámetro, debe encontrarse las cadenas del primer parámetro
        $this->assertStringContainsString('page[size]=2', $firstLink);
        $this->assertStringContainsString('page[number]=1', $firstLink);

        $this->assertStringContainsString('page[size]=2', $lastLink);
        $this->assertStringContainsString('page[number]=3', $lastLink);

        $this->assertStringContainsString('page[size]=2', $prevLink);
        $this->assertStringContainsString('page[number]=1', $prevLink);

        $this->assertStringContainsString('page[size]=2', $nextLink);
        $this->assertStringContainsString('page[number]=3', $nextLink);
    }

    /** @test */
    public function can_paginate_sorted_articles(): void
    {
        // Paso 1: creamos 3 artículos con los factory de modo que podamos comprobar posteriormente la
        // ordenación
        Article::factory()->create(['title' => "C title"]);
        Article::factory()->create(['title' => "A title"]);
        Article::factory()->create(['title' => "B title"]);
// Paso 2. Definimos la ruta con los parámetros correspondientes
        $url = route('api.v1.articles.index', [
            'sort' => 'title',
            'page' => [
                'size' => 1,
                'number' => 2
            ]]);

//        dd(urldecode($url)); // Inspeccionamos la variable, que debería contener el string
        //          articles?sort=title&page['size']=1&page['number']=2
        // según especificación json:qpi

        // Paso 3. Creamos la petición. Las cabeceras las añade el trait MakesJsonApiRequests
        $response = $this->getJson($url);

        // Hacemos las comprobaciones (asserts) siguientes

        // Pedimos que ordene la respuesta y no devuelva la página 2, siendo el tamaño de la
        // página 1, por tanto, queremos el segundo artículo de la respuesta completa
        //  C, A, B  -->  respuesta ordenada por title será A, B, C y el que vendrá
        // en la respuesta paginada será 'B title'
        $response->assertSee([
            'B title',
        ]);
        // Y no estará en la respuesta los artículos A ni C
        $response->assertDontSee([
            'A title',
            'C title',
        ]);

        $firstLink = urldecode($response->json('links.first'));
        $lastLink = urldecode($response->json('links.last'));
        $prevLink = urldecode($response->json('links.prev'));
        $nextLink = urldecode($response->json('links.next'));

        // comprobamos que en estos cuatro enlaces aparece el parámetro 'sort=title'
        // Los parámetros page['size'] y page['number'] se comprobaron en el método anterior
        $this->assertStringContainsString('sort=title', $firstLink);
        $this->assertStringContainsString('sort=title', $lastLink);
        $this->assertStringContainsString('sort=title', $prevLink);
        $this->assertStringContainsString('sort=title', $nextLink);
    }

    /** @test */
    public function can_paginate_filtered_articles(): void
    {
        Article::factory(3)->create();
        // Paso 1: creamos 3 artículos con los factory de modo que podamos comprobar posteriormente la
        // ordenación
        Article::factory()->create(['title' => "C Laravel"]);
        Article::factory()->create(['title' => "A Laravel"]);
        Article::factory()->create(['title' => "B Laravel"]);
// Paso 2. Definimos la ruta con los parámetros correspondientes
        $url = route('api.v1.articles.index', [
            'filter[title]' => 'laravel',
            'page' => [
                'size' => 1,
                'number' => 2
            ]]);

//        dd(urldecode($url)); // Inspeccionamos la variable, que debería contener el string
        //          articles?filter[title]=laravel&page['size']=1&page['number']=2
        // según especificación json:qpi

        // Paso 3. Creamos la petición. Las cabeceras las añade el trait MakesJsonApiRequests
        $response = $this->getJson($url);

        // Hacemos las comprobaciones (asserts) siguientes
        $firstLink = urldecode($response->json('links.first'));
        $lastLink = urldecode($response->json('links.last'));
        $prevLink = urldecode($response->json('links.prev'));
        $nextLink = urldecode($response->json('links.next'));

        // comprobamos que en estos cuatro enlaces aparece el parámetro 'sort=title'
        // Los parámetros page['size'] y page['number'] se comprobaron en el método anterior
        $this->assertStringContainsString('filter[title]=laravel', $firstLink);
        $this->assertStringContainsString('filter[title]=laravel', $lastLink);
        $this->assertStringContainsString('filter[title]=laravel', $prevLink);
        $this->assertStringContainsString('filter[title]=laravel', $nextLink);
    }
}
