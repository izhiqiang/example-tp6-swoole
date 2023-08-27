<?php
declare (strict_types = 1);

namespace app\listener;

class SwSendEmailFinish
{
    /**
     * 事件监听处理
     *
     * @return mixed
     */
    public function handle($event)
    {

        //
        echo "发送邮件处理完毕，其他逻辑处理";

        var_dump($event);
    }    
}
