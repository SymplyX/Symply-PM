<?php

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