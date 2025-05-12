<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Like;
use App\Models\Item;
use App\Models\Purchase;

class ItemController extends Controller
{
    // 商品検索機能
    public function search(Request $request)
    {
        // 検索クエリを取得
        $query = $request->input('query');

        // 商品名で部分一致検索
        $items = Item::where('name', 'LIKE', '%' . $query . '%')->get(); // 全件取得に変更

        // ビューに検索結果を渡して表示
        return view('items.index', [
            'items' => $items,
            'activeTab' => 'search', // 検索結果タブを表示するためのフラグ
        ]);
    }

    // 商品詳細情報取得機能（コメント数）
    public function getItemWithCommentsCount($item_id)
    {
        return Item::withCount('comments')->findOrFail($item_id);
    }

    // 商品詳細情報取得機能（カテゴリ情報）
    public function getItemWithCategories($item_id)
    {
        return Item::with('categories')->findOrFail($item_id);
    }

    // 商品詳細画面表示
    public function detail($item_id)
    {
        // 商品情報の取得（コメント数＆カテゴリの情報を個別に取得）
        $itemWithComments = $this->getItemWithCommentsCount($item_id);
        $itemWithCategories = $this->getItemWithCategories($item_id);

        // ビューへデータを渡す
        return view('items.detail', [
            'item' => $itemWithComments, // コメント数を含む商品情報
            'categories' => $itemWithCategories->categories, // 商品カテゴリ情報
        ]);
    }

    // 商品アップロード機能
    public function upload(ItemRequest $request)
    {
        // アップロード先ディレクトリ
        $dir = 'images';

        // ファイル名を一意にする（タイムスタンプ＋ランダム文字列）
        $file_name = time() . '_' . uniqid() . '.' . $request->file('product_image')->getClientOriginalExtension();

        // ファイルを指定のディレクトリに保存
        $request->file('product_image')->storeAs('public/' . $dir, $file_name);

        // 新しい商品データを作成
        $item = new Item();
        $item->name = $request->input('product_name');
        $item->price = $request->input('product_price');
        $item->image = 'storage/' . $dir . '/' . $file_name; // ファイルパス
        $item->description = $request->input('product_description');
        $item->save();

        // 成功レスポンスを返却
        return redirect()->route('items.index')->with('success', '商品をアップロードしました！');
    }

    // 商品一覧表示機能（トップ画面）
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend'); // デフォルトは 'recommend'
        
        if ($tab === 'mylist' && Auth::check()) {
            // ログインユーザーが購入した商品を取得
            $user_id = Auth::id();
            $purchasedItemIds = Purchase::where('user_id', $user_id)->pluck('item_id');
            $items = Item::whereIn('id', $purchasedItemIds)->get();
        } else {
            $items = Item::all(); // おすすめ（全件）を表示
        }

        return view('items.index', [
            'items' => $items,
            'activeTab' => $tab, // アクティブなタブ（おすすめやマイリスト）を設定
        ]);
    }

    // おすすめ商品表示機能（全商品表示に変更）
    public function recommend()
    {
        // すべての商品を取得
        $items = Item::all(); // 全件取得に変更

        // ビューに商品のデータを渡して表示
        return view('items.index', [
            'items' => $items,
            'activeTab' => 'recommend', // おすすめタブを表示するためのフラグ
        ]);
    }

    // いいね機能
    public function like(Request $request, $item_id)
    {
        // ログインユーザーのIDを取得
        $user_id = Auth::id();

        // 既に（いいね）をしているかチェック
        $existing_like = Like::where('user_id', $user_id)->where('item_id', $item_id)->first();

        if ($existing_like) {
            // 既にいいね済みの場合は、解除する
            $existing_like->delete();
            $message = 'いいねを解除しました';
        } else {
            // いいねを新規登録する
            Like::create([
                'user_id' => $user_id,
                'item_id' => $item_id,
            ]);
            $message = 'いいねしました';
        }

        return redirect()->back()->with('status', $message);
    }
}
