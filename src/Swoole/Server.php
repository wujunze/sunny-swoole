<?php
namespace Sunny\Swoole;
class Server {

    private $srv;
    public function __construct() {
    	$this->srv = new \swoole_server("0.0.0.0", 1500, SWOOLE_PROCESS, SWOOLE_SOCK_TCP);
        $this->srv->set([
            'worker_num' => 4, //工作进程数量
            'daemonize' => false, //是否作为守护进程
            'max_request' => 10000,//设置worker进程的最大任务数，默认为0，一个worker进程在处理完超过此数值的任务后将自动退出，进程退出后会释放所有内存和资源。
            'dispatch_mode' => 2,//数据分发包策略
            'debug_mode' => true,//是否开启debug模式
            'task_worker_num' => 8,//监听task之后可以通过这个参数设置任务的进程数
        ]);
        $this->srv->on('WorkerStart', [$this, 'onWorkerStart']);//监听进程启动
        $this->srv->on('receive', [$this, 'onReceive']);//监听tcp接收数据
        $this->srv->on('Task', [$this, 'onTask']);//监听异步任务投递
        $this->srv->on('Finish', [$this, 'onFinish']);//监听异步任务完成
        $this->srv->start();
    }
   
    /**
     * 接收TCP数据
     */
    public function onReceive($serv, $fd, $from_id, $data) {
        echo "data:" . ($data).PHP_EOL;
        //接收到数据投递到异步方法
        $this->srv->task($data);
    }

    /**
     * 定时器触发方法
     */
    public function showTime() {
        echo "定时器：".time().PHP_EOL;
    }

    /**
     * 异步任务投递
     */
    public function onTask($serv, $task_id, $from_id, $data) {
        echo "$task_id -> 任务开始:".$data.PHP_EOL;
        sleep(2);
        // 任务完成通知已经结束
        $serv->finish($data);
    }

    /**
     * 异步任务完成回调
     */
    public function onFinish($serv, $task_id, $data) {
        echo "$task_id -> 任务完成:".$data.PHP_EOL;
    }

    /**
     * 进程启动
     */
    public function onWorkerStart($serv, $worker_id) {
        if ($worker_id == 0) {
            if (!$serv->taskworker) {
		        $serv->tick(1000, function ($id) {
		            $this->showTime();
		        });
		    }else{
		        $serv->addtimer(1000);
		    }
        }
    }
}
