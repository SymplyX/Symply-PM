<?php

namespace symply\behavior\entities\component\behavior;

use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use symply\behavior\entities\BehaviorMob;
use symply\behavior\entities\IComponentMob;
use symply\entity\Mob;

/**
 * Allows the mob to enter the panic state, which makes it run around and away from the damage source that made it enter this state.
 * @package symply\behavior\entities\component
 */
class PanicBehavior extends BehaviorMob
{
	private ?Vector3 $targetPos;

	/**
	 * @param int $priority
	 * @param Mob $mob
	 * @param float $speedMultiplier Movement speed multiplier of the mob when using this AI Goal
	 * @param bool $preferWater If true, the mob will prefer water over land
	 * @param bool $ignoreMobDamage If true, the mob will not panic in response to damage from other mobs. This overrides the damage types in "damage_sources"
	 * @param bool $force If true, this mob will not stop panicking until it can't move anymore or the goal is removed from it
	 * @param array $damageSources The list of Entity Damage Sources that will cause this mob to panic
	 */
	public function __construct(
		int $priority, Mob $mob,

		protected float $speedMultiplier = 1.0,
		protected bool $preferWater = false,
		protected bool $ignoreMobDamage = false,
		protected bool $force = false,
		protected array $damageSources = []
	) {
		parent::__construct($priority, $mob);
	}

	public function getName(): string
	{
		return "minecraft:behavior.panic";
	}

	public function canStart() : bool{
		return false;
	}

	public function toNbt() : CompoundTag
	{
		return CompoundTag::create()->setTag("minecraft:behavior.panic", CompoundTag::create()
			->setInt("priority", $this->getPriority())
			->setFloat("speed_multiplier", $this->speedMultiplier)
			->setByte("prefer_water", $this->preferWater ? 1 : 0)
			->setByte("ignore_mob_damage", $this->ignoreMobDamage ? 1 : 0)
			->setByte("force", $this->force ? 1 : 0)
			//->setTag("damage_sources", $this->damageSources)
		);
	}
}