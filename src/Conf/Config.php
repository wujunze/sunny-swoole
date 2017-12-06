<?php
/**
 * +----------------------------------------------------------------------
 * | 
 * +----------------------------------------------------------------------
 * | Copyright (c) 2016 http://www.sunnyos.com All rights reserved.
 * +----------------------------------------------------------------------
 * | Date：2017-12-06 08:06:43
 * | Author: Sunny (admin@sunnyos.com) QQ：327388905
 * +----------------------------------------------------------------------
 */

namespace Conf;

class Config
{
    private static $instance;
    protected $conf;
    function __construct()
    {
        $conf = $this->sysConf()+$this->userConf();
    }
    static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new static();
        }
        return self::$instance;
    }

    private function sysConf(){
        return array(
            "SERVER"=>array(
                "LISTEN"=>"0.0.0.0",
                "SERVER_NAME"=>"",
                "PORT"=>9501,
                "RUN_MODE"=>SWOOLE_PROCESS,//不建议更改此项
                "SERVER_TYPE"=>\Core\Swoole\Config::SERVER_TYPE_WEB,//
                'SOCKET_TYPE'=>SWOOLE_TCP,//当SERVER_TYPE为SERVER_TYPE_SERVER模式时有效
                "CONFIG"=>array(
                    'task_worker_num' => 8, //异步任务进程
                    "task_max_request"=>10,
                    'max_request'=>5000,//强烈建议设置此配置项
                    'worker_num'=>8,
                ),
            ),
            "DEBUG"=>array(
                "LOG"=>true,
                "DISPLAY_ERROR"=>true,
                "ENABLE"=>true,
            ),
            "CONTROLLER_POOL"=>true//web或web socket模式有效
        );
    }

    private function userConf(){
        return array();
    }
}