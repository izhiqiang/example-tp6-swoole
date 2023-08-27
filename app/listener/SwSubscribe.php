<?php
declare (strict_types = 1);

namespace app\listener;

class SwSubscribe
{
    protected $ws = null;

    // public function __construct()
    // {
    //     $this->ws = app('think\swoole\Websocket');
    // }

    public function __construct(\think\Container $c)
    {
        $this->ws = $c->make(\think\swoole\Websocket::class);
    }
    
    public function onConnect()
    {
        $fd = $this->ws->getSender();
        echo "server: handshake success with fd{$fd}\n";
    }
    public function onClose()
    {
        $fd = $this->ws->getSender();
        echo "client {$fd} closed\n";
    }
    public function onMessage($event)
    {
        $fd = $this->ws->getSender();
        var_dump($event);
        echo "server: handshake success with fd{$fd}\n";
        $this->ws->emit("this is server", $fd);
    }
}
