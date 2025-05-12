<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /** 
     * マスアサイン可能な属性
     * 
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_image', // 追加
    ];

    /** 
     * 配列や JSON に変換する際に非表示とする属性
     * 
     * @var string[]
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** 
     * キャストする属性の定義
     * 
     * @var array<string,string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /** 
     * ユーザーは複数の出品情報（Exhibitions）を持つ
     */
    public function exhibitions()
    {
        return $this->hasMany(Exhibition::class);
    }

    /** 
     * ユーザーは複数の購入情報（Purchases）を行う
     */
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }


    /** 
     * ユーザーは複数の住所（Addresses）を持つ
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /** 
     * ユーザーは複数のコメント（Comments）を投稿する
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /** 
     * ユーザーは複数のいいね（Likes）を行う
     */
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    /** 
     * ユーザーが購入した商品の総額を取得
     */
    public function getTotalPurchaseAmount()
    {
        return $this->purchases->sum('amount');
    }

    /** 
     * ユーザーのプロフィールを更新するメソッド（便利メソッド）
     * @param array $data
     * @return bool
     */
    public function updateProfile(array $data)
    {
        return $this->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'profile_image' => $data['profile_image'] ?? $this->profile_image, // 追加
        ]);
    }

        /**  
     * ユーザーは複数の商品（Items）を持つ  
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }
}