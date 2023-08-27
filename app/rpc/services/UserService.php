<?php
namespace app\rpc\services;
use app\rpc\interfaces\UserInterface;
class UserService implements UserInterface
{
    public function create()
    {
        // TODO: Implement create() method.
        return "service create success";
    }
    public function find(int $id)
    {
        // TODO: Implement find() method.
        return $id. "查询数据遍历";
    }
}