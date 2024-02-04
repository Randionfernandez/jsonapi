<?php

namespace Tests\Feature;

use App\Http\Middleware\ValidateJsonApiHeaders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ValidateJsonApiHeadersTest extends TestCase
{
    use RefreshDatabase;

    protected function setup(): void
    {
        parent::setup();
        Route::any('test_route', fn() => 'Ok')->middleware(ValidateJsonApiHeaders::class);
    }

    /** @test */
    public function accept_header_must_be_present_in_all_request(): void
    {
        $this->get('test_route')->assertStatus(406);

        // En la función 'get' el segundo parámetro contiene las cabeceras
        $this->get('test_route', [
            'accept' => 'application/vnd.api+json'
        ])->assertSuccessful();
    }

    /** @test */
    public function content_type_header_must_be_present_in_all_post_request(): void
    {
        // Sin cabecera 'accept' nos devolverá un estado 406
//        $this->post('test_route')->assertStatus(406);
        $this->post('test_route')->assertStatus(406);

        // Con la cabecera 'accept' pero sin la cabecera 'content-type' nos devolverá un estado 415
        // El tercer parámetro contiene las cabeceras.
        $this->post('test_route', [], [
            'accept' => 'application/vnd.api+json',
        ])->assertStatus(415);

        // con ambas cabeceras retornará un estado entre 200 y menor de 300
        $response = $this->post('test_route', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertSuccessful();
    }

    /** @test */
    public function content_type_header_must_be_present_in_all_patch_request(): void
    {
        $this->patch('test_route')->assertStatus(406);

        $this->patch('test_route', [], [
            'accept' => 'application/vnd.api+json',
        ])->assertStatus(415);

        $this->patch('test_route', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertSuccessful();
    }

    /** @test */
    public function content_type_header_must_be_present_in_all_responses(): void
    {
        $this->get('test_route', [
            'accept' => 'application/vnd.api+json',
        ])->assertHeader('content-type', 'application/vnd.api+json');

        $this->post('test_route', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertHeader('content-type', 'application/vnd.api+json');

        $response = $this->patch('test_route', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertHeader('content-type', 'application/vnd.api+json');

        $this->delete('test_route', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json',
        ])->assertHeader('content-type', 'application/vnd.api+json');
    }

    /** @test */
    public function content_type_header_must_not_be_present_in_empty_responses(): void
    {
        Route::any('empty_response', fn() => response()->noContent())
            ->middleware(ValidateJsonApiHeaders::class); // devuelve estado 204 No content

        $this->get('empty_response', [
            'accept' => 'application/vnd.api+json',
        ])->assertHeaderMissing('content-type');

        $this->post('empty_response', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json'
        ])->assertHeaderMissing('content-type');

        $this->patch('empty_response', [], [
            'accept' => 'application/vnd.api+json',
            'content-type' => 'application/vnd.api+json'
        ])->assertHeaderMissing('content-type');

        $this->delete('empty_response', [], [
            'accept' => 'application/vnd.api+json',
        ])->assertHeaderMissing('content-type');
    }
}
