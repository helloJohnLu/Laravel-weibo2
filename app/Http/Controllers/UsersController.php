<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    // 用户注册页面 渲染
    public function create()
    {
        return view('users.create');
    }

    // 显示用户信息
    public function show(User $user)
    {
        return view('users.show',compact('user'));
    }
}
