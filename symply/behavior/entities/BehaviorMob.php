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

namespace symply\behavior\entities;

use symply\behavior\common\component\IComponent;
use symply\entity\Mob;

abstract class BehaviorMob implements IComponent
{
	public function __construct(private int $priority, protected Mob $mob) {
	}

	public function getMutexBits() : int{
		return 0;
	}

	public abstract function canStart() : bool;

	public function onStart() : void {

	}

	public function onTick() : void{
	}

	public function onEnd() : void{
	}

	public function isMutable() : bool{
		return true;
	}

	public function getMob() : Mob{
		return $this->mob;
	}

	public function canContinue() : bool{
		return $this->canStart();
	}

	public function getPriority() : int{
		return $this->priority;
	}
}
