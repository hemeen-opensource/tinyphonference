<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class IndexController extends Controller
{

    public $module = '';
    public $parent_module = 'parent_index';

    /**
     * 总后台首页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        return view('admin.index');
    }


    /**
     * 登录
     * @param Request $request
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function login(Request $request){
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'password' => 'required',
                'code' => 'required|captcha',
            ], [
                'name.required' => '请输入用户名',
                'code.required' => '请输入图形验证码',
                'code.captcha' => '图形验证码错误',
            ]);



            if ($validator->fails()) {
                return redirect('')
                    ->withErrors($validator)
                    ->withInput();
            }
            $user = User::where('name',$request->get('name'))->first();
            if($user){
                $pass = password_verify($request->get('password'),$user->password);
                Log::Info("輸入密碼：" .$request->get('password') . "验证结果: " . $pass);
                if($pass){
                    // 更新登录信息
                    $user->last_login_time =$user->login_time;
                    $user->last_ip = $user->ip;
                    $user->login_time = date('Y-m-d H:i:s',time());
                    $user->ip = $request->getClientIp();
                    $user->save();
                    $administrator = $user->administrator ? $user->administrator : 0;
                    Session::put('users', $user);
                    Session::put('administrator', $administrator);
                    Session::put('name', $user->name);
                    return redirect('admin')->with('result', '登录成功');
                }
            }
            return redirect()->back()->withInput()->withErrors('用户名或密码有误');
        }
        return view('admin.login');
    }

    /**
     * 退出系统
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(){
        Session::flush();
        return redirect()->action('Admin\IndexController@login');
    }

    /**
     * 重设密码可通过此方法获取加密串 然后手动覆盖数据即可
     * @param $password
     */
    public function encrypt($password){
        // 通过bcrypt方法加密
        $hash_password = bcrypt($password);
        echo "加密后hash值为：". $hash_password;

        echo "<br/>";
        // 验证密码是否正确
        $pass = password_verify($password,$hash_password);
        if($pass){
            echo "密码正确";
        }else{
            echo "密码不正确";
        }
    }




}
