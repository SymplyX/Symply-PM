<?php

namespace symply\behavior\entities\component\behavior;

use pocketmine\nbt\tag\CompoundTag;
use symply\behavior\entities\BehaviorMob;
use symply\behavior\entities\IComponentMob;
use symply\entity\Mob;

/**
 * Allows this mob to breed with other mobs.
 * @package symply\behavior\entities\component
 */
class BreedBehavior extends BehaviorMob
{
	/**
	 * @param int $priority
	 * @param Mob $mob
	 * @param float $speedMultiplier Movement speed multiplier of the mob when using this AI Goal
	 */
	public function __construct(
		int $priority, Mob $mob,
		protected float $speedMultiplier = 1.0
	) {
		parent::__construct($priority, $mob);
	}

	public function getName(): string
	{
		return "minecraft:behavior.breed";
	}

	public function canStart(): bool
	{
		return false;
	}

	public function toNbt() : CompoundTag
	{
		return CompoundTag::create()->setTag("minecraft:breed", CompoundTag::create()
			->setInt("priority", $this->getPriority())
			->setFloat("speed_multiplier", $this->speedMultiplier)
		);
	}
}