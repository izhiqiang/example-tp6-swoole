<?php
declare (strict_types = 1);

namespace app\command;

use think\console\Command;
use think\console\input\Argument;


class SwooleTimer extends Command
{
    protected function configure()
    {
        // 指令配置
        $this->setName('app\command\swooletimer')
            ->addArgument('action', Argument::OPTIONAL, "start | stop", 'start')
            ->setDescription('Swoole Timer for ThinkPHP');
    }


    public function handle()
    {
        $action = $this->input->getArgument('action');
        if (in_array($action, ['start','stopall'])) {
            $this->app->invokeMethod([$this, $action], [], true);
        } else {
            $this->output->writeln("<error>Invalid argument action:{$action}, Expected start</error>");
        }
    }

    /**
     * 启动定时任务 主要任务计划在这里书写
     */
    protected function start()
    {
        // https://wiki.swoole.com/#/timer
        $timer_id=swoole_timer_tick(2000,function (){
            echo "2s循环执行需要做的事情".time()."\n";
        });
        $this->output->writeln("Swoole Timer_id:{$timer_id} ");
    }

    /**
     * 清除所有的定时任务
     */
    protected  function stop(){
        swoole_timer_clear_all();
        $this->output->writeln("Swoole Timer  clear all ok");
    }
}