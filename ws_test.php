<?php
use Workerman\Worker;
require_once __DIR__ . '/Workerman/Autoloader.php';

// 心跳间隔55秒
define('HEARTBEAT_TIME', 55);

// 注意：这里与上个例子不同，使用的是websocket协议
<<<<<<< HEAD
$worker = new Worker("websocket://192.168.0.102:2000");

$worker->count = 1;
// 新增加一个属性，用来保存uid到connection的映射(uid是用户id或者客户端唯一标识)
$worker->uidConnections = array();
// 当有客户端发来消息时执行的回调函数
$worker->onMessage = function($connection, $data)
{
    global $worker;
    // 判断当前客户端是否已经验证,即是否设置了uid
    if(!isset($connection->uid))
    {
        // 没验证的话把第一个包当做uid（这里为了方便演示，没做真正的验证）
        $connection->uid = $data;
        /* 保存uid到connection的映射，这样可以方便的通过uid查找connection，
         * 实现针对特定uid推送数据
         */
        $worker->uidConnections[$connection->uid] = $connection;
        return;
    }
    // 其它逻辑，针对某个uid发送 或者 全局广播
    // 假设消息格式为 uid:message 时是对 uid 发送 message
    // uid 为 all 时是全局广播
    list($recv_uid, $message) = explode('%%', $data);
    // 全局广播
    if($recv_uid == 'all')
    {
        broadcast($message);
    }
    // 给特定uid发送
    else
    {
        sendMessageByUid($recv_uid, $message);
    }
=======
$worker = new Worker("websocket://192.168.0.38:2000");

// ====这里进程数必须必须必须设置为1====
$worker->count = 1;
// 新增加一个属性，用来保存uid到connection的映射(uid是用户id或者客户端唯一标识)
$worker->uidConnections = array();
// 当有客户端发来消息时执行的回调函数
$worker->onMessage = function($connection, $data)
{
    global $worker;
    // 给connection临时设置一个lastMessageTime属性，用来记录上次收到消息的时间
    // 首次连接设置uid 并返回给客户端
    if(!isset($connection->uid))
    {

        // 没验证的话把第一个包当做uid（这里为了方便演示，没做真正的验证）
        $connection->uid = $data;
        /* 保存uid到connection的映射，这样可以方便的通过uid查找connection，
         * 实现针对特定uid推送数据
         */
        $worker->uidConnections[$connection->uid] = $connection;
        return ;
    }

    // 其它逻辑，针对某个uid发送 或者 全局广播
    // 假设消息格式为 uid:message 时是对 uid 发送 message
    // uid 为 all 时是全局广播
    if($data == "xt"){ //心跳数据
        $connection->lastMessageTime = time();
        return ;
    }else{
        list($recv_uid, $message) = explode('%%', $data);
        // 全局广播
        if($recv_uid == 'all')
        {
            broadcast($message);
        }
        // 给特定uid发送
        else
        {
            sendMessageByUid($connection,$recv_uid, $message);
        }
    }
};

// 进程启动后设置一个每秒运行一次的定时器
$worker->onWorkerStart = function($worker) {
    \Workerman\Lib\Timer::add(1, function()use($worker){
        $time_now = time();
        foreach($worker->connections as $connection) {
            // 有可能该connection还没收到过消息，则lastMessageTime设置为当前时间
            if (empty($connection->lastMessageTime)) {
                $connection->lastMessageTime = $time_now;
                continue;
            }
            // 上次通讯时间间隔大于心跳间隔，则认为客户端已经下线，关闭连接
            if ($time_now - $connection->lastMessageTime > HEARTBEAT_TIME) {
                $connection->close();
            }
        }
    });
>>>>>>> 506a038fbe56e631ce8655a6043237458587c759
};

// 当有客户端连接断开时
$worker->onClose = function($connection)
{
    global $worker;
    if(isset($connection->uid))
    {
        // 连接断开时删除映射
        unset($worker->uidConnections[$connection->uid]);
    }
};

// 向所有验证的用户推送数据
function broadcast($message)
{
    global $worker;
    foreach($worker->uidConnections as $connection)
    {
        $connection->send($message);
    }
}

// 针对uid推送数据
<<<<<<< HEAD
function sendMessageByUid($uid, $message)
{
=======
function sendMessageByUid($fromConnection,$uid, $message)
{

>>>>>>> 506a038fbe56e631ce8655a6043237458587c759
    global $worker;
    if(isset($worker->uidConnections[$uid]))
    {
        $connection = $worker->uidConnections[$uid];
<<<<<<< HEAD

        $connection->send($message);
=======
        $connection->send($message);
        if(substr($fromConnection->uid,0,1)!=h){
            $fromConnection->send("对方在线");
        }
    }else{
        if(substr($fromConnection->uid,0,1)!=h){
            $fromConnection->send("对方不在线");
        }
>>>>>>> 506a038fbe56e631ce8655a6043237458587c759
    }
}

// 运行所有的worker（其实当前只定义了一个）
Worker::runAll();