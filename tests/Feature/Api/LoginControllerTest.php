<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;

use Symfony\Component\HttpFoundation\Response as Status;

use App\Models\User;

use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    private string $url = '/api/login';
    /**
     * A basic feature test example.
     */
    public function test_login(): void
    {
        // user we want to login as
        $user = User::factory()->create();
        Log::debug(print_r($user, true));


        // wrong req type
        $resp = $this->getJson($this->url);
        $resp->assertStatus(Status::HTTP_METHOD_NOT_ALLOWED);

        // missing params
        $params = ['username' => $user->username];
        $resp = $this->postJson($this->url, $params);
        $resp->assertStatus(Status::HTTP_UNPROCESSABLE_ENTITY);
        $params = ['password' => $user->username];
        $resp = $this->postJson($this->url, $params);
        $resp->assertStatus(Status::HTTP_UNPROCESSABLE_ENTITY);

        // unknown user
        $params = ['username' => $user->username . '1',
                   'password' => $user->username];
        $resp = $this->postJson($this->url, $params);
        $resp->assertStatus(Status::HTTP_UNAUTHORIZED);

        // wrong password
        $params = ['username' => $user->username,
                   'password' => $user->username . '1'];
        $resp = $this->postJson($this->url, $params);
        $resp->assertStatus(Status::HTTP_UNAUTHORIZED);

        // all good
        $params = ['username' => $user->username,
                   'password' => $user->username];
        $resp = $this->postJson($this->url, $params);
        $resp->assertStatus(Status::HTTP_OK);
    }
}
