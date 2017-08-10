<?php

namespace App\Http\Controllers\Admin;

use App\User;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AccountController extends Controller
{
    public $module = '';
    public $parent_module = 'parent_account';

    /**
     * 账号列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request){
        $users =  $request->session()->get('users');
        if($users->administrator == 0){
            return redirect()->back()->withInput()->withErrors('权限不足');
        }
        $name = $request->get('name', '');
        $accounts = User::where(function ($query) use ($name,$users) {
            if ($name)
                $query->where('name', 'like', '%' . $name . '%');
        })
            ->orderBy('created_at','desc')
            ->paginate();
        $search = $request->all();
        return view('admin.account.index', compact('accounts', 'search'));
    }

    /**
     * 创建管理员账号
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Request $request){
        $users =  $request->session()->get('users');
        if($users->administrator == 0){
            return redirect()->back()->withInput()->withErrors('权限不足');
        }
        if ($request->isMethod('post')) {

            $validator = Validator::make($request->all(), [
                'name' => 'required|unique:users',
                'password' => 'required|same:repassword',
                'repassword' => 'required',
            ], [
                'name.required' => '用户名必填',
                'name.unique' => '用户名已经被注册',
                'password.required' => '密码必填',
                'same' => '两次密码必须一致',
            ]);
            if ($validator->fails()) {
                return redirect('admin/account/create')
                    ->withErrors($validator)
                    ->withInput();
            }
            $options = $request->only('name', 'email', 'qq', 'mobile', 'remark');
            $options['password'] = bcrypt($request->get('password'));
            $options['created_at'] = date('Y-m-d H:i:s',time());
            $create_users = User::insertGetId($options);
            if($create_users){
                return redirect('admin/account')->with('result', '创建成功');
            }
            return redirect()->back()->withInput()->withErrors('创建失败');
        }
        return view('admin.account.create');
    }


    /**
     * 修改管理员账号
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail($id,Request $request){
        $users =  $request->session()->get('users');
        if($users->administrator == 0){
            return redirect()->back()->withInput()->withErrors('权限不足');
        }
        $user = User::find($id);
        if ($request->isMethod('post')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
            ], [
                'name.required' => '用户名必填',
            ]);
            if ($validator->fails()) {
                return redirect('portal/account/detail/'.$id)
                    ->withErrors($validator)
                    ->withInput();
            }
            $password = $request->get('password');
            if(! empty($password)){
                $repassword = $request->get('repassword');
                if($password != $repassword){
                    return redirect()->back()->withInput()->withErrors('重复密码不一致');
                }
                $user->password = bcrypt(trim($password));

            }
            $user->mobile = $request->get('mobile');
            $user->qq = $request->get('qq');
            $user->email = $request->get('email');
            $user->remark = $request->get('remark');
            $user->save();
            return redirect('admin/account/detail/' . $id)->with('result', '修改成功');


        }
        return view('admin.account.detail',compact('user'));
    }


    /**
     * 个人中心
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function personal(Request $request){
        $user=  $request->session()->get('users');

        return view('admin.account.personal',compact('user'));
    }

    /**
     * 删除子账号
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id,Request $request)
    {
        $users =  $request->session()->get('users');
        if($users->administrator == 0){
            return redirect()->back()->withInput()->withErrors('权限不足');
        }
        $account = User::find($id);
        if($account){
            $account->delete();
            return redirect('admin/account')->with('result', '删除子账号成功');
        }
        return redirect('admin/account')->with('result', '删除失败');
    }

}
