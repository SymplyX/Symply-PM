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

namespace symply\entity;

use pocketmine\entity\Location;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\utils\Random;
use symply\behavior\entities\BehaviorManager;

abstract class Mob extends Subsist
{
	private BehaviorManager $behaviorManager;
	private BehaviorManager $targetBehaviorManager;
	private Random $random;

	public function __construct(Location $location, ?CompoundTag $nbt = null){
		parent::__construct($location, $nbt);
		$this->behaviorManager = new BehaviorManager();
		$this->targetBehaviorManager = new BehaviorManager();
		$this->random = new Random($this->getId());
	}

	public function getBehaviorManager() : BehaviorManager{
		return $this->behaviorManager;
	}

	public function getTargetBehaviorManager() : BehaviorManager{
		return $this->targetBehaviorManager;
	}
}
