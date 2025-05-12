<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;

    /**
     * マスアサイン可能な属性
     *
     * likes テーブルの item_id, user_id カラムに対応します。
     *
     * @var array
     */
    protected $fillable = [
        'item_id',
        'user_id',
    ];

    /**
     * このいいねは1つの商品に属する
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * このいいねは1人のユーザーに属する
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}