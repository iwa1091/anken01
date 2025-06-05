<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * ID1: 正常な会員登録ができるかテスト
     */
    public function test_user_can_register_with_valid_data(): void
    {
        // 登録用データ
        $formData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // POSTで登録リクエストを送信
        $response = $this->post('/register', $formData);

        // ユーザがDBに登録されていることを確認
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);

        // 認証されていること（ログインされていること）を確認
        $this->assertAuthenticated();

        // リダイレクト先を確認（ホームなどにリダイレクトされる）
        $response->assertRedirect('/email/verify');
    }

        public function test_name_is_required(): void
    {
        $formData = [
            'name' => '',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $formData);

        $response->assertSessionHasErrors([
            'name' => 'お名前を入力してください',
        ]);
    }


    public function test_email_is_required(): void
    {
        $formData = [
            'name' => 'テストユーザー',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->post('/register', $formData);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスを入力してください',
        ]);
    }

    public function test_password_is_required(): void
    {
        $formData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => '',
            'password_confirmation' => '',
        ];

        $response = $this->post('/register', $formData);

        $response->assertSessionHasErrors([
            'password' => 'パスワードを入力してください',
        ]);
    }

    public function test_password_min_length(): void
    {
        $formData = [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'abc123',
            'password_confirmation' => 'abc123',
        ];

        $response = $this->post('/register', $formData);

        $response->assertSessionHasErrors([
            'password' => 'パスワードは8文字以上で入力してください',
        ]);
    }
    public function test_password_confirmation_mismatch(): void
{
    $formData = [
        'name' => 'テストユーザー',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different123',
    ];

    $response = $this->post('/register', $formData);

    $response->assertSessionHasErrors([
        'password' => 'パスワードと一致しません',
    ]);
}

}
