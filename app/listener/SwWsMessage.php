<?php

declare(strict_types=1);

namespace app\listener;

class SwWsMessage
{
    /**
     * 接受ws.send发送的数据
     * 发送数据格式
     * ws.send(JSON.stringify(['message',"发送数据"]));
     * $event 接受到的是发送数据，可以是字符串也可以是数组
     * @return mixed
     */
    public function handle($event, \think\swoole\websocket $ws)
    {
        $fd = $ws->getSender();


        $data = json_encode($event);
        echo "receive from {$fd}:{$data}\n";
        // 给自己发信息
        // $ws->emit("this is server", $fd);


        // 给指定fd发信息
        // $to = intval($event['to']);
        // $ws->to($to)->emit("messagecallback", [
        //     'form' => [
        //         'uid'=>1,
        //         'fd'=> $fd,
        //     ],
        //     'to' => [
        //         'uid'=>2,
        //         'fd'=> $to
        //     ],
        //     'message' => [
        //         'content'=>$event['msg']
        //     ]
        // ]);


        $tos = $event['to'];
        $ws->to(explode(",", $tos))->emit("messagecallback", [
            'form' => [
                'uid' => 1,
            ],
            'to' => [
                'uid' => 2,
            ],
            'message' => [
                'content' => $event['msg']
            ]
        ]);


        //发送给所有的(不包含自己)
        // $ws->broadcast()->emit("messagecallback", [
        //     'form' => [
        //         'uid' => 1,
        //         'fd' => $fd,
        //     ],
        //     'to' => [
        //         'uid' => 2,
        //         'fd' => $to
        //     ],
        //     'message' => [
        //         'content' => $event['msg']
        //     ]
        // ]);

        //模拟formfd 给tofd 发送消息
        // $ws->setSender($formfd)->to($tofd)->emit("messagecallback","模拟formfd 给tofd 发送消息");
    
        

        $swa = app('swoole.server');
        $sws = app("think\swoole\Manager")->getServer();

   
        $es = $sws->isEstablished(2);

        var_dump($es);

        // var_dump( get_class($swa) );
        // var_dump( get_class($sws));
    
    }
}
