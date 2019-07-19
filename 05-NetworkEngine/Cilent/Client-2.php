<?php
/**
 * 同步阻塞客户端，php-fpm环境下
 * User: sHuXnHs <hexiaohongsun@gmail.com>
 * Date: 19-7-12
 * Time: 16:47
 */

//$cilent = new swoole_client(SWOOLE_SOCK_TCP);
$cilent = new swoole_client(SWOOLE_SOCK_UDP);

try{
    $cilent->connect("127.0.0.1",9502,-1);
}catch (Exception $e){
    echo $e->getMessage().PHP_EOL;
    exit("connect failed. Error: {$cilent->errCode}".PHP_EOL);
}

fwrite(STDOUT, "请输入");
$msg = trim(fgets(STDIN));
// 发送消息
$cilent->send($msg);

// 接收数据
$result = $cilent->recv();
echo $result.PHP_EOL;
$cilent->close();

