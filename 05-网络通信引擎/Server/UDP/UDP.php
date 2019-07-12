<?php
/**
 * User: sHuXnHs <hexiaohongsun@gmail.com>
 * Date: 19-7-12
 * Time: 17:47
 */
class UDP{

    private $sock_type = SWOOLE_SOCK_UDP;

    protected $config = [
        'worker_num' => '8',        // worker进程数
        'max_conn' => '1000',       // 最大连接数
        'daemonize' => '1',         // 开启转入后台守护进程运行
        'reactor_num' => '8',       // 线程数量
        'max_request' => '1000',    // 最大请求数，表示worker进程在处理ｎ次请求后结束运行，设置为０表示不自动重启
        'log_file' => '/var/log/swoole.log',    //　指定swoole错误日志文件ao
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
    public function __construct($host = '127.0.0.1', $port = 9502, $mode = SWOOLE_PROCESS)
    {
        $this->serv = new swoole_server($host, $port, $mode, $this->sock_type);

        // 设置运行的各项参数
        $this->serv->set($this->config);

        // UDP没有连接概念，直接向Server发送数据包
        $this->serv->on("Packet", [$this, 'onPacket']);

        // 开启事件
        $this->serv->start();

    }

    /**
     * @function        监听接收到UDP数据包事件
     * @param   object  $serv       server对象
     * @param   string  $data       收到的数据文件，可能是文本或二进制内容
     * @param   array   $clientInfo 客户端信息数据
     */
    public function onPacket($serv, $data, $clientInfo) {
        $serv->sendto($clientInfo['address'], $clientInfo['port'], "Server ".$data);
        var_dump($clientInfo);
    }
}
$serv = new UDP();