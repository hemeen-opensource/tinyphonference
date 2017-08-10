<?php

namespace App\Http\Controllers\Admin;

use App\Call;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class LogController extends Controller
{
    public $module = '';
    public $parent_module = 'parent_log';

    /**
     * 呼叫方向
     * @var array
     */
    protected $dir_array = [
       0 => '呼入',
       1 => '呼出',
       2 => '内部呼叫'
    ];

    /**
     * 通道设备类型
     * @var array
     */
    protected $devtype_array = [
        1 => '中继',
        2 => 'SIP',
        3 => 'H323',
        4 => '模拟外线',
        5 => '模拟内线',
        10 => '逻辑通道'
    ];

    /**
     * 接通标志
     * @var array
     */
    protected $callstatus_array = [
        0 => '否',
        1 => '是',
    ];

    /**
     * 结束类型
     * @var array
     */
    protected $endtype_array = [
        0 => '',
        1 => '本地拆线',
        2 => '远端拆线',
        3 => '设备拆线'
    ];

    /**
     * 呼叫失败原因
     * @var array
     */
    protected $callfailcause_array = [
        0 => '设备',
        1 => 'SS7',
        2 => 'PRI',
        3 => 'SIP'
    ];

    /**
     * 账单详情 只记录成功的呼叫，按分钟计费，
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bill(Request $request)
    {
        $logs = $this->formBillLog($request);
        $search = $request->all();
        return view('admin.log.bill', compact('logs','search'));
    }

    /**
     * 呼叫详情详情
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function call(Request $request)
    {
        $logs = $this->formCallLog($request);
        $search = $request->all();
        return view('admin.log.call', compact('logs','search'));
    }

    /**
     * 会议详情 会议-发起者-被邀请者
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function conf(Request $request)
    {
        $logs = $this->formCallLog($request);
        $search = $request->all();
        return view('admin.log.conf', compact('logs','search'));
    }

    /**
     * 查询详单详情记录
     * @param $request
     * @return mixed
     */
    public function formBillLog($request,$all = false){
        $mobile = $request->get('mobile');
        $month = date('Y-m-01', strtotime(date("Y-m-d")));
        $start_time = $request->get('start_time',$month);
        $end_time = $request->get('end_time');

        if($end_time){
            $end_time = date('Y-m-d H:i:s',(strtotime("$end_time + 1 day") - 1));
        }else{
            $end_time = date('Y-m-d H:i:s',(strtotime("$month + 1 month") -1));
        }

        $dir = $request->get('dir',-1);

        // 是否接通
        $callstatus = 1;
        $talkduration = $request->get('talkduration',-1);

        // 查询记录
        $logs = Call::where(function ($query) use ($mobile,$start_time,$end_time,$dir,$callstatus,$talkduration) {
            if($start_time){
                Log::Info(sprintf('开始时间: %s 结束时间: %s',$start_time,$end_time));
                $query->whereBetween('callendtime',[$start_time,$end_time]);
            }
            if ($mobile){
                $query->where(function ($query) use($mobile) {
                    $query->orWhere('ani', 'like', '%'.$mobile .'%')
                        ->orWhere('dnis', 'like', '%'.$mobile .'%');
                });
            }
            if($dir >= 0){
                $query->where('dir', $dir);
            }
            if($callstatus >=0){
                $query->where('callstatus',$callstatus);
            }
            if($talkduration >=0){
                if($talkduration == 1){
                    $query->whereBetween('talkduration',[1,60]);
                }
                if($talkduration == 30){
                    $query->whereBetween('talkduration',[60,1800]);
                }
                if($talkduration == 60){
                    $query->whereBetween('talkduration',[1800,3600]);
                }
                if($talkduration > 60){
                    $query->where('talkduration','>',3600);
                }
            }
        })
            ->orderBy('callendtime', 'desc');

        if($all){
            $list = $logs->get();
        }
        else{
            $list =  $logs->paginate();
        }

        return $this->getLogList($list);

    }

    /**
     * 查询呼叫详情记录
     * @param $request
     * @return mixed
     */
    public function formCallLog($request,$all = false){
        $mobile = $request->get('mobile');
        $month = date('Y-m-01', strtotime(date("Y-m-d")));
        $start_time = $request->get('start_time',$month);
        $end_time = $request->get('end_time');

        if($end_time){
            $end_time = date('Y-m-d H:i:s',(strtotime("$end_time + 1 day") - 1));
        }else{
            $end_time = date('Y-m-d H:i:s',(strtotime("$month + 1 month") -1));
        }

        $dir = $request->get('dir',-1);
        $callstatus = $request->get('callstatus',-1);
        $talkduration = $request->get('talkduration',-1);

        // 查询记录
        $logs = Call::where(function ($query) use ($mobile,$start_time,$end_time,$dir,$callstatus,$talkduration) {
            if($start_time){
                Log::Info(sprintf('开始时间: %s 结束时间: %s',$start_time,$end_time));
                $query->whereBetween('callendtime',[$start_time,$end_time]);
            }
            if ($mobile){
                $query->where(function ($query) use($mobile) {
                    $query->orWhere('ani', 'like', '%'.$mobile .'%')
                        ->orWhere('dnis', 'like', '%'.$mobile .'%');
                });
            }
            if($dir >= 0){
                $query->where('dir', $dir);
            }
            if($callstatus >=0){
                $query->where('callstatus',$callstatus);
            }
            if($talkduration >=0){
                if($talkduration == 1){
                    $query->whereBetween('talkduration',[1,60]);
                }
                if($talkduration == 30){
                    $query->whereBetween('talkduration',[60,1800]);
                }
                if($talkduration == 60){
                    $query->whereBetween('talkduration',[1800,3600]);
                }
                if($talkduration > 60){
                    $query->where('talkduration','>',3600);
                }
            }
        })
            ->orderBy('callendtime', 'desc');
        if($all){
            $list = $logs->get();
        }
        else{
            $list =  $logs->paginate();
        }
        return $this->getLogList($list);
    }


    /**
     * 处理记录信息
     * @param $logs
     * @return mixed
     */
    public function getLogList($logs){
        if($logs){
            foreach ($logs as &$log){
                // 呼叫方向
                $log['dir'] = isset($this->dir_array[$log['dir']]) ? $this->dir_array[$log['dir']] : '' ;
                //通道设备类型
                $log['devtype'] = isset($this->devtype_array[$log['devtype']]) ? $this->devtype_array[$log['devtype']] : '' ;
                //接通标志
                $log['callstatus'] = isset($this->callstatus_array[$log['callstatus']]) ? $this->callstatus_array[$log['callstatus']] : '' ;
                // 挂断类型
                $log['endtype'] = isset($this->endtype_array[$log['endtype']]) ? $this->endtype_array[$log['endtype']] : '' ;
                // 呼叫失败原因
                $log['callfailcause'] = isset($this->callfailcause_array[$log['callfailcause']]) ? $this->callfailcause_array[$log['callfailcause']] : '' ;
                // 分钟数
                if($log['talkduration'] > 0){
                    $log['minute'] =  ceil($log['talkduration'] / 60) ;
                }else{
                    $log['minute'] = 0;
                }
            }
        }
        return $logs;
    }

    /**
     * excel 导出呼叫详情
     */
    public function excel($type,Request $request)
    {
        $headTile = [
            'ID',
            '开始时间',
            '接通时间',
            '结束时间',
            '结束原因',
            '是否接通',
            '挂断',
            '呼叫方向',
            '主叫',
            '被叫',
            '号码所属运营商',
            '通话时间长度秒',
            '通话分钟数'
        ];
        $cellData[] = $headTile;

        switch ($type){
            case 'call':
                $logs = $this->formCallLog($request,true);
                $title = '呼叫详情';
                break;
            case 'bill':
                $logs = $this->formBillLog($request,true);
                $title = '账单';
                break;
            case 'conf':
                $logs = $this->formCallLog($request,true);
                $title = '会议';
                break;
        }

        if ($logs) {
            foreach ($logs as $log) {
                $cell = [
                    $log->id, // ID
                    $log->callbegintime, // 开始时间
                    $log->connectbegintime, // 接通时间
                    $log->callendtime, // 结束时间
                    $log->callfailcause,// 结束原因
                    $log->callstatus, // 是否接通
                    $log->endtype, // 挂断
                    $log->dir, // 呼叫方向
                    $log->ani, // 主叫
                    $log->dnis,// 被叫
                    '',// 运营商
                    $log->talkduration, // 通话时间长度秒
                    $log->minute, // 通话分钟数
                ];
                $cellData[] = $cell;
            }
        }

        $file_title = $title . date('YmdHis', time());
        Excel::create($file_title, function ($excel) use ($cellData) {
            $excel->sheet('详情记录', function ($sheet) use ($cellData) {
                $sheet->rows($cellData);
            });
        })->export('xls');

    }


    /**
     * 假数据
     */
    public function create(Request $request)
    {
        $password = $request->get('password');
        if ($password != 'liushuixingyun') {
            dd('哥，密码错误！');
        }
        $year = $request->get('year');
        if(! $year){
            dd('哥，输入年份呀！格式  &year=2017');
        }
        // http://localhost/create?password=liushuixingyun&year=2017
        //dd('这个不是你能随便访问的好吗');
        $this->createYear('2017');
    }

    /**
     * 随机手机号码
     * @return string
     */
    public function mobile()
    {
        $mobile = '1';
        $mobile .= rand(1000, 9999);
        $mobile .= rand(100000, 999999);
        return $mobile;
    }

    /**
     * 生成年数据
     * @param $year
     */
    public function createYear($year)
    {
        for ($i = 1; $i <= 12; $i++) {
            $this->createMonth($i, $year);
        }
    }

    /**
     * 生成月数据
     * @param $month
     * @param $year
     */
    public function createMonth($month, $year)
    {
        $total_day = $this->getMonthLastDay($month, $year);
        for ($i = 1; $i <= $total_day; $i++) {
            $total_day_string = $year . "-" . $month . "-" . $i;
            $this->createDay($total_day_string);
        }
    }

    /**
     * 每月多少天
     * @param $month
     * @param $year
     * @return int
     */
    function getMonthLastDay($month, $year)
    {
        switch ($month) {
            case 4 :
            case 6 :
            case 9 :
            case 11 :
                $days = 30;
                break;
            case 2 :
                if ($year % 4 == 0) {
                    if ($year % 100 == 0) {
                        $days = $year % 400 == 0 ? 29 : 28;
                    } else {
                        $days = 29;
                    }
                } else {
                    $days = 28;
                }
                break;
            default :
                $days = 31;
                break;
        }
        return $days;
    }

    protected $callstatus = [0, 1]; // 呼通标志 0  呼叫未接通 | 1 呼叫接通
    protected $endtype = [0, 1, 2, 3]; // 结束类型 0 未定义 | 1 本地拆线 | 2 远端拆线 | 3 设备拆线
    protected $ipscreason = [0, 1]; // 呼叫失败原因：IPSC定义reason值
    protected $callfailcause = [0, 1, 2, 3]; // 呼叫失败原因 0 设备 | 1 SS7 | 2 PRI | 3 SIP


    /**
     * 传入时间戳 天
     * @param $date
     */
    public function createDay($date)
    {
        $today = strtotime($date);
        $count = 1;
        do {
            if ($count > 6) {
                $total = rand(0, 1);
            } elseif ($count > 6 && $count < 18) {
                $total = rand(1, 10);
            } else {
                $total = rand(0, 5);
            }
            if ($total > 0) {
                for ($i = 0; $i <= $total; $i++) {
                    $callstatus = rand(0, 6);
                    // 呼叫未接通数据
                    if ($callstatus / 2 == 1) {
                        $callendtime = $today + $count * 60 * rand(0, 60); // 挂断时间在该时间段
                        $talkduration = 0; // 通话时长（秒）0
                        $connectbegintime = $callendtime; // 应答时间 = 挂断时间
                        $callbegintime = $callendtime - rand(0, 60); // 接听时间 = 挂断时间 - 随机数（0-60）
                        $dir = rand(0, 1); // 呼入 呼出
                        $callstatus = 0;
                    } else {
                        // 呼叫接通数据
                        $callendtime = $today + $count * 60 * rand(0, 60); // 挂断时间在该时间段
                        $talkduration = rand(0, 600); // 通话时长（秒），在10分钟内
                        $connectbegintime = $callendtime - $talkduration; // 应答时间 = 挂断 - 通话时长
                        $callbegintime = $connectbegintime - rand(0, 60); // 接听时间 = 应答时间 - 随机数（0-60）
                        $dir = rand(0, 1); // 呼入 呼出
                        $callstatus = 1;
                    }
                    $params = [
                        'dir' => $dir,
                        'callbegintime' => date('Y-m-d H:i:s', $callbegintime),
                        'connectbegintime' => date('Y-m-d H:i:s', $connectbegintime),
                        'callendtime' => date('Y-m-d H:i:s', $callendtime),
                        'talkduration' => $talkduration,
                        'callstatus' => $callstatus
                    ];
                    // 插入数据
                    $options = $this->options($params);
                    Call::insertGetId($options);
                }
            }
            $count++;
        } while ($count < 24);

    }

    /**
     * 返回数据
     * @param $params
     * @return array
     */
    public function options($params)
    {

        //        `id` VARCHAR(64) NOT NULL COMMENT 'UUID',
        //  `nodeid` VARCHAR(256) NOT NULL COMMENT 'IPSC节点ID（格式：区域ID.站ID.IPSC实例ID）',
        //  `cdrid` VARCHAR(256) NOT NULL COMMENT 'CDR 记录ID',
        //  `processid` VARCHAR(256) NOT NULL COMMENT '流水号（全局唯一，IPSC实例启动时开始计算，单个实例期间严格递增）',
        //  `callid` VARCHAR(256) NOT NULL COMMENT '呼叫标识号（节点内全局唯一）',
        //  `ch` INT NOT NULL COMMENT '通道号：因交换机初始化时间不同，通道号可能会变化',
        //  `cdrcol` VARCHAR(256) NULL,
        //  `devno` VARCHAR(256) NOT NULL COMMENT '设备号： \n中继：格式 “0:0:1:1”—“交换机号:板号:中继号:通道号”；\nSIP：格式“0:0:1”—“交换机号:板号:通道号”；\nFXO：格式“0:0:1”—“交换机号:板号:通道号”；',
        //  `ani` VARCHAR(256) NULL COMMENT '主叫号码',
        //  `dnis` VARCHAR(256) NULL COMMENT '被叫号码',
        //  `dnis2` VARCHAR(256) NULL COMMENT '原被叫号码',
        //  `orgcallno` VARCHAR(256) NULL COMMENT '原始号码',
        //  `dir` INT NOT NULL COMMENT '呼叫方向 \n0: 呼入\n1: 呼出\n2: 内部呼叫（保留）',
        //  `devtype` INT NOT NULL COMMENT '通道设备类型 \n1: 中继\n2: SIP\n3: H323\n4: 模拟外线\n5: 模拟内线\n10: 逻辑通道',
        //  `busitype` INT NULL,
        //  `callstatus` INT NOT NULL COMMENT '呼通标志 \n0: 呼叫未接通\n1: 呼叫接通',
        //  `endtype` INT NOT NULL COMMENT '结束类型 \n0: 空（初始值，未定义）\n1: 本地拆线\n2: 远端拆线\n3: 设备拆线',
        //  `ipscreason` INT NULL COMMENT '呼叫失败原因：IPSC定义reason值',
        //  `callfailcause` INT NULL COMMENT '呼叫失败原因：设备、SS7、PRI、SIP的失败cause值',
        //  `callbegintime` DATETIME NOT NULL COMMENT '开始时间',
        //  `connectbegintime` DATETIME NULL COMMENT '应答时间（呼叫未接通时，该时间为空）',
        //  `callendtime` DATETIME NOT NULL COMMENT '挂断时间',
        //  `talkduration` INT NOT NULL DEFAULT 0 COMMENT '通话时长（单位秒，应答时间-挂断时间，如果没有应答时间，通话时长为0）',

        // ------------------------------------------------------------------------------------------------------------------ //

        //        callstatus
        //        'dir'
        //        'callbegintime'=>$params['callbegintime'],
        //        'connectbegintime'=>$params['connectbegintime'],
        //        'callendtime'=> $params['callendtime'],
        //        'talkduration'=>$params['talkduration']

        $options = [
            'id' => uniqid(),  // [VARCHAR(64)]  UUID
            'nodeid' => uniqid(), // [VARCHAR(256)]  IPSC节点ID
            'cdrid' => uniqid(), // [VARCHAR(256)]   DR 记录ID
            'processid' => date('YmdHis', time()) . rand(100000, 999999), //  [VARCHAR(256)]  流水号 PSC实例启动时开始计算，单个实例期间严格递增
            'callid' => date('YmdHis', time()) . uniqid(), //  [VARCHAR(265)] 呼叫标识号 全局唯一
            'ch' => rand(100000, 999999), // [INT] 通道号：因交换机初始化时间不同，通道号可能会变化
            'cdrcol' => null,
            'devno' => '0:0:1:1',
            'ani' => $this->mobile(), // [VARCHAR(256)] 主叫号码
            'dnis' => $this->mobile(), // [VARCHAR(256)] 被叫号码
            'dnis2' => $this->mobile(), // [VARCHAR(256)] 原被叫号码
            'orgcallno' => $this->mobile(), // [VARCHAR(256)] 原始号码
            'dir' => $params['dir'], // [INT] 呼叫方向 0 呼入 | 1 呼出
            'projectid' => uniqid(),
            'flowid' => uniqid(),
            'devtype' => rand(0, 5), // [INT] 通道设备类型 0 中继 | 1 SIP | 2 H323 | 3 模拟外线 | 4 模拟内线 | 5 逻辑通道
            'busitype' => null,
            'callstatus' => $params['callstatus'], // [INT] 呼通标志 0 呼叫未接通 | 1 呼叫接通
            'endtype' => rand(0, 3), // [INT] 结束类型 0 空 | 1 本地拆线 | 2 远端拆线 | 3 设备拆线
            'ipscreason' => null, // [INT] 呼叫失败原因 IPSC定义reason值
            'callfailcause' => rand(1, 3), // 呼叫失败原因
            'callbegintime' => $params['callbegintime'],
            'connectbegintime' => $params['connectbegintime'],
            'callendtime' => $params['callendtime'],
            'talkduration' => $params['talkduration']
        ];
        return $options;
    }


}
