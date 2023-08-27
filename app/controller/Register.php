<?php

declare(strict_types=1);

namespace app\controller;

use app\common\controller\Controller;
use think\Request;

class Register extends Controller
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //
        return "注册页面";
    }

    public function doRegister()
    {
        $server = app('swoole.server');
        $server->task(\app\listener\SwSendEmailTask::class);
        return "注册成功";
    }

    // public function doRegister(\think\swoole\Manager $manager)
    // {
    //     $server = $manager->getServer();
    //     $server->task(\app\listener\SwSendEmailTask::class);
    //     return "注册成功";
    // }
    // public function doRegister(\Swoole\Server $server)
    // {
    //     # code...
    //     $server->task(\app\listener\SwSendEmailTask::class);

    //     return "注册成功";
    // }

}
