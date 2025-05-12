<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    /**
     * マスアサイン可能な属性
     *
     * addresses テーブルの user_id, postal_code, address, building_name カラムに対応する
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'postal_code',
        'address',
        'building_name',
    ];

    public function getFormattedPostalCodeAttribute()
    {
        return $this->postal_code ? '〒' . substr($this->postal_code, 0, 3) . '-' . substr($this->postal_code, 3) : '未設定';
    }

    /**
     * この住所は1人のユーザーに属する
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}