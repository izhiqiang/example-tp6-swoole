<?php
namespace app\rpc\interfaces;
interface UserInterface
{
    public function create();
    public function find(int $id);
}