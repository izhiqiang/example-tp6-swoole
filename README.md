## 官网文档
> thinkphp6文档
> https://www.kancloud.cn/manual/thinkphp6_0/1037479
>
> swoole文档
> https://wiki.swoole.com/#/
>
> think-swoole文档
> https://www.kancloud.cn/manual/thinkphp6_0/1359700


## 安装

~~~
composer require topthink/think-swoole
~~~

## 命令行

```
php think swoole [start|stop|reload|restart]
```

## 服务启动

当你在命令行`php think swoole`下执行完成之后就会启动一个HTTP Server，可以直接访问当前的应用

~~~
'server'     => [
    'host'      => env('SWOOLE_HOST', '0.0.0.0'), // 监听地址
    'port'      => env('SWOOLE_PORT', 9501), // 监听端口
    'mode'      => SWOOLE_PROCESS, // 运行模式 默认为SWOOLE_PROCESS
    'sock_type' => SWOOLE_SOCK_TCP, // sock type 默认为SWOOLE_SOCK_TCP
    'options'   => [
    	// 服务启动后，进程ID存放文件
        'pid_file'              => runtime_path() . 'swoole.pid',
        // swoole 的日志文件
        'log_file'              => runtime_path() . 'swoole.log',
        // 守护进程模式设置 true 后台运行
        'daemonize'             => false,
        // 设置启动的reactor线程数
        'reactor_num'           => swoole_cpu_num(),
        // 设置启动的worker进程数
        'worker_num'            => swoole_cpu_num(),
        //配置Task进程的数量
        'task_worker_num'       => swoole_cpu_num(),
        //开启静态文件请求处理，需配合document_root
        'enable_static_handler' => true,
        //静态文件根目录
        'document_root'         => root_path('public'),
        // 设置最大数据包尺寸，单位字节
        'package_max_length'    => 20 * 1024 * 1024,
        //配置发送输出缓冲区内存尺寸
        'buffer_output_size'    => 10 * 1024 * 1024,
        //设置客户端连接最大允许占用的内存数量
        'socket_buffer_size'    => 128 * 1024 * 1024,
    ],
],
~~~

## 热更新

swoole服务器运行过程中php文件是常驻内存运行，这样就可以避免重复的读取磁盘，重复的解释编译php，以便达到最高的性能，所以修改代码需要重启服务

think-swoole扩展提供热更新功能，在检测相关文件有更新会自动重启，不在需要手动完成重启，方便开发调试

生产环境下不建议开始文件监控，性能损耗，正常情况下你所修改的文件需要确认无误才能进行更新部署

`.env`里面设置`APP_DEBUG = true`会默认开启热更新

~~~
'hot_update' => [
    'enable'  => env('APP_DEBUG', false),
    'name'    => ['*.php'],
    'include' => [app_path()],
    'exclude' => [],
],
~~~

参数说明

| 参数    | 说明                     |
| ------- | ------------------------ |
| enable  | 是否开启热更新           |
| name    | 监听哪些类型的文件变动   |
| include | 监听哪些目录下的文件变动 |
| exclude | 排除目录                 |

## websocket

先来一个官方的例子

~~~
$server = new Swoole\WebSocket\Server("0.0.0.0", 9501);
$server->on('open', function (Swoole\WebSocket\Server $server, $request) {
    echo "server: handshake success with fd{$request->fd}\n";
});
$server->on('message', function (Swoole\WebSocket\Server $server, $frame) {
    echo "receive from {$frame->fd}:{$frame->data}\n";
    $server->push($frame->fd, "this is server");
});
$server->on('close', function ($ser, $fd) {
    echo "client {$fd} closed\n";
});
$server->start();
~~~

开启think-swoole的websocket功能 `\config\swoole.php`

~~~
'websocket'  => [
	'enable'        => true,
],
~~~

创建三个事件

~~~
php think make:listener SwWsConnect
php think make:listener SwWsClose
php think make:listener SwWsMessage
~~~

然后将这三个事件写到到事件监听中，分别有以下2中文件可以修改方式，注意二选一

thinkphp6自带的事件绑定`app\event.php`

~~~
    'listen'    => [
		........
        // 监听链接
        'swoole.websocket.Connect' => [
            \app\listener\SwWsConnect::class
        ],
        //关闭连接
        'swoole.websocket.Close' => [
            \app\listener\SwWsClose::class
        ],
        //发送消息场景
        'swoole.websocket.Message' => [
            \app\listener\SwWsMessage::class
        ]
    ],
~~~

think-swoole事件绑定`config\swoole.php`

~~~
'listen'        => [
    'connect'=>\app\listener\SwWsConnect::class,
    'close'=>\app\listener\SwWsClose::class,
    'message'=> \app\listener\SwWsMessage::class
],
~~~

> 怎么选择是保存在`config\swoole.php`还是`app\event.php`配置中呢？
>
> 首先我们 我们确定一下我们这个项目中存在有几个实时通讯，
>
> 如果只是存在一个实时通讯 个人建议 保存在`config\swoole.php`
>
> 如果是存在多个实时通讯，就保存在`app\event.php`
>
> key值 必须是`swoole.websocket.事件名称` 例如 `swoole.websocket.Message`

开始写事件中中方法

连接事件`app\listener\SwWsConnect.php`

~~~
public function handle($event, \think\swoole\websocket $ws)
{
    // 获取当前发送者的fd
    $fd = $ws->getSender();
    echo "server: handshake success with fd{$fd}\n";
}
~~~

关闭事件`app\listener\SwWsClose.php`

~~~

public function handle($event, \think\swoole\websocket $ws)
{
    $fd = $ws->getSender();
    echo "client {$fd} closed\n";
}
~~~

message事件`app\listener\SwWsMessage.php`

~~~
public function handle($event, \think\swoole\websocket $ws)
{
	$fd = $ws->getSender();
	$data = json_encode($event);
	echo "receive from {$fd}:{$data}\n";
    $ws->emit("this is server", $fd);
}
~~~

启动`php think swoole`进行测试



think-swoole中的websocket方法总结

~~~
//给自己发消息
$ws->emit("this is server", $ws->getSender());
//给指定一个fd发消息
$ws->to($to)->emit("messagecallback",$data);
//给指定多个人发消息
$ws->to([1,2,3])->emit("messagecallback",$data);
//发送给所有的(不包含自己)
$ws->broadcast()->emit("messagecallback",$data);
//模拟formfd 给tofd 发送消息
$ws->setSender($formfd)->to($tofd)->emit("messagecallback",$data);
~~~

> 注意：在多个实时通讯场景下使用 `emit`
>
> 第一个参数传入  传入 事件名称callback 例如 `messagecallback`



如果你发现你think-swoole中有些没有swoole中的方法可以这么干

~~~
$sw = app('swoole.server');
$sw = app("think\swoole\Manager")->getServer();
//以上二选一

$es = $sw->isEstablished($fd); //检查连接是否为有效的WebSocket客户端连接
var_dump($es);
~~~



## 聊天室room实现

前端文件参考 `html\room.html` 或 `html\room-socket-io.html`

~~~
php think make:listener SwRoomJoin
php think make:listener SwRoomLeave
php think make:listener SwRoomMessage
~~~

事件绑定

~~~
// 加入房间
'swoole.websocket.RoomJoin' => [
	\app\listener\SwRoomJoin::class
],
// 离开房间
'swoole.websocket.Roomleave' => [
	\app\listener\SwRoomLeave::class
],
// 在房间发消息
'swoole.websocket.RoomMessage' => [
	\app\listener\SwRoomMessage::class
]
~~~

加入房间逻辑

~~~
public function handle($event, \think\swoole\websocket $ws, \think\swoole\websocket\room $room)
{
    $fd = $ws->getSender();
    //客户端假如定的room
    $roomid = $event['room'];
    //获取指定房间下有哪些客户端
    $roomfds = $room->getClients($roomid);
    // 判断这个房间有没有自己 如果有自己就不需要再次发送通知
    if (in_array($fd, $roomfds)) {
        $ws->to($roomfds)->emit("roomjoincallback", "房间{$roomid}已加入");
        return;
    }
    //加入房间
    $ws->join($roomid);
    $ws->to($roomfds)->emit("roomjoincallback", "{$fd}加入房间{$roomid}成功");
}
~~~

离开房间逻辑

~~~
public function handle($event, \think\swoole\websocket $ws, \think\swoole\websocket\Room $room)
{
    $roomid = $event['room'];
    $fd = $ws->getSender();
    $roomfds = $room->getClients($roomid);
    if (!in_array($fd, $roomfds)) {
        $ws->emit("roomleavecallback", "{$fd}不在{$roomid}房间内，怎么离开~");
        return;
    }
    //离开房间
    $ws->leave($roomid);
    //获取当前客户端加入了哪些客户端
    $rooms = $room->getRooms($fd);
    $ws->to($roomfds)->emit("roomleavecallback", "{$fd}已离开了~~");
}
~~~

在房间发布聊天逻辑

~~~
    public function handle($event, \think\swoole\websocket $ws, \think\swoole\websocket\room $room)
    {
        //
        $roomid = $event['room'];
        $text = $event['text'];
        $fd = $ws->getSender();
        $roomfds = $room->getClients($roomid);
        if (!in_array($fd, $roomfds)) {
            $ws->emit("roommessagecallback", "{$fd}不在{$roomid}房间内，无法进入发布聊天~");
            return;
        }
        $ws->to($roomfds)->emit("roommessagecallback",  $text);
    }
~~~



## 事件订阅

~~~
php think make:listener SwSubscribe
~~~
app\listener\SwSubscribe.php
~~~
<?php
declare (strict_types = 1);

namespace app\listener;

class SwSubscribe
{
    protected $ws = null;

    // public function __construct()
    // {
    //     $this->ws = app('think\swoole\Websocket');
    // }

    public function __construct(\think\Container $c)
    {
        $this->ws = $c->make(\think\swoole\Websocket::class);
    }
    
    public function onConnect()
    {
        $fd = $this->ws->getSender();
        echo "server: handshake success with fd{$fd}\n";
    }
    public function onClose()
    {
        $fd = $this->ws->getSender();
        echo "client {$fd} closed\n";
    }
    public function onMessage($event)
    {
        $fd = $this->ws->getSender();
        var_dump($event);
        echo "server: handshake success with fd{$fd}\n";
        $this->ws->emit("this is server", $fd);
    }
}

~~~

> 有点类似 将原生的swoole代码改成面向对象代码，生效方法 `config\swoole.php`中在`subscribe` 加入`\app\listener\SwSubscribe::class`
>
> ~~~
> 'subscribe'     => [
> 	\app\listener\SwSubscribe::class
> ],
> ~~~
>
> 在`app\event.php`文件中的 `swoole.websocket.Connect` 相当于 `app\listener\SwSubscribe.php`文件中的`onConnect`函数。如果同时存在的存在的话，就会向客户端发送2次以上的消息





## Task任务投递

https://wiki.swoole.com/#/start/start_task

生成事件

~~~
php think make:listener SwSendEmailTask
~~~

编写发送邮件方法`app\listener\SwSendEmailTask.php`

~~~
public function handle($event)
{
    var_dump($event);
    //
    echo "开发发送邮件".time();
    sleep(3);
    echo "结束发送邮件".time();
}  
~~~

注册事件`app\event.php`

~~~
'swoole.task'=>[
	\app\listener\SwSendEmailTask::class
],
~~~

在控制器中投递任务

~~~
public function doRegister()
{
    $server = app('swoole.server');
    $server->task(\app\listener\SwSendEmailTask::class);
    return "注册成功";
}

public function doRegister(\think\swoole\Manager $manager)
{
    $server = $manager->getServer();
    $server->task(\app\listener\SwSendEmailTask::class);
    return "注册成功";
}
public function doRegister(\Swoole\Server $server)
{
    $server->task(\app\listener\SwSendEmailTask::class);
    return "注册成功";
}
~~~

> 三种获取`\Swoole\Server`,任意选其一

在swoole中还有一个事件叫`finish`，它的作用就是把异步任务的结果返回，在think-swool是这么处理的

定义一个发送邮件异步任务处理结果的事件

~~~
php think make:listener SwSendEmailFinish
~~~

注册事件`app\event.php`

~~~
'swoole.finish'=>[
	\app\listener\SwSendEmailFinish::class
],
~~~

在task任务中调用

~~~
public function handle($event)
{
    var_dump($event);
    //
    echo "开发发送邮件".time();
    sleep(3);
    echo "结束发送邮件".time();
    $event->finish(\app\listener\SwSendEmailFinish::class);
} 
~~~

## 高性能共享内存 Table

https://wiki.swoole.com/#/memory/table

先定结构在进行操作数据（原生swoole操作）

~~~
$table = new Swoole\Table(1024);
//创建表
$table->column("id", Swoole\Table::TYPE_INT);
$table->column("name", Swoole\Table::TYPE_STRING);
$table->column("money", Swoole\Table::TYPE_FLOAT);
$table->create();

//添加数据
$table->set("zq", [
    'id' => 1,
    'name' => "zhiqiang",
    'money' => 100,
]);
//获取一行数据
$table->get("zq");
// 修改数据
// 字段递增
$table->incr("zq","money",2);
//递减
$table->decr("zq","money",2);
// 返回 table 中存在的条目数。
$table->count();
//遍历table中的数据
foreach($table as $item){
    var_dump($item);
}
~~~

think-swoole中的操作

先对table表结构进行初始化`config\swoole.php`

~~~
    'tables'     => [
        'user'=>[
            'size'=>1024,
            'columns'=>[
                [
                    'name'=>'id',
                    'type'=>\Swoole\Table::TYPE_INT
                ],
                [
                    'name'=>'name',
                    'type'=>\Swoole\Table::TYPE_STRING,
                    'size'=>32
                ],
                [
                    'name'=>'money',
                    'type'=>\Swoole\Table::TYPE_FLOAT
                ],

            ],
        ],
    ],
~~~

操作数据

~~~
$table =  app('swoole.table.user');
$table->set("zq", [
    'id' => 1,
    'name' => "zhiqiang",
    'money' => 100
]);
//获取一行数据
$table->get("zq");
// 修改数据
// 字段递增
$table->incr("zq", "money", 2);
//递减
$table->decr("zq", "money", 2);
// 返回 table 中存在的条目数。
$table->count();
//遍历table中的数据
foreach ($table as $item) {
var_dump($item);
}
// 检查 table 中是否存在某一个 key。
$table->exist('zq');
//获取实际占用内存尺寸,单位字节
$table->momorySize();
~~~

## RPC

RPC(Remote Procedure Call)：远程过程调用，它是一种通过网络从远程计算机程序上请求服务，而不需要了解底层网络技术的思想。

详细介绍：https://developer.51cto.com/art/201906/597963.htm

- 解决分布式系统中，服务之间的调用问题。
- 远程调用时，要能够像本地调用一样方便，让调用者感知不到远程调用的逻辑。
- 节点角色说明：
- Server: 暴露服务的服务提供方
- Client: 调用远程服务的服务消费方
- Registry: 服务注册与发现的注册中心

think-swoole实现RPC功能

### 服务器端

#### 接口定义`app/rpc/interfaces/UserInterface.php`

~~~
<?php
namespace app\rpc\interfaces;
interface UserInterface
{
    public function create();
    public function find(int $id);
}
~~~

#### 实现接口`app/rpc/services/UserService.php`

~~~
<?php
namespace app\rpc\services;
use app\rpc\interfaces\UserInterface;
class UserService implements UserInterface
{
    public function create()
    {
        // TODO: Implement create() method.
        return "service create success";
    }
    public function find(int $id)
    {
        // TODO: Implement find() method.
        return $id. "查询数据遍历";
    }
}
~~~

#### 注册rpc服务`config/swoole.php`

~~~
    'rpc'        => [
        'server' => [
        	//开启rpc服务
            'enable'   => true,
            //rpc端口
            'port'     => 9000,
            'services' => [
            	//注册服务
                \app\rpc\services\UserService::class
            ],
        ],
        // 如果填写也是可以调用其他服务端
        'client' => [
        ],
    ],
~~~

启动服务端

~~~
php think swoole start /  php think swoole:rpc
~~~

### 客户端

~~~
    'rpc'        => [
        'server' => [
        ],
        'client' => [
            'tp6'=>[
            	//服务端的ip地址
                'host'=>'127.0.0.1',
                //服务端对应的端口
                'port'=>'9000'
            ]
            // 更多服务端
        ],
    ],
~~~

运行`php think rpc:interface`生成RPC接口文件`app\rpc.php`

~~~
<?php
/**
 * This file is auto-generated.
 */
declare(strict_types=1);
namespace rpc\contract\tp6;
interface UserInterface
{
	public function create();
	public function find(int $id);
}
return ['tp6' => ['rpc\contract\tp6\UserInterface']];
~~~

在控制器调用

~~~
    public function index(\rpc\contract\tp6\UserInterface $user)
    {
        //
        $user->find(1);
//        $user->create();
    }
~~~

## 定时任务

在think-swoole 2.0版本的时候还是支持自定义定时任务配置,详细参考https://github.com/top-think/think-swoole/tree/2.0  

在3.0就不支持了，在这里介绍一个通用的命令行启动定时任务

~~~
php think make:command SwooleTimer
~~~

加载命令行`config/console.php`

~~~
'commands' => [
	'swooletimer'=>app\command\SwooleTimer::class
	...........
],
~~~

书写命令脚本`app/command/SwooleTimer.php`

~~~
<?php
declare (strict_types = 1);

namespace app\command;

use think\console\Command;
use think\console\input\Argument;


class SwooleTimer extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('app\command\swooletimer')
            ->addArgument('action', Argument::OPTIONAL, "start | stop", 'start')
            ->setDescription('Swoole Timer for ThinkPHP');
    }


    public function handle()
    {
        $action = $this->input->getArgument('action');
        if (in_array($action, ['start','stopall'])) {
            $this->app->invokeMethod([$this, $action], [], true);
        } else {
            $this->output->writeln("<error>Invalid argument action:{$action}, Expected start</error>");
        }
    }

    /**
     * 启动定时任务 主要任务计划在这里书写
     */
    protected function start()
    {
        // https://wiki.swoole.com/#/timer
        $timer_id=swoole_timer_tick(2000,function (){
            echo "2s循环执行需要做的事情".time()."\n";
        });
        $this->output->writeln("Swoole Timer_id:{$timer_id} ");
    }

    /**
     * 清除所有的定时任务
     */
    protected  function stop(){
        swoole_timer_clear_all();
        $this->output->writeln("Swoole Timer  clear all ok");
    }
}
~~~






杀死所有指定名称进程
~~~
ps -ef | grep swoole | grep -v grep | awk '{print $2}' | xargs kill -9
~~~

