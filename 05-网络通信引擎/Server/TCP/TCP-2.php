<?php
/**
 * User: sHuXnHs <hexiaohongsun@gmail.com>
 * Date: 19-7-12
 * Time: 12:34
 */

//创建Server对象，监听 127.0.0.1:9501端口
$serv = new swoole_server("127.0.0.1", 9501);

//
$serv->set([
    'worker_num' => '8',        // worker进程数
    'max_conn' => '1000',       // 最大连接数
    'daemonize' => '1',         // 开启转入后台守护进程运行
    'reactor_num' => '8',       // 线程数量
    'max_request' => '1000',    // 最大请求数，表示worker进程在处理ｎ次请求后结束运行，设置为０表示不自动重启
    'log_file' => '/var/log/swoole.log',    //　指定swoole错误日志文件ao
    'heartbeat_check_interval' => 30 ,      //  每隔多少秒检测一次，swoole会轮询所有TCP请求，超过心跳时间关闭，单位秒
    'heartbeat_idle_time' => 60             // TCP连接最大闲置时间，单位秒
]);

//监听连接进入事件
$serv->on('connect', function ($serv, $fd) {
    echo "Client:".$fd." Connect.\n";
});

//监听数据接收事件
$serv->on('receive', function ($serv, $fd, $from_id, $data) {
    $serv->send($fd, "Server: ".$data);
});

//监听连接关闭事件
$serv->on('close', function ($serv, $fd) {
    echo "Client: Close.\n";
});

//启动服务器
$serv->start();