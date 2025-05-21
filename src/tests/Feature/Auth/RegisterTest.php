<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 正常に会員登録できる()
    {
        $response = $this->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/'); // 遷移先が違う場合は変更
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function 名前が未入力の場合は登録できない()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['name']);
        $this->assertGuest();
    }

    /** @test */
    public function メールアドレスが未入力の場合は登録できない()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /** @test */
    public function メールアドレスが重複している場合は登録できない()
    {
        User::factory()->create(['email' => 'duplicate@example.com']);

        $response = $this->from('/register')->post('/register', [
            'name' => '別のユーザー',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /** @test */
    public function パスワードが8文字未満の場合は登録できない()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    /** @test */
    public function パスワードと確認用パスワードが一致しない場合は登録できない()
    {
        $response = $this->from('/register')->post('/register', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }
}
