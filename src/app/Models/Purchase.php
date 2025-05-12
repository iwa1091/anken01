<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    /**
     * モデルに対してマスアサイン可能な属性
     *
     * ここでは、ユーザーID、商品ID、配送先住所ID、支払い方法、支払い状況、合計金額など、
     * 購入時に必要な情報を例示しています。設計書に合わせて必要な属性を追加してください。
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'item_id',
        'address_id',
        'payment_method',
        'payment_status',
        'total_price',
    ];

    /**
     * 購入情報は1人のユーザーに属する
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * 購入情報は1つの商品に属する
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * 購入情報は配送先の住所情報に属する
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}