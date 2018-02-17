<?php

namespace App\Models;

use App\Notifications\ResetPassword;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /* Gravatar 用户头像 */
    public function gravatar($size = 100)
    {
        /* 通过将用户的 Gravatar 登录邮箱进行 MD5 转码，并与 Gravatar 的 URL 进行拼接来获取 Gravatar 头像。 */
        $hash = md5(strtolower(trim($this->attributes['email'])));

        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    /* 用户激活令牌 监听 */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->activation_token = str_random(30);
        });
    }

    /* 重设密码邮件 */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /* 一个用户拥有多条微博，一对多 */
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }


    /* 取出当前用户发布过的所有微博 */
    public function feed()
    {
        return $this->statuses()->orderBy('created_at','desc');
    }
}
