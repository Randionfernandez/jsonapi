<?php

namespace Tests\Feature;

use App\Http\Middleware\ValidateJsonApiDocument;
use App\Http\Middleware\ValidateJsonApiHeaders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\MakesJsonApiRequests;
use Tests\TestCase;

//use Illuminate\Foundation\Testing\WithFaker;

class ValidateJsonApiDocumentTest extends TestCase
{
    use RefreshDatabase;

    protected function setup(): void
    {
        parent::setup();

        $this->withoutJsonApiDocumentFormatting();

        Route::any('test_route', fn() => 'Ok')->middleware([
            ValidateJsonApiHeaders::class,
            ValidateJsonApiDocument::class,
        ]);
    }

    /** @test */
    public function data_is_required(): void
    {
        $this->postJson('test_route', []
        )->assertJsonApiValidationErrors('data');

        $this->patchJson('test_route', []
        )->assertJsonApiValidationErrors('data');
    }

    /** @test */
    public function data_must_be_an_array(): void
    {
        $this->postJson('test_route', [
            'data' => 'string',
            'type'=> 'string',
            'attributes'=>[
                'name'=>'test',
            ]
        ]
        )->assertJsonApiValidationErrors('data');

        $this->patchJson('test_route', [
            'data' => 'string',
            'type'=>'string',
            'attributes'=>[
                'name'=> 'test',
            ],
        ],
        )->assertJsonApiValidationErrors('data');
    }

    /** @test */
    public function data_type_is_required()
    {
        $this->postJson('test_route', [
                 'name' => 'test',
        ],
        )->assertJsonApiValidationErrors('data.type');

        $this->patchJson('test_route', [
            'data' => [
                'attributes' => [
                    'name' => 'test',
                ],
            ],
        ],
        )->assertJsonApiValidationErrors('data.type');
    }

    /** @test */
    public function data_type_is_an_string()
    {
        $this->postJson('test_route', [
            'data' => [
                'type' => 1,
                'attributes' => [
                    'name' => 'test'
                ],
            ],
        ],
        )->assertJsonApiValidationErrors('data.type');

        $this->patchJson('test_route', [
            'data' => [
                'type' => 1,
                'attributes' => [
                    'name' => 'test',
                ],
            ],
        ],
        )->assertJsonApiValidationErrors('data.type');
    }

    /** @test */
    public function data_attributes_is_required()
    {
        $this->postJson('test_route', [
            'data' => [
                'type' => 'string',
            ]
        ],
        )->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('test_route', [
            'data' => [
                'type' => 'string',
            ]
        ],
        )->assertJsonApiValidationErrors('data.attributes');
    }

    /** @test */
    public function data_attributes_must_be_an_array()
    {
        $this->postJson('test_route', [
            'data' => [
                'type' => 'string',
                'attributes' => 'string',
            ]
        ],
        )->assertJsonApiValidationErrors('data.attributes');

        $this->patchJson('test_route', [
            'data' => [
                'type' => 'string',
                'attributes' => 'string',
            ]
        ],
        )->assertJsonApiValidationErrors('data.attributes');
    }

    /** @test */
    public function data_id_is_required()
    {
        $this->patchJson('test_route', [
            'data' => [
                'type' => 'string',
                'attributes' => [
                    'name' => 'test',
                ]
            ]
        ],
        )->assertJsonApiValidationErrors('data.id');
    }

    /** @test */
    public function data_id_is_an_string()
    {
        $this->patchJson('test_route', [
            'data' => [
                'id' => 1,
                'type' => 'string',
                'attributes' => [
                    'name' => 'test',
                ]
            ]
        ],
        )->assertJsonApiValidationErrors('data.id');
    }

    /** @test */
    public function only_accepts_valid_json_api_document()
    {
        $this->postJson('test_route', [
            'data' => [
                'type' => 'string',
                'attributes' => [
                    'name' => 'test',
                ]
            ]
        ],
        )->assertSuccessful();

        $this->patchJson('test_route', [
            'data' => [
                'id' => '1',
                'type' => 'string',
                'attributes' => [
                    'name' => 'test',
                ]
            ]
        ],
        )->assertSuccessful();
    }
}
