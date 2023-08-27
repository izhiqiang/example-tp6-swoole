<?php
namespace app\controller;

use app\common\controller\Controller;

class Index extends Controller
{

    public function index()
    {
        return  "Index";
    }

    public function hello($name = 'ThinkPHP6')
    {
        return 'hello,' . $name;
    }
}
