<?php
declare (strict_types = 1);

namespace app\listener;

class SwSendEmailTask
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event)
    {
        var_dump($event);
        //
        echo "开发发送邮件".time();
        sleep(3);
        echo "结束发送邮件".time();

        $event->finish(\app\listener\SwSendEmailFinish::class);
    }    
}
