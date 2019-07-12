<?php
/**
 * 客户端
 * User: sHuXnHs <hexiaohongsun@gmail.com>
 * Date: 19-7-12
 * Time: 16:47
 */
class Client{

    public $client = null;

    public $config = [];

    /**
     * Client constructor.
     * @param string    $host           host
     * @param int       $port           port
     * @param int       $sock_type      socket的类型，默认TCP，也可以UDP:SWOOLE_SOCK_UDP
     * @param int       $is_async       默认同步阻塞，也可以指定为SWOOLE_SOCK_ASYNC异步非阻塞
     * @param bool      $is_keep        是否创建长连接
     */
    public function __construct($host = '127.0.0.1', $port = 9501, $sock_type = SWOOLE_SOCK_TCP,
                                $is_async = SWOOLE_SOCK_SYNC, $is_keep = true)
    {
        if($is_keep){
            $this->client = new Swoole\Client($sock_type | SWOOLE_KEEP, $is_async);
        }else{
            $this->client = new Swoole\Client($sock_type | SWOOLE_KEEP, $is_async);
        }

        // 设置运行的各项参数
        $this->client->set($this->config);

        // 注册异步事件回调函数
        $this->client->on("connect", [$this, 'onConnect']);

        $this->client->on("receive", [$this, 'onReceive']);

        $this->client->on("error", [$this, 'onError']);

        $this->client->on("close", [$this, 'onClose']);

        // 开启事件
        $this->client->connect($host, $port);

    }

    /**
     * @function                    监听连接进入事件
     * @param   object  $cli        client对象
     */
    public function onConnect($cli){
        fwrite(STDOUT, "input data to server");
        $data = trim(fgets(STDIN));
        $cli->send($data);
    }


    /**
     * @function    监听事件接受数据
     * @param   object  $cli    client对象
     * @param   string  $data   接收到的数据
     */
    public function onReceive($cli, $data){
        echo "Receive data: ".$data.PHP_EOL;
    }


    /**
     * @function    监听事件错误
     * @param   object  $cli    client对象
     */
    public function onError($cli){
        echo "error".$cli->errCode.PHP_EOL;
    }

    /**
     * @function    监听连接关闭事件
     * @param   object  $cli        client对象
     */
    public function onClose($cli){
        echo "Connection close".PHP_EOL;
    }

}
$client = new Client();
