<?php
declare (strict_types = 1);

namespace app\controller;


use think\Request;

class rpc
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(\rpc\contract\tp6\UserInterface $user)
    {
        //
        $user->find(1);
//        $user->create();
    }

}
