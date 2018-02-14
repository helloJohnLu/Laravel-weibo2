<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SessionsController extends Controller
{
    /**
     * 渲染登录表单
     *
     * @return
     */
    public function create()
    {
        return view('sessions.create');
    }

    /**
     * 处理用户登录
     *
     * @param Request $request
     * @return
     */
    public function store(Request $request)
    {
        /* 校验数据合法性 */
        $credentials = $this->validate($request,[
            'email'     =>  'required|email|max:255',
            'password'  =>  'required'
        ]);

        /* 逻辑 */
        if (\Auth::attempt($credentials, $request->has('remember'))) {
            session()->flash('success', '欢迎回来！');
            return redirect()->intended(route('users.show',[\Auth::user()]));
        }else{
            /* 消息提示和页面重定向 */
            session()->flash('danger','很抱歉，您的邮箱与密码不匹配');
            return redirect()->back();
        }
    }

    /**
     * 退出登录
     *
     * @return
     */
    public function destroy()
    {
        \Auth::logout();
        session()->flash('success', '您已成功退出！');
        return redirect('login');
    }
}
