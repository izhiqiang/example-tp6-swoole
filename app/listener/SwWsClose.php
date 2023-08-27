<?php

declare(strict_types=1);

namespace app\listener;

class SwWsClose
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event, \think\swoole\websocket $ws)
    {
        $fd = $ws->getSender();
        echo "client {$fd} closed\n";
    }
}
