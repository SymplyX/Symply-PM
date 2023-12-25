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

namespace symply\behavior;

use pmmp\thread\ThreadSafeArray;
use pocketmine\scheduler\AsyncTask;
use ReflectionException;

class AsyncRegisterBehaviorsTask extends AsyncTask
{

	private ThreadSafeArray $asyncTransmitterBlockCustom;
	private ThreadSafeArray $asyncTransmitterItemOverwrite;
	private ThreadSafeArray $asyncTransmitterItemCustom;
	private ThreadSafeArray $asyncTransmitterBlockOverwrite;

	public function __construct()
	{
		$this->asyncTransmitterBlockOverwrite = SymplyBlockFactory::getInstance()->getAsyncTransmitterBlockOverwrite();
		$this->asyncTransmitterBlockCustom = SymplyBlockFactory::getInstance()->getAsyncTransmitterBlockCustom();
		$this->asyncTransmitterItemOverwrite = SymplyItemFactory::getInstance()->getAsyncTransmitterItemOverwrite();
		$this->asyncTransmitterItemCustom = SymplyItemFactory::getInstance()->getAsyncTransmitterItemCustom();
	}

	/**
	 * @inheritDoc
	 * @throws ReflectionException
	 */
	public function onRun() : void
	{
		$symplyBlockFactory = SymplyBlockFactory::getInstanceModeAsync();
		$symplyItemFactory = SymplyItemFactory::getInstanceModeAsync();
		foreach ($this->asyncTransmitterBlockOverwrite as $closure){
			$symplyBlockFactory->overwriteBlockPMMP($closure[0], $closure[1], $closure[2]);
		}
		foreach ($this->asyncTransmitterBlockCustom as $closure) {
			$symplyBlockFactory->register($closure[0], $closure[1], $closure[2]);
		}
		foreach ($this->asyncTransmitterItemOverwrite as $closure){
			$symplyItemFactory->overwriteItemPMMP($closure[0], $closure[1], $closure[2]);
		}
		foreach ($this->asyncTransmitterItemCustom as $closure){
			$symplyItemFactory->register($closure[0], $closure[1], $closure[2]);
		}
	}
}
