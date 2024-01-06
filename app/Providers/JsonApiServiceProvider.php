<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Testing\TestResponse;

class JsonApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        TestResponse::macro('assertJsonApiValidationErrors', function ($attribute) {
            /** @var TestResponse $this */
            $this->assertJsonStructure([
                'errors' => [
                    ['title', 'detail', 'source' => ['pointer']]
                ]
            ])->assertJsonFragment([
                'source' => ['pointer' => "/data/attributes/{$attribute}"]
            ])->assertHeader(
                'content-type', 'application/vnd.api+json'
            )->assertStatus(422);
        });
    }
}
