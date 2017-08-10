<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public $module;
    public $parent_module;
    public function __construct()
    {
        View::share('active',[$this->module=>'active']);
        View::share('parent_active',[$this->parent_module=>'active']);
    }


    /**
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function errorResponse($message = "不能为空", $status = 406) {
        $response = array(
            'success'=>false,
            'message' => $message,
            'status_code' => $status,
            'data'=> array(),
        );
        return json_encode($response);
    }

    /**
     * @param array $data
     * @param string $message
     * @param int $status
     * @return \Illuminate\Http\JsonResponse
     */
    protected function successResponse($data = array() ,$message = "请求成功", $status = 200) {
        $response = array(
            'success'=>true,
            'message' => $message,
            'status_code' => $status,
            'data'=>$data,
        );
        return json_encode($response);
    }


}
