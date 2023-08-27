<?php

declare(strict_types=1);

namespace app\listener;

class SwRoomLeave
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event, \think\swoole\websocket $ws, \think\swoole\websocket\Room $room)
    {
        //
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
}
