<?php

declare(strict_types=1);

namespace app\listener;

class SwRoomMessage
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
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
}
