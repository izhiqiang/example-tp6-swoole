<?php

declare(strict_types=1);

namespace app\listener;

class SwRoomJoin
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
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

        $ws->join($roomid);
        //获取指定客户端假如的所有room
        // $fdrooms = $room->getRooms($fd);
        //同时加入多个房间
        // $ws ->join([1,2]);
        // 指定客户端加入指定room
        // $ws->setSender(1)->join($rooms)
        // 告诉这个房间中的所有客户端有新的客户端加入
        $ws->to($roomfds)->emit("roomjoincallback", "{$fd}加入房间{$roomid}成功");
        
        
    }
}
