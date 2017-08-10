<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class StatisticsController extends Controller
{
    public $module = '';
    public $parent_module = 'parent_statistic';

    /**
     * MYSQL日期格式
     * @var array
     */
    protected $date_format_array = [
        'day' => '%Y-%m-%d %H',
        'month' => '%Y-%m-%d',
        'year' => '%Y-%m',
    ];

    /**
     * 指标类型 call | call_reason | call_percent
     * @var
     */
    protected $type;

    /**
     * 当前查询时间类型 day | month | year
     * @var
     */
    protected $current_date_type;

    /**
     * 当前日期格式
     * @var
     */
    protected $current_date_format;

    /**
     * 开始时间
     * @var
     */
    protected $start_time;

    /**
     * 结束时间
     * @var
     */
    protected $end_time;


    /**
     * 呼叫统计
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        return view('admin.statistic.index');
    }

    /**
     * 通话时长统计
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function call(){
        return view('admin.statistic.call');
    }

    /**
     * 呼出失败原因统计
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function reason(){
        return view('admin.statistic.reason');
    }


    /**
     * @param type day | month | year
     * @param day
     * @param month
     * @param year
     * @param start_time
     * @param end_time
     * @param start_day
     * @param end_day
     * @param start_month
     * @param end_month
     * @param Request $request
     * @param Request $request
     * @return string
     * @throws \Exception
     */
    public function start(Request $request){
        try{
            $this->type = $request->get('type','call');

            $this->current_date_type = $request->get('date_type','day');
            // 日期格式
            $this->current_date_format = $this->date_format_array[$this->current_date_type];
            switch ($this->current_date_type){
                case 'day':
                    $list = $this->getDayData($request);
                    break;
                case 'month':
                    $list = $this->getMonthData($request);
                    break;
                case 'year':
                    $list = $this->getYearData($request);
                    break;
            }
            return $this->successResponse($list);
        }catch (\Exception $exception){
            throw $exception;
        }
    }

    /**
     * 获取每日某时段数据
     * @param $request
     * @return array
     */
    public function getDayData($request){
        $start_time = $request->get('start_time');
        $end_time = $request->get('end_time');
        if($start_time){
            $this->start_time = date('Y-m-d H:i:s',strtotime($start_time));
        }
        if($end_time){
            $this->end_time =date('Y-m-d H:i:s',strtotime($end_time) );
        }
        if(!$start_time){
            $today = date('Y-m-d',time());
            $this->start_time = $request->get('start_time',date('Y-m-d H:i:s',strtotime($today)));
            $this->end_time = $request->get('end_time',date('Y-m-d H:i:s',(strtotime($today) + 60 * 60 * 24 -1) ));
        }
        Log::Info(sprintf('开始时间:%s 结束时间 %s',$this->start_time,$this->end_time));
        $call_list = [];
        $day_list = $this->getCallList();

        if($day_list){
            foreach ($day_list as $list){
                $call_list[$list->time] = $list;
            }
        }
        $call_list = $this->cureDayList($call_list);
        return $call_list;
    }

    /**
     * 获取每月时间段数据
     * @param $request
     * @return array
     */
    public function getMonthData($request){
        $month = date('Y-m-01', strtotime(date("Y-m-d")));
        $this->start_time = $request->get('start_time',$month);
        $end_month = $request->get('end_time');
        $this->end_time = date('Y-m-d H:i:s',(strtotime("$end_month + 1 day") -1));
        // 获取数据
        $month_list = $this->getCallList();
        $call_list = [];
        if($month_list){
            foreach ($month_list as $list){
                $call_list[$list->time] = $list;
            }
        }
        $call_list = $this->cureMonthList($call_list);
        return $call_list;
    }

    /**
     * 获取每年某时间段数据
     * @param $request
     * @return array
     */
    public function getYearData($request){
        $year = date('Y',time())."-01-01 00:00:00";
        $start_time = $request->get('start_time');
        if($start_time){
            $this->start_time = date('Y-m-d H:i:s',strtotime(date('Y-m',strtotime($start_time))));
        }else{
            $this->start_time = $year;
        }
        $end_time = $request->get('end_time');
        if($end_time){
            $end_time = date('Y-m',strtotime($end_time));
            $this->end_time = date('Y-m-d H:i:s',(strtotime("$end_time + 1 month") -1));
        }else{
            $this->end_time = date('Y-m-d H:i:s',(strtotime("$year + 1 year") -1));
        }
        $year_list = $this->getCallList();
        Log::Info(sprintf('开始时间:%s 结束时间 %s',$this->start_time,$this->end_time));
        $call_list = [];
        if($year_list){
            foreach ($year_list as $list){
                $call_list[$list->time] = $list;
            }
        }
        $call_list = $this->cureYearList($call_list);
        return $call_list;
    }

    /**
     * 处理年时段数据
     * @param $call_list
     * @return array
     */
    public function cureYearList($call_list){
        $short_start_times =intval(date('m',strtotime($this->start_time))) ;
        $short_end_times =intval(date('m',strtotime($this->end_time)));
        $start_time = date('Y-',strtotime($this->start_time));
        switch ($this->type){
            case 'call':
                $result_list = $this->getCureCallList($short_start_times,$short_end_times,$start_time,$call_list);
                break;
            case 'call_reason':
                $result_list = $this->getCureCallReasonList($short_start_times,$short_end_times,$start_time,$call_list);
                break;
            case 'call_percent':
                $result_list = $this->getCurePercentList($short_start_times,$short_end_times,$start_time,$call_list);
                break;
        }
        return $result_list;
    }


    /**
     * 处理月时段数据
     * @param $call_list
     * @return array
     */
    public function cureMonthList($call_list){
        $short_start_times =intval(date('d',strtotime($this->start_time))) ;
        $short_end_times =intval(date('d',strtotime($this->end_time)));
        $start_time = date('Y-m-',strtotime($this->start_time));
        switch ($this->type){
            case 'call':
                $result_list = $this->getCureCallList($short_start_times,$short_end_times,$start_time,$call_list);
                break;
            case 'call_reason':
                $result_list = $this->getCureCallReasonList($short_start_times,$short_end_times,$start_time,$call_list);
                break;
            case 'call_percent':
                $result_list = $this->getCurePercentList($short_start_times,$short_end_times,$start_time,$call_list);
                break;
        }
        return $result_list;
    }

    /**
     * 处理日时段数据
     * @param $call_list
     * @return array
     */
    public function cureDayList($call_list){
        $short_start_times =intval(date('H',strtotime($this->start_time))) ;
        $short_end_times =intval(date('H',strtotime($this->end_time)));
        $start_time = date('Y-m-d ',strtotime($this->start_time));
        switch ($this->type){
            case 'call':
                $result_list = $this->getCureCallList($short_start_times,$short_end_times,$start_time,$call_list);
                break;
            case 'call_reason':
                $result_list = $this->getCureCallReasonList($short_start_times,$short_end_times,$start_time,$call_list);
                break;
            case 'call_percent':
                $result_list = $this->getCurePercentList($short_start_times,$short_end_times,$start_time,$call_list);
                break;
        }
        return $result_list;
    }

    /**
     * 返回处理过的通话记录
     * @param $short_start_times
     * @param $short_end_times
     * @param $start_time
     * @param $call_list
     * @return array
     */
    public function getCureCallList($short_start_times,$short_end_times,$start_time,$call_list){
        $result_list = [
            'call_all' =>[],
            'call_in' =>[],
            'call_out_success' =>[],
            'call_out_fail' =>[],
            'call_effect' =>[]
        ];
        for ($i = intval($short_start_times); $i<=$short_end_times;$i++){
            $current_times = $start_time .$this->getTimes($i);
            if(!array_key_exists( $current_times,$call_list)){
                $result_list['call_all'][] = 0;
                $result_list['call_in'][] = 0;
                $result_list['call_out_success'][] = 0;
                $result_list['call_out_fail'][] = 0;
                $result_list['call_effect'][] = 0;
            }else{
                $result_list['call_in'][] = intval($call_list[$current_times]->call_in_num);
                $result_list['call_out_success'][] = intval($call_list[$current_times]->call_out_success_num);
                $result_list['call_out_fail'][] = intval($call_list[$current_times]->call_out_fail_num);
                $result_list['call_effect'][] = intval($call_list[$current_times]->call_effect_num);
                $result_list['call_all'][] = intval($call_list[$current_times]->count);
            }
        }
        return $result_list;
    }

    /**
     * 返回处理过的通话时长
     * @param $short_start_times
     * @param $short_end_times
     * @param $start_time
     * @param $call_list
     * @return array
     */
    public function getCurePercentList($short_start_times,$short_end_times,$start_time,$call_list){
        $result_list = [
            'call_in_time' =>[],
            'call_out_time' =>[],
            'call_in_num' =>[],
            'call_out_num' =>[],
            'count' =>[]
        ];
        for ($i = intval($short_start_times); $i<=$short_end_times;$i++){
            $current_times = $start_time .$this->getTimes($i);
            if(!array_key_exists( $current_times,$call_list)){
                $result_list['call_in_time'][] = 0;
                $result_list['call_out_time'][] = 0;
                $result_list['call_out_success'][] = 0;
                $result_list['call_out_num'][] = 0;
                $result_list['count'][] = 0;
            }else{
                $result_list['call_in_time'][] = intval($call_list[$current_times]->call_in_time);
                $result_list['call_out_time'][] = intval($call_list[$current_times]->call_out_time);
                $result_list['call_in_num'][] = intval($call_list[$current_times]->call_in_num);
                $result_list['call_out_num'][] = intval($call_list[$current_times]->call_out_num);
                $result_list['count'][] = intval($call_list[$current_times]->count);
            }
        }
        return $result_list;
    }

    /**
     * 返回处理原因统计
     * @param $short_start_times
     * @param $short_end_times
     * @param $start_time
     * @param $call_list
     * @return array
     */
    public function getCureCallReasonList($short_start_times,$short_end_times,$start_time,$call_list){
        $result_list = [
            'callfailcause_1' =>[],
            'callfailcause_2' =>[],
            'callfailcause_3' =>[],
            'callfailcause_4' =>[],
            'count' =>[],
        ];
        for ($i = intval($short_start_times); $i<=$short_end_times;$i++){
            $current_times = $start_time .$this->getTimes($i);
            if(!array_key_exists( $current_times,$call_list)){
                $result_list['callfailcause_1'][] = 0;
                $result_list['callfailcause_2'][] = 0;
                $result_list['callfailcause_3'][] = 0;
                $result_list['callfailcause_4'][] = 0;
                $result_list['count'][] = 0;
            }else{
                $result_list['callfailcause_1'][] = intval($call_list[$current_times]->callfailcause_1);
                $result_list['callfailcause_2'][] = intval($call_list[$current_times]->callfailcause_2);
                $result_list['callfailcause_3'][] = intval($call_list[$current_times]->callfailcause_3);
                $result_list['callfailcause_4'][] = intval($call_list[$current_times]->callfailcause_4);
                $result_list['count'][] = intval($call_list[$current_times]->count);
            }
        }
        return $result_list;
    }

    /**
     * 获取日时间格式
     * @param $days
     * @return string
     */
    public function getTimes($days){
        if($days <10){
            return "0".$days;
        }
        return $days ;
    }

    /**
     * 分发查询任务
     * @return mixed
     */
    public function getCallList(){
        $list = [];
        switch ($this->type){
            case 'call':
                $list = $this->getCall();
                break;
            case 'call_reason':
                $list = $this->getCallReason();
                break;
            case 'call_percent':
                $list = $this->getCallPercent();
                break;
        }
        return $list;
    }

    /**
     *  第三个指标 '呼入数量','呼出成功数量','呼出失败数量','有效呼叫总数'
     * @return mixed
     */
    public function getCall(){
        $sql = 'select DATE_FORMAT(callendtime,"'.$this->current_date_format.'") as time,count(*) as count, '
            .'sum(CASE WHEN callstatus=0 THEN 1 else 0 END) as call_in_num, ' // 呼入数量
            .'sum(CASE WHEN callstatus=1 AND talkduration>0 THEN 1 else 0 END) as call_out_success_num, ' // 呼出成功数量
            .'sum(CASE WHEN callstatus=1 AND callfailcause!=0 AND talkduration=0 THEN 1 else 0 END) as call_out_fail_num, ' // 呼出失败数量
            .'sum(CASE WHEN talkduration>0 THEN 1 else 0 END) as call_effect_num ' // 有效呼叫总数
            .'from cti_cdr where callendtime between :start_time and :end_time GROUP BY DATE_FORMAT(callendtime,"'.$this->current_date_format.'")';
        $options = [
            'start_time' => $this->start_time,
            'end_time' => $this->end_time
        ];
        return DB::select($sql,$options);
    }

    /**
     * 第二个指标 呼叫失败原因1
     * @return mixed
     */
    public function getCallReason(){
        $sql = 'select DATE_FORMAT(callendtime,"'.$this->current_date_format.'") as time,count(*) as count, '
            .'sum(CASE WHEN callfailcause=0 THEN 1 else 0 END) as callfailcause_1, ' // 失败原因1
            .'sum(CASE WHEN callfailcause=1 THEN 1 else 0 END) as callfailcause_2, ' // 失败原因2
            .'sum(CASE WHEN callfailcause=2 THEN 1 else 0 END) as callfailcause_3, ' // 失败原因3
            .'sum(CASE WHEN callfailcause=3 THEN 1 else 0 END) as callfailcause_4 ' // 失败原因4
            .'from cti_cdr where callendtime between :start_time and :end_time GROUP BY DATE_FORMAT(callendtime,"'.$this->current_date_format.'")';
        $options = [
            'start_time' => $this->start_time,
            'end_time' => $this->end_time
        ];
        return DB::select($sql,$options);
    }

    /**
     * 第一个指标 呼出数量 + 呼入数量
     * @return mixed
     */
    public function getCallPercent()
    {
        $sql = 'select DATE_FORMAT(callendtime,"'.$this->current_date_format.'") as time,count(*) as count, '
            .'sum(CASE WHEN talkduration>0 AND dir =1  THEN CEILING(talkduration/60) else 0 END) as call_in_time, ' // 呼入时长
            .'sum(CASE WHEN talkduration>0 AND dir =0  THEN CEILING(talkduration/60) else 0 END) as call_out_time, ' // 呼出时长
            .'sum(CASE WHEN dir=0 THEN 1 else 0 END) as call_in_num, ' // 呼入数量
            .'sum(CASE WHEN dir=1 THEN 1 else 0 END) as call_out_num ' // 呼出数量
            .'from cti_cdr where callendtime between :start_time and :end_time GROUP BY DATE_FORMAT(callendtime,"'.$this->current_date_format.'")';
        $options = [
            'start_time' => $this->start_time,
            'end_time' => $this->end_time
        ];
        return DB::select($sql,$options);
    }



}
