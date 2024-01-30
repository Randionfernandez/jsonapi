<?php
namespace App\Providers;

// use Illuminate\Database\Query\Builder;
use App\JsonApi\JsonApiQueryBuilder;
use App\JsonApi\JsonApiTestResponse;
use Illuminate\Database\Eloquent\Builder;
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
        TestResponse::mixin(new JsonApiTestResponse());
        Builder::mixin(new JsonApiQueryBuilder());
    }
}
