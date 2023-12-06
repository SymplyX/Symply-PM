<?php

namespace symply\behavior\entities;

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
					if(!$this->canUse($behavior) or !$behavior->canContinue()){
						$behavior->onEnd();

						unset($this->workingBehaviors[$id]);
					}
				}

				if($this->canUse($behavior) and $behavior->canStart()){
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
					if(!$this->theyCanWorkCompatible($entry, $behaviorEntry) and isset($this->workingBehaviors[$id])){
						return false;
					}
				}elseif(!$behaviorEntry->isMutable() and isset($this->workingBehaviors[$id])){
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