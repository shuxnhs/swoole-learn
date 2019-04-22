<?php
/**
 * Timer定时器作用：按照设定的时间间隔，每到对应时间就调用一次回调函数onTimer通知Server
 */
    class Server
    {

       
        private $serv;

        public function __construct() {
            $this->serv = new swoole_server("0.0.0.0", 9501);

            //开启Task功能
            $this->serv->set(array(
                'worker_num' => 8,
                'daemonize' => false,
                'max_request' => 8,
                'dispatch_mode' => 2,
                'debug_mode'=> 1 ,
            ));
            $this->serv->on('WorkerStart', array($this, 'onWorkerStart'));
            $this->serv->on('Connect', array($this, 'onConnect'));
            $this->serv->on('Receive', array($this, 'onReceive'));
            $this->serv->on('Close', array($this, 'onClose'));
            $this->serv->start();
        }

        public function onWorkerStart( $serv , $worker_id) {
            // 在Worker进程开启时绑定定时器
            echo "onWorkerStart\n";


            // 1.7.5之后onStart回调中不再支持定时器，此方法已移除，请勿使用
            // $serv->addtimer(500);
            // $serv->addtimer(1000);
            // $serv->addtimer(1500);

            // 只有当worker_id为0时才添加定时器,避免重复添加,建议使用tick定时器
            //swoole版本低于1.8.0的Task进程不能使用tick/after定时器
            if( $worker_id == 0 ) {
               
                    $serv->tick(500, function() {
                        echo "Do Thing A at interval 500\n";
                    });
                    $serv->tick(2000, function() {
                        echo "Do Thing A at interval 2000\n";
                    });

                    //after是一个一次性的定时器，执行完成后会被销毁
                    $serv->after(1000, function() {
                        echo "Do Thing A at interval 1000\n";
                    });
                   
            }
        }

        public function onConnect( $serv, $fd, $from_id ) {
            echo "Client {$fd} connect\n";
        }

        public function onReceive( swoole_server $serv, $fd, $from_id, $data ) {
            echo "Get Message From Client {$fd}:{$data}\n";
        }

        public function onClose( $serv, $fd, $from_id ) {
            echo "Client {$fd} close connection\n";
        }

    }

// 启动服务器 Start the server
$server = new Server();

