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
 * @api 5.0.0
 */

namespace symply\plugin;

use pocketmine\plugin\PluginBase;
use symply\behavior\AsyncRegisterBehaviorsTask;
use symply\behavior\SymplyBlockFactory;
use symply\behavior\SymplyItemFactory;
use symply\test\ItemPP;
use symply\test\ItemPP2;
use symply\test\PP;

class SymplyPlugin extends PluginBase
{
	public function onLoad() : void
	{
		SymplyBlockFactory::getInstance()->register(static fn () => new PP());
		SymplyItemFactory::getInstance()->register(static fn() => new ItemPP());
		SymplyItemFactory::getInstance()->register(static fn() => new ItemPP2());
	}

	protected function onEnable() : void
	{
		$asyncPool = $this->getServer()->getAsyncPool();
		$asyncPool->addWorkerStartHook(static function(int $worker) use($asyncPool) : void{
			$asyncPool->submitTaskToWorker(new AsyncRegisterBehaviorsTask(), $worker);
		});
	}
}
