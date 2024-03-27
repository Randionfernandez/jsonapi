<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\PersonalAccessToken;
use Tests\TestCase;

class AccessTokenTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_issue_access_token(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $user = User::factory()->create();

        $data = $this->validCredentials([
            'email' => $user->email,
        ]);

        $response = $this->postJson(route('api.v1.login'), $data);

        $token = $response->json('plain-text-token');
        $dbToken = PersonalAccessToken::findToken($token);
        $this->assertTrue($dbToken->tokenable->is($user));
    }

    /** @test */
    public function password_must_be_valid(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $user = User::factory()->create();

        $data = $this->validCredentials([
            'email' => $user->email,
            'password' => 'incorrect',
        ]);

        $response = $this->postJson(route('api.v1.login'), $data);
        $response->assertJsonValidationErrorFor('password');

    }

    /** @test */
    public function user_must_be_registered(): void
    {
        $this->withoutJsonApiDocumentFormatting();

        $user = User::factory()->create();

        $data = $this->validCredentials([
            'email' => $user->email,
            'password' => 'incorrect',
        ]);

        $response = $this->postJson(route('api.v1.login'), $data);

        // En el repo original figura 'email' en lugar de 'password'
        // en consonancia el código de LoginController.php, pero que es un error,
        // pues se está validando la 'password' no el 'email'.
        $response->assertJsonValidationErrorFor('password');

    }

    protected function validCredentials(mixed $overrides = []): array
    {
        return array_merge([
            'email' => 'randion@cifpfbmoll.eu',
            'password' => 'password',
            'device_name' => 'My device',
        ], $overrides);
    }
}
