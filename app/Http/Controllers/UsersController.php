<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /* 过滤 */
    public function __construct()
    {
        $this->middleware('auth', [
            'except'    =>  ['show', 'create', 'store', 'index']
        ]);

        $this->middleware('guest', [
            'only' => ['create']
        ]);
    }

    /* 用户列表 */
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index',compact('users'));
    }


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

        \Auth::login($user);    // 用户注册后自动登录

        /* 显示注册成功的提示信息 */
        session()->flash('success','欢迎，您将在这里开启一段新的旅程~');

        return redirect()->route('users.show',[$user]);
    }

    // 显示用户信息
    public function show(User $user)
    {
        return view('users.show',compact('user'));
    }

    /* 渲染编辑表单 */
    public function edit(User $user)
    {
        /* 授权 */
        $this->authorize('update', $user);
        return view('users.edit',compact('user'));
    }

    /* 处理用户更新 */
    public function update(User $user, Request $request)
    {
        /* 校验数据合法性 */
        $this->validate($request,[
            'name'      =>  'required|max:50|min:3',
            'password'  =>  'nullable|confirmed|min:6'
        ]);

        /* 授权验证 */
        $this->authorize('update', $user);

        /* 更新逻辑 */
        $data = [];
        $data['name'] = $request->name;
        if ($request->password) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        session()->flash('success','个人资料更新成功！');

        return redirect()->route('users.show', $user->id);
    }

    /* 删除用户 */
    public function destroy (User $user)
    {
        $this->authorize('destroy',$user);
        $user->delete();
        session()->flash('success', '成功删除用户！');
        return back();
    }
}
