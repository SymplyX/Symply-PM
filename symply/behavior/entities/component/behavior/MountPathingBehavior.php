<?php

namespace symply\behavior\entities\component\behavior;

use pocketmine\nbt\tag\CompoundTag;
use symply\behavior\entities\BehaviorMob;
use symply\behavior\entities\IComponentMob;
use symply\entity\Mob;
use symply\entity\Tamable;

/**
 * Allows the mob to move around on its own while mounted seeking a target to attack.
 * @package symply\behavior\entities\component
 */
class MountPathingBehavior extends BehaviorMob
{
	/**
	 * @param int $priority
	 * @param Mob $mob the mob tamable
	 * @param float $speed_multiplier Movement speed multiplier of the mob when using this AI Goal
	 * @param float $target_dist The distance at which this mob wants to be away from its target
	 * @param bool $track_target If true, this mob will chase after the target as long as it's a valid target
	 */
	public function __construct(
		int $priority, Mob $mob,

		protected float $speed_multiplier = 1.5,
		protected float $target_dist = 0.0,
		protected bool $track_target = false
	) {
		parent::__construct($priority, $mob);
	}

	public function getName(): string
	{
		return "minecraft:behavior.mount_pathing";
	}

	public function canStart(): bool
	{
		return false;
	}

	public function toNbt() : CompoundTag
	{
		return CompoundTag::create()->setTag("minecraft:behavior.mount_pathing", CompoundTag::create()
			->setInt("priority", $this->getPriority())
			->setFloat("speed_multiplier", $this->speed_multiplier)
			->setFloat("target_dist", $this->target_dist)
			->setByte("track_target", $this->track_target ? 1 : 0)
		);
	}
}