<?php
/**
 * User: sHuXnHs <hexiaohongsun@gmail.com>
 * Date: 19-7-17
 * Time: 11:47
 */
class HTTP{

    protected $config = [
        'upload_tmp_dir' => '',                 // 设置上传文件的临时目录，目录的最大长度不能超过220字节
        'enable_static_handler' => true,        // 开启静态文件的请求处理功能，执行document_root目录的内容
        'document_root' => '',                  // 配置静态文件根目录，文件如果存在不会再触发onRequest回调
        'static_handler_locations' => [],       // 设置静态处理器的路径，类型为数组，类似nginx的location，如['/static', '/app/images']
        'http_compression' => true,             // 支持gzip,br,deflate压缩，默认开启，根据客户端的Accept-Encoding自动选择压缩方式
        'http_parse_post' => true,              // 设置POST消息解析开关，选项为true时自动将Content-Type为x-www-form-urlencoded的请求包体解析到POST数组
        'http_parse_cookie' => false,           // 关闭Cookie解析，将在header中保留未经处理的原始的Cookies信息。
    ];

    public $serv = null;

    /**
     * HTTP constructor.
     * @param string    $host host
     * @param int       $port port
     */
    public function __construct($host = '127.0.0.1', $port = 8088)
    {
        $this->serv = new swoole_http_server($host, $port);

        // 设置运行的各项参数
        $this->serv->set($this->config);

        // 请求的回调函数
        $this->serv->on("request", [$this, 'onRequest']);

        // 开启事件
        $this->serv->start();

    }


    /**
     * @function    请求的回调函数
     * @param   object  $request    http请求信息对象，包含了header/get/post/cookie等相关信息
     * @param   object  $response   Http响应对象，支持cookie/header/status等Http操作
     * 函数返回时底层会销毁$request和$response对象，如果未执行$response->end()操作，底层会自动执行一次$response->end("")
     */
    public function onRequest($request, $response){

    /********************************************
     ************ 请求相关信息的获取方法 ************
     ********************************************/

        // 获取请求头信息
        echo "获取请求头信息".PHP_EOL;
        var_dump($request->header);
        echo PHP_EOL;

        // 获取服务器信息
        echo "获取服务器信息".PHP_EOL;
        var_dump($request->server);
        echo PHP_EOL;

        // 获取所有的GET参数
        echo "获取所有的GET参数".PHP_EOL;
        var_dump($request->get);
        echo PHP_EOL;

        // 获取所有的post参数
        echo "获取所有的post参数".PHP_EOL;
        var_dump($request->post);
        echo PHP_EOL;

        // 获取COOKIE信息，格式为键值对数组
        echo "获取COOKIE信息".PHP_EOL;
        var_dump($request->cookie);
        echo PHP_EOL;

        // 获取上传的文件信息
        echo "获取上传的文件信息".PHP_EOL;
        var_dump($request->files);
        echo PHP_EOL;

        // 获取原始的POST包体，用于非application/x-www-form-urlencoded格式的Http POST请求
        echo "获取原始的POST包体".PHP_EOL;
        var_dump($request->rawContent);
        echo PHP_EOL;

        // 获取完整的原始Http请求报文。包括Http Header和Http Body
        echo "获取完整的原始Http请求报文".PHP_EOL;
        var_dump($request->getData);
        echo PHP_EOL;

        /********************************************
         ************ 响应相关信息的发送方法 ************
         ********************************************/

        // http头的key->value，默认http自动格式化
        $response->header("key", "value", true);

        // 设置响应的cookie信息，与setcookie(name,value,expire,path,domain,secure)一致
        // expire 为cookie的有效期，时间戳：time()± 60s，加设置cookie的保存时间，减删除cookie
        // path 为cookie的服务器路径
        // domain 为cookie的域名
        // secure 是否通过安全的HTTPS连接来传输cookie，默认false
        $response->cookie("key", "value");

        // 发送http状态码,必须为合法的HttpCode：200,301，404,500等
        $response->status(200);

        // redirect(url,status),状态码status发生跳转，301永久跳转，302临时跳转
        $response->redirect('', 404);

        // 启用Http Chunk分段向浏览器发送相应内容。
        $response->write('');

        // sendfile(filename,offset,length)发送文件
        // offset上传的偏移量，可以用于支持断点续传
        // 发送文件的尺寸，默认为整个文件的尺寸
        $response->sendfile('', 0);

        // 发送Http响应体，并结束请求处理,传递的是字符串，数据要JSON_ENCODE()
        // end只能调用一次，如果需要分多次向客户端发送数据，请使用write方法
        // 客户端开启了KeepAlive，连接将会保持，服务器会等待下一次请求
        $response->end('');

        // 还有create，detach，upgrade，recv，push
    }
}
$serv = new HTTP();