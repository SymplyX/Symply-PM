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

use function count;
use function spl_object_id;

class BehaviorManager
{
	/** @var BehaviorMob[] */
	private array $entriesBehaviors = [];

	/** @var BehaviorMob[] */
	private array $workingBehaviors = [];

	private int $counterTick = 0;

	private int $rateTick = 3;

	public function setBehavior(BehaviorMob $behavior) : void{
		$this->entriesBehaviors[spl_object_id($behavior)] = $behavior;
	}

	public function removeBehavior(BehaviorMob $behavior) : void{
		unset($this->entriesBehaviors[spl_object_id($behavior)]);
	}

	public function onUpdate() : bool{
		if($this->counterTick++ % $this->rateTick === 0){
			foreach($this->entriesBehaviors as $id => $behavior){

				if(isset($this->workingBehaviors[$id])){
					if(!$this->canUse($behavior) || !$behavior->canContinue()){
						$behavior->onEnd();

						unset($this->workingBehaviors[$id]);
					}
				}

				if($this->canUse($behavior) && $behavior->canStart()){
					$behavior->onStart();

					$this->workingBehaviors[$id] = $behavior;
				}
			}
		}else{
			foreach($this->workingBehaviors as $id => $behavior){
				if(!$behavior->canContinue()){
					$behavior->onEnd();

					unset($this->workingBehaviors[$id]);
				}
			}
		}

		foreach($this->workingBehaviors as $behavior){
			$behavior->onTick();
		}

		return count($this->workingBehaviors) > 0;
	}

	public function canUse(BehaviorMob $entry) : bool{
		foreach($this->entriesBehaviors as $id => $behaviorEntry){
			if($behaviorEntry !== $entry){
				if($entry->getPriority() >= $behaviorEntry->getPriority()){
					if(!$this->theyCanWorkCompatible($entry, $behaviorEntry) && isset($this->workingBehaviors[$id])){
						return false;
					}
				}elseif(!$behaviorEntry->isMutable() && isset($this->workingBehaviors[$id])){
					return false;
				}
			}
		}

		return true;
	}

	public function theyCanWorkCompatible(BehaviorMob $b1, BehaviorMob $b2) : bool{
		return ($b1->getMutexBits() & $b2->getMutexBits()) === 0;
	}

	public function getTickRate() : int{
		return $this->rateTick;
	}

	public function setTickRate(int $tickRate) : void{
		$this->rateTick = $tickRate;
	}

	/**
	 * @return BehaviorMob[]
	 */
	public function getBehaviorEntries() : array{
		return $this->entriesBehaviors;
	}

	public function clearBehaviors() : void{
		$this->entriesBehaviors = [];
		$this->workingBehaviors = [];
	}
}
