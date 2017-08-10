<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MonitorController extends Controller
{
    public $module = '';
    public $parent_module = 'parent_monitor';

    /**
     * 实时监控
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        return view('admin.monitor.index');
    }

    /**
     * 实时监控系统负载
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function real(Request $request){
        $sys = [
            'load'=>rand(0,100)
        ];
        return $this->successResponse($sys);
    }

}
