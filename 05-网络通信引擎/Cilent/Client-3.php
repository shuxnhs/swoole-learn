<?php
/**
 * 异步非阻塞客户端,只能在Cli命令行环境下运行
 * User: sHuXnHs <hexiaohongsun@gmail.com>
 * Date: 19-7-12
 * Time: 16:47
 */

$client = new Swoole\Client(SWOOLE_SOCK_TCP, SWOOLE_SOCK_ASYNC);
$client->on("connect", function(swoole_client $cli) {
    fwrite(STDOUT, "请输入");
    $msg = trim(fgets(STDIN));
    $cli->send($msg);
});
$client->on("receive", function(swoole_client $cli, $data){
    echo "Receive: $data";
});
$client->on("error", function(swoole_client $cli){
    echo "error".PHP_EOL;
});
$client->on("close", function(swoole_client $cli){
    echo "Connection close".PHP_EOL;
});

$client->connect('127.0.0.1', 9501);

