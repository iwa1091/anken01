<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmailVerificationPromptController extends Controller
{
    /**
     * メール認証待ち画面の表示
     */
    public function __invoke(Request $request)
    {
        return view('auth.verify-email'); // メール認証画面 (verify-email.blade.php) を表示
    }
}