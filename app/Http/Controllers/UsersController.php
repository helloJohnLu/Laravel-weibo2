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

    /* 处理用户注册表单提交 */
    public function store(Request $request)
    {
        /* 校验数据合法性 */
        $this->validate($request,[
            'name'      =>  'required|max:50|min:3',
            'email'     =>  'required|email|unique:users|max:255',
            'password'  =>  'required|confirmed|min:6'
        ]);

        /* 逻辑 */
        $user = User::create([
            'name'      =>  $request->name,
            'email'     =>  $request->email,
            'password'  =>  bcrypt($request->password),
        ]);

        /* 显示注册成功的提示信息 */
        session()->flash('success','欢迎，您将在这里开启一段新的旅程~');

        return redirect()->route('users.show',[$user]);
    }


    // 显示用户信息
    public function show(User $user)
    {
        return view('users.show',compact('user'));
    }
}
