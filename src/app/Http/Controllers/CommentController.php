<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest; // CommentRequest をインポート
use Illuminate\Http\Request;
use App\Models\Comment;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * 商品へのコメントを投稿
     */
    public function store(CommentRequest $request, $item_id) // CommentRequest を利用
    {
        // ログインユーザーの ID を取得
        $user_id = Auth::id();

        // 商品の存在チェック
        $item = Item::findOrFail($item_id);

        // コメントを新規登録
        Comment::create([
            'user_id' => $user_id,
            'item_id' => $item_id,
            'content' => $request->input('content'),
        ]);

        // 商品詳細ページへリダイレクト（コメント数が増加表示されるか確認）
        return redirect()->route('items.detail', ['item_id' => $item_id])
            ->with('success', 'コメントを投稿しました！');
    }
}