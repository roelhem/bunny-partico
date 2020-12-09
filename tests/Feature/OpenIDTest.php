<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\OpenIDSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Client;
use Laravel\Passport\ClientRepository;
use Laravel\Passport\Passport;
use Tests\TestCase;

class OpenIDTest extends TestCase
{

    # http://partico.test/oauth/authorize?client_id=92343157-e40a-40d4-869f-6940c5e37d3b&redirect_uri=http://partico.test/auth/callback&response_type=code&scope=openid&state=ABCDEFGHIJKLMNOP

    // http://partico.test/oauth/token?grant_type=authorization_code&client_id=92343157-e40a-40d4-869f-6940c5e37d3b
    // 78b935b76eea6c77e2659af1e91c730db8cec816e8b8df29f801a172c813d1a6a5b8474ece58cc0a

    use RefreshDatabase;

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function testCanGetOpenIDToken()
    {
        /** @var User $user */
        $user = User::factory()->createOne([
            'name' => 'test',
        ]);

        /** @var Client $client */
        $client = Passport::client()::factory()->createOne([
            'name' => 'Test Client',
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
        ]);

        $authCode = $client->authCodes()->create([
            'id' => 'ABC',
            'user_id' => $user->id,
            'scopes' => 'openid',
            'revoked' => false,
        ]);

        $response = $this->post('/oauth/token', [
            'grand_type' => 'authorization_code',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'redirect_uri' => $client->redirect,
            'code' => $authCode->code,
        ]);

        dd($response->content());

        $response->assertStatus(201);
    }
}
