<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Mail;

class UsersController extends Controller
{
    /* 过滤 */
    public function __construct()
    {
        $this->middleware('auth', [
            'except'    =>  ['show', 'create', 'store', 'index', 'confirmEmail']
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

        $this->sendEmailConfirmationTo($user);
        session()->flash('success', '验证邮件已发送到你的注册邮箱上，请注意查收。');
        return redirect('/');
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

    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        $from = 'jack@gmail.com';
        $name = 'Jack';
        $to = $user->email;
        $subject = "感谢注册 Weibo 应用！请确认你的邮箱。";

        Mail::send($view, $data, function ($message) use ($from, $name, $to, $subject) {
            $message->from($from, $name)->to($to)->subject($subject);
        });
    }


    /* 激活账户确认邮件 */
    public function confirmEmail($token)
    {
        $user = User::where('activation_token',$token)->firstOrFail();

        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        \Auth::login($user);
        session()->flash('success','恭喜你，激活成功！');
        return redirect()->route('users.show',[$user]);
    }
}
