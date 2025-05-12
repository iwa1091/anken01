<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exhibition extends Model
{
    use HasFactory;

    /**
     * マスアサイン可能な属性
     *
     * exhibitions テーブルの item_id, user_id カラムに対応します。
     *
     * @var array
     */
    protected $fillable = ['item_id', 'user_id', 'status'];

    /** 型キャスト */
   protected $casts = [
    'item_id' => 'integer',
    'user_id' => 'integer',
    ];



    /**
     * この出品情報は1つの商品に属する
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    /**
     * この出品情報は1人のユーザーに属する
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * item_categoryモデルファイル経由でExhibitionモデルファイルとCategoryモデルファイルを多対多の関係を構築
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'item_category', 'item_id', 'category_id');
    }


        protected static function boot()
    {
        parent::boot();

        static::saving(function ($exhibition) {
        \Log::info('Saving Exhibition:', [
            'item_id' => $exhibition->item_id,
            'user_id' => $exhibition->user_id,
            'status' => $exhibition->status ?? 'N/A',
        ]);
    });

    static::saved(function ($exhibition) {
        \Log::info('Saved Exhibition:', [
            'item_id' => $exhibition->item_id,
            'user_id' => $exhibition->user_id,
            'status' => $exhibition->status ?? 'N/A',
        ]);
    });
    }
}