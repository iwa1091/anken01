<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * マスアサイン可能な属性
     *
     * categories テーブルの name カラムに対応します。
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * カテゴリと商品の多対多リレーション
     *
     * 中間テーブル item_category を利用して、商品（Item）との関連付けを行います。
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_category');
    }
}