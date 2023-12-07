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

/**
 * @name SymplyPlugin
 * @main \symply\plugin\SymplyPlugin
 * @version 0.0.1
 * @load STARTUP
 * @api 5.0.0
 */

namespace symply\plugin;

use Exception;
use pocketmine\plugin\PluginBase;
use symply\behavior\AsyncRegisterBehaviorsTask;
use symply\plugin\listener\BehaviorListener;
use symply\plugin\listener\ClientBreakListener;
use symply\plugin\listener\ServerBreakListener;
use symply\YmlSymplyProperties;

class SymplyPlugin extends PluginBase
{

	public function onLoad() : void
	{
	}

	protected function onEnable() : void
	{
		$serverBreakSide = $this->getServer()->getConfigGroup()->getSymplyProperty(YmlSymplyProperties::SERVER_BREAK_SIDE, false);
		$this->getServer()->getPluginManager()->registerEvents(new BehaviorListener($serverBreakSide), $this);
		if ($serverBreakSide) {
			$this->getServer()->getPluginManager()->registerEvents(new ServerBreakListener(), $this);
			$this->getLogger()->alert("You have activated the breaking mode managed by the server attention the system and experimental");
		}else{
			$this->getServer()->getPluginManager()->registerEvents(new ClientBreakListener(), $this);
		}
		$asyncPool = $this->getServer()->getAsyncPool();
		$asyncPool->addWorkerStartHook(static function(int $worker) use($asyncPool) : void{
			$asyncPool->submitTaskToWorker(new AsyncRegisterBehaviorsTask(), $worker);
		});
	}

	/**
	 * @throws Exception
	 */
	protected function onDisable(): void
	{
		if ($this->getServer()->isRunning()){
			throw new Exception("you dont can disable this plugin because your break intergrity of Symply");
		}
	}
}
