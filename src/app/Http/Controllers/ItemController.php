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
        $query = $request->input('query');

        // 検索クエリで商品名部分一致検索
        $items = Item::where('name', 'LIKE', '%' . $query . '%')->get();

        return view('items.index', [
            'items' => $items,
            'activeTab' => 'search',
            'query' => $query,
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
        $itemWithComments = $this->getItemWithCommentsCount($item_id);
        $itemWithCategories = $this->getItemWithCategories($item_id);

        return view('items.detail', [
            'item' => $itemWithComments,
            'categories' => $itemWithCategories->categories,
        ]);
    }

    // 商品アップロード機能
    public function upload(ItemRequest $request)
    {
        $dir = 'images';
        $file_name = time() . '_' . uniqid() . '.' . $request->file('product_image')->getClientOriginalExtension();
        $request->file('product_image')->storeAs('public/' . $dir, $file_name);

        $item = new Item();
        $item->name = $request->input('product_name');
        $item->price = $request->input('product_price');
        $item->image = 'storage/' . $dir . '/' . $file_name;
        $item->description = $request->input('product_description');
        $item->save();

        return redirect()->route('items.index')->with('success', '商品をアップロードしました！');
    }

    // 商品一覧表示機能（トップ画面）
    // 商品一覧表示機能（トップ画面）
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend'); // デフォルトは 'recommend'
        $query = $request->query('query', '');     // 検索キーワード
        $user = Auth::user();

        if ($tab === 'mylist') {
            if ($user && $user->email_verified_at !== null) {
                // いいねした商品IDを取得
                $likedItemIds = Like::where('user_id', $user->id)->pluck('item_id');

                // いいね商品かつ検索条件に合致する商品を取得
                $items = Item::whereIn('id', $likedItemIds)
                    ->when($query !== '', function ($q) use ($query) {
                        $q->where('name', 'LIKE', '%' . $query . '%');
                    })
                    ->get();
            } else {
                $items = collect();
            }
        } else {
            if ($user) {
                // 自分が出品していない商品のみ取得
                $items = Item::whereHas('exhibition', function ($query) use ($user) {
                    $query->where('user_id', '!=', $user->id);
                })
                ->when($query !== '', function ($q) use ($query) {
                    $q->where('name', 'LIKE', '%' . $query . '%');
                })
                ->with('exhibition.user')
                ->get();
            } else {
                // 未認証ユーザーは全商品を検索条件付きで取得
                $items = Item::when($query !== '', function ($q) use ($query) {
                    $q->where('name', 'LIKE', '%' . $query . '%');
                })
                ->with('exhibition.user')
                ->get();
            }
        }

        // 購入済み判定を追加（空でなければ purchase を eager load）
        if ($items->isNotEmpty()) {
            $items->load('purchase');
        }

        // is_sold 属性を追加（ビューで使いやすく）
        $items->each(function ($item) {
            $item->is_sold = $item->isSold();
        });

        return view('items.index', [
            'items' => $items,
            'activeTab' => $tab,
            'query' => $query,
        ]);
    }


    // いいね機能
    public function like(Request $request, $item_id)
    {
        $user_id = Auth::id();

        $existing_like = Like::where('user_id', $user_id)->where('item_id', $item_id)->first();

        if ($existing_like) {
            $existing_like->delete();
            $message = 'いいねを解除しました';
        } else {
            Like::create([
                'user_id' => $user_id,
                'item_id' => $item_id,
            ]);
            $message = 'いいねしました';
        }

        return redirect()->back()->with('status', $message);
    }
}
