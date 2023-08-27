<?php

/**
 * This file is auto-generated.
 */

declare(strict_types=1);

namespace rpc\contract\tp6;

interface UserInterface
{
	public function create();


	public function find(int $id);
}
return ['tp6' => ['rpc\contract\tp6\UserInterface']];