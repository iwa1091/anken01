<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    /**
     * モデルに対してマスアサイン可能な属性
     *
     * ここには設計書に基づく商品情報のカラムを定義します。
     * 例として、商品名、商品説明、価格、画像のURLなどを入れています。
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'price',
        'img_url',
        'condition',
    ];


    /**
     * このアイテムを出品している出品情報との1対1のリレーション
     *
     * 例: 出品中の商品の場合、出品情報（Exhibition）と関連づけています。
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function exhibition()
    {
        return $this->hasOne(Exhibition::class);
    }


    /**
     * このアイテムを購入している出品情報との1対1のリレーション
     *
     * 例: 購入した購入した商品の場合、商品が購入されたデータ（Purchase）と関連づけています。
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    public function isSold()
    {
        return $this->purchase()->exists();
    }

    /**
     * アイテムに対するコメントとの1対多のリレーション
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * アイテムに対する「いいね」情報との1対多のリレーション
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    /**
     * アイテムとカテゴリとの多対多のリレーション
     *
     * 中間テーブルは item_categories を使用します。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'item_categories', 'item_id', 'category_id');
    }


}