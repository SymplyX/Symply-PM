<?php

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