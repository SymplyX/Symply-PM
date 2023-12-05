<?php

/*
 *
 *  _____                       _
 * /  ___|                     | |
 * \ `--. _   _ _ __ ___  _ __ | |_   _
 *  `--. \ | | | '_ ` _ \| '_ \| | | | |
 * /\__/ / |_| | | | | | | |_) | | |_| |
 * \____/ \__, |_| |_| |_| .__/|_|\__, |
 *         __/ |         | |       __/ |
 *        |___/          |_|      |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author Symply Team
 * @link http://www.symplymc.com/
 *
 *
 */

declare(strict_types=1);

namespace symply;

use pocketmine\scheduler\TaskScheduler;
use pocketmine\Server;
use symply\behavior\AsyncRegisterBehaviorsTask;
use symply\behavior\SymplyBlockFactory;
use symply\behavior\SymplyItemFactory;
use symply\test\ItemPP;
use symply\test\PP;

class SymplyBypass
{
	private TaskScheduler $scheduler;
	public function __construct(private Server $server)
	{
		$this->scheduler = new TaskScheduler("symply");
	}

	public function onLoad() : void{

	}

	public function onEnable() : void{
		$asyncPool = $this->getServer()->getAsyncPool();
		$asyncPool->addWorkerStartHook(static function(int $worker) use($asyncPool) : void{
			$asyncPool->submitTaskToWorker(new AsyncRegisterBehaviorsTask(), $worker);
		});
	}

	public function getServer() : Server
	{
		return $this->server;
	}

	public function getScheduler() : TaskScheduler
	{
		return $this->scheduler;
	}
}
