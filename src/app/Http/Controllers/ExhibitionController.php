<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreExhibitionRequest;
use App\Models\Exhibition;
use App\Models\Category;
use App\Models\Item;
use App\Models\Purchase;

class ExhibitionController extends Controller
{
    // マイページ（出品済商品・購入済商品一覧）
    public function mypage()
    {
        $listedItems = Exhibition::where('user_id', auth()->id())->with('item')->get();
        $purchasedItems = Purchase::where('user_id', auth()->id())->with('item')->get();

        return view('mypage', compact('listedItems', 'purchasedItems'));
    }

    // 出品ページの表示
    public function create()
    {
        $categories = Category::all();
        $itemId = session('selected_item_id');
        $item = $itemId ? Item::find($itemId) : Item::latest()->first();

        return view('exhibition.show', compact('categories', 'item'));
    }

    // 商品出品情報の保存
    public function store(StoreExhibitionRequest $request)
    {
        $validatedData = $request->validated();

        // 画像アップロード処理
        if ($request->hasFile('image')) {
            $filePath = $request->file('image')->store('images', 'public');
            $validatedData['image'] = str_replace('public/', 'storage/', $filePath);
        }

        $itemId = $request->input('item_id');
        $item = Item::find($itemId);

        if (!$item) {
            $item = Item::create([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'price' => $validatedData['price'],
                'img_url' => $validatedData['image'],
                'condition' => $validatedData['condition'], 
            ]);
            $itemId = $item->id;
        }

        // アイテム情報更新
        $item->update([
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
            'price' => $validatedData['price'],
            'img_url' => $validatedData['image'],
            'condition' => $validatedData['condition'],
        ]);

        // 出品情報の登録
        $exhibition = new Exhibition();
        $exhibition->user_id = auth()->id();
        $exhibition->item_id = $item->id;
        $exhibition->save();

        // カテゴリの紐付け
        $categoryIds = array_map('intval', (array) $validatedData['category']);
        $item->categories()->sync($categoryIds);

        return redirect()->route('mypage.profile')->with('success', '商品を出品しました！');
    }

    // 商品検索機能
    public function search(Request $request)
    {
        $query = $request->input('query');

        $results = Exhibition::whereHas('item', function ($q) use ($query) {
            $q->where('name', 'LIKE', "%{$query}%")
              ->orWhere('description', 'LIKE', "%{$query}%");
        })->with('item')->get();

        return view('exhibition.search', compact('results', 'query'));
    }
}
