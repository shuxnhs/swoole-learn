<?php
/**
 * User: sHuXnHs <hexiaohongsun@gmail.com>
 * Date: 19-7-9
 * Time: 11:47
 */
class TCP{

    private $sock_type = SWOOLE_SOCK_TCP;

    protected $config = [
        'worker_num' => '8',        // worker进程数
        'max_conn' => '1000',       // 最大连接数
        'daemonize' => '1',         // 开启转入后台守护进程运行
        'reactor_num' => '8',       // 线程数量
        'max_request' => '1000',    // 最大请求数，表示worker进程在处理ｎ次请求后结束运行，设置为０表示不自动重启
        'log_file' => '/var/log/swoole.log',    //　指定swoole错误日志文件
        'heartbeat_check_interval' => 30 ,      //  每隔多少秒检测一次，swoole会轮询所有TCP请求，超过心跳时间关闭，单位秒
        'heartbeat_idle_time' => 60             // TCP连接最大闲置时间，单位秒
    ];

    public $serv = null;

    /**
     * TCP constructor.
     * @param string    $host host
     * @param int       $port port
     * @param int       $mode 默认为多线程模式，还有SWOOLE_BASE基本模式
     */
    public function __construct($host = '127.0.0.1', $port = 9501, $mode = SWOOLE_PROCESS)
    {
        $this->serv = new swoole_server($host, $port, $mode, $this->sock_type);

        // 设置运行的各项参数
        $this->serv->set($this->config);

        $this->serv->on("connect", [$this, 'onConnect']);

        $this->serv->on("receive", [$this, 'onReceive']);

        $this->serv->on("close", [$this, 'onClose']);

        // 开启事件
        $this->serv->start();

    }

    /**
     * @function                    监听连接进入事件
     * @param   object  $serv       server对象
     * @param   int     $fd         客户端唯一标示
     * @param   int     $reactor_id 线程id
     */
    public function onConnect($serv, $fd, $reactor_id){
        echo "Cilent: ".$fd."-thread".$reactor_id."-connect".PHP_EOL;

    }

    /**
     * @function    监听事件接受数据
     * @param $serv
     * @param $fd
     * @param $from_id
     * @param $data
     */
    public function onReceive($serv, $fd, $from_id, $data){
        echo "Get Message From Cilent: ".$fd." data :{$data}\n";
        $serv->send($fd,  "server:".$data);
    }

    /**
     * @function    监听连接关闭事件
     * @param $serv
     * @param $fd
     */
    public function onClose($serv, $fd){
        echo "Cilent".$fd."close".PHP_EOL;
    }
}
$serv = new TCP();