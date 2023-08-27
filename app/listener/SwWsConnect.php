<?php
declare (strict_types = 1);

namespace app\listener;

class SwWsConnect
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event,\think\swoole\websocket $ws)
    {
        //用户数据uid 和fd 进行绑定操作
        // 保存的位置 mysql table redis:hash
        // 获取当前发送者的fd
        $fd = $ws->getSender();
        echo "server: handshake success with fd{$fd}\n";
    }    
}
