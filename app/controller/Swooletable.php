<?php

declare(strict_types=1);

namespace app\controller;

use think\Request;

class Swooletable
{
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        //

        $table =  app('swoole.table.user');
        $table->set("zq", [
            'id' => 1,
            'name' => "zhiqiang",
            'money' => 100
        ]);
        //获取一行数据
       var_dump($table->get("zq"));
        
        
//      // 修改数据
//      // 字段递增
//      $table->incr("zq", "money", 2);
//      //递减
//      $table->decr("zq", "money", 2);
//      // 返回 table 中存在的条目数。
//      $table->count();
//      //遍历table中的数据
//      foreach ($table as $item) {
//          var_dump($item);
//      }
//      // 检查 table 中是否存在某一个 key。
//      $table->exist('zq');
//      //获取实际占用内存尺寸,单位字节
//      $table->momorySize();
    }
}
