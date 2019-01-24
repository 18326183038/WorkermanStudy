<?php
use Workerman\Worker;
require_once __DIR__ . '/Workerman/Autoloader.php';

// 创建一个Worker监听2345端口，使用http协议通讯 192.168.159.128
$http_worker = new Worker("http://192.168.0.38:2345");

// 启动4个进程对外提供服务
$http_worker->count = 4;

// 接收到浏览器发送的数据时回复hello world给浏览器
$http_worker->onMessage = function($connection, $data)
{
    // 向浏览器发送hello world
    $connection->send('hello world success  xxx');
};
$http_worker->name="httpWorker";
/*$http_worker->onWorkerStart = function($http_worker)
{
    // 只在id编号为0的进程上设置定时器，其它1、2、3号进程不设置定时器
    if($http_worker->id === 0)
    {
        \Workerman\Lib\Timer::add(1, function(){
            echo "4个worker进程，只在0号进程设置定时器\n";
        });
    }
};*/

// 运行worker
Worker::runAll();