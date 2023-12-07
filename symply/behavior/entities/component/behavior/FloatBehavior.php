<?php

namespace symply\behavior\entities\component\behavior;

use pocketmine\nbt\tag\CompoundTag;
use symply\behavior\entities\BehaviorMob;
use symply\behavior\entities\IComponentMob;
use symply\entity\Mob;

/**
 * Allows the mob to stay afloat while swimming. Passengers will be kicked out the moment the mob's head goes underwater, which may not happen for tall mobs.
 * @package symply\behavior\entities\component
 */
class FloatBehavior extends BehaviorMob
{
	/**
	 * @param int $priority
	 * @param Mob $mob
	 * @param bool $sinkWithPassengers If true, the mob will keep sinking as long as it has passengers.
	 */
	public function __construct(
		int $priority, Mob $mob,

		protected bool $sinkWithPassengers = false
	) {
		parent::__construct($priority, $mob);
	}
	public function getName(): string
	{
		return "minecraft:behavior.float";
	}

	public function canStart() : bool{
		return false;
	}

	public function toNbt(): CompoundTag
	{
		return CompoundTag::create()->setTag("minecraft:behavior.float", CompoundTag::create()
			->setInt("priority", $this->getPriority())
			->setByte("sink_with_passengers", $this->sinkWithPassengers ? 1 : 0)
		);
	}
}