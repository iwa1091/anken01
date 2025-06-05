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
    // 商品一覧表示機能（検索 + タブ切り替え含む）
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'recommend'); // 'recommend' or 'mylist'
        $query = $request->query('query', '');      // 検索キーワード
        $user = Auth::user();
        $items = collect();

        if ($tab === 'mylist') {
            // マイリストタブ：ログインかつメール認証済ユーザーのみ
            if ($user && $user->email_verified_at !== null) {
                $likedItemIds = Like::where('user_id', $user->id)->pluck('item_id');

                $items = Item::where(function ($q) use ($likedItemIds, $query) {
                    // いいね商品IDを含むか OR 検索キーワードに部分一致
                    $q->whereIn('id', $likedItemIds);
                    if ($query !== '') {
                        $q->orWhere('name', 'LIKE', "%{$query}%");
                    }
                })
                ->whereDoesntHave('exhibition', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->with('exhibition.user')
                ->get();
            }
        } else {
            // おすすめタブ（未ログインでもOK）
            $items = Item::when($query !== '', fn($q) => $q->where('name', 'LIKE', "%$query%"))
                ->when($user, function ($q) use ($user) {
                    $q->whereHas('exhibition', fn($subQ) => $subQ->where('user_id', '!=', $user->id));
                })
                ->with('exhibition.user')
                ->get();
        }

        // 購入済み商品情報をロード
        if ($items->isNotEmpty()) {
            $items->load('purchase');
        }

        // 各商品に is_sold, is_liked 属性を追加
        $items->each(function ($item) use ($user) {
            $item->is_sold = $item->isSold();
            $item->is_liked = $user
                ? Like::where('user_id', $user->id)->where('item_id', $item->id)->exists()
                : false;
        });

        return view('items.index', [
            'items' => $items,
            'activeTab' => $tab,
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

        $user = Auth::user();
        $itemWithComments->is_liked = $user
            ? Like::where('user_id', $user->id)->where('item_id', $item_id)->exists()
            : false;

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
         $item->brand = $request->input('product_brand');
        $item->save();

        return redirect()->route('items.index')->with('success', '商品をアップロードしました！');
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
