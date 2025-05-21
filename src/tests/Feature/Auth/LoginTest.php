<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_正常にログインできる()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/'); // ログイン後の遷移先に応じて修正
        $this->assertAuthenticatedAs($user);
    }

    public function test_誤ったパスワードではログインできない()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_存在しないユーザーではログインできない()
    {
        $response = $this->post('/login', [
            'email' => 'notfound@example.com',
            'password' => 'any-password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }
}
