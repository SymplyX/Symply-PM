<?php

namespace symply\entity\passive;

use pocketmine\entity\EntitySizeInfo;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use symply\behavior\entities\component\behavior\BreedBehavior;
use symply\behavior\entities\component\behavior\FloatBehavior;
use symply\behavior\entities\component\behavior\FollowParentBehavior;
use symply\behavior\entities\component\behavior\LookAtPlayer;
use symply\behavior\entities\component\behavior\PanicBehavior;
use symply\behavior\entities\component\behavior\RandomLookAroundBehavior;
use symply\behavior\entities\component\behavior\RandomStrollBehavior;
use symply\behavior\entities\component\behavior\TemptBehavior;
use symply\entity\PassiveMob;

class Cow extends PassiveMob
{
	protected function getInitialSizeInfo(): EntitySizeInfo
	{
		return new EntitySizeInfo(0.9, 1.3); // change to CollisionBoxGeneric
	}

	public static function getNetworkTypeId(): string
	{
		return "minecraft:cow";
	}

	public function getName(): string
	{
		return "Cow";
	}

	public function initEntity(CompoundTag $nbt): void
	{
		parent::initEntity($nbt);
		$this->getBehaviorManager()->setBehavior(new FloatBehavior(0, $this));
		$this->getBehaviorManager()->setBehavior(new PanicBehavior(1, $this,1.25));
		//$this->getBehaviorManager()->setBehavior(new MountPathingBehavior(2, $this, 1.5, 0.0, true)); IDK WHY
		$this->getBehaviorManager()->setBehavior(new BreedBehavior(3, $this,1.0));
		$this->getBehaviorManager()->setBehavior(new TemptBehavior(4, $this,1.25, [VanillaItems::WHEAT()]));
		$this->getBehaviorManager()->setBehavior(new FollowParentBehavior(5, $this, 1.25));
		$this->getBehaviorManager()->setBehavior(new RandomStrollBehavior(6, $this, 0.8));
		$this->getBehaviorManager()->setBehavior(new LookAtPlayer(7, $this, 6, 0.02));
		$this->getBehaviorManager()->setBehavior(new RandomLookAroundBehavior(8, $this));
	}

	public function getDrops() : array{
		return [
			VanillaItems::LEATHER()->setCount(rand(0, 2)),
			($this->isOnFire() ? VanillaItems::RAW_BEEF() : VanillaItems::STEAK())->setCount(rand(1, 3))
		];
	}

	public function isBaby(): bool
	{
		return false;
	}
}