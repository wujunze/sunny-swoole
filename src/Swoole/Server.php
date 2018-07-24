<?php
namespace Sunny\Swoole;
class Server {
	protected static $instance;
    protected $swooleServer;
    protected $isStart = 0;

    /*
     * 单例模式
     * @return Object Server
     */
    static function getInstance(){
        if(!isset(self::$instance)){
            self::$instance = new static();
        }
        return self::$instance;
    }

    private function __construct(){
    	$conf = Config::getInstance();
        if($conf->getServerType() == Config::SERVER_TYPE_SERVER){
            $this->swooleServer = new \swoole_server($conf->getListenIp(),$conf->getListenPort(),$conf->getRunMode(),$conf->getSocketType());
        }else if($conf->getServerType() == Config::SERVER_TYPE_WEB){
            $this->swooleServer = new \swoole_http_server($conf->getListenIp(),$conf->getListenPort(),$conf->getRunMode());
        }else if($conf->getServerType() == Config::SERVER_TYPE_WEB_SOCKET){
            $this->swooleServer = new \swoole_websocket_server($conf->getListenIp(),$conf->getListenPort(),$conf->getRunMode());
        }else{
            die('server type error');
        }
    }
}
