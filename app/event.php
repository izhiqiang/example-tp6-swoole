<?php
// 事件定义文件

return [
    'bind'      => [],

    'listen'    => [
        'AppInit'  => [],
        'HttpRun'  => [],
        'HttpEnd'  => [],
        'LogLevel' => [],
        'LogWrite' => [],

        'swoole.task'=>[
            \app\listener\SwSendEmailTask::class
        ],
        'swoole.finish'=>[
            \app\listener\SwSendEmailFinish::class
        ],
        // // 监听链接
        // 'swoole.websocket.Connect' => [
        //     \app\listener\SwWsConnect::class
        // ],
        // //关闭连接
        // 'swoole.websocket.Close' => [
        //     \app\listener\SwWsClose::class
        // ],
        // //发送消息场景
        // 'swoole.websocket.Message' => [
        //     \app\listener\SwWsMessage::class
        // ],

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

    ],

    'subscribe' => [],
];
