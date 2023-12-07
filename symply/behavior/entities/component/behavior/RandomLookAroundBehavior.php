<?php

namespace symply\behavior\entities\component\behavior;

use pocketmine\math\Vector2;
use pocketmine\nbt\tag\CompoundTag;
use symply\behavior\entities\BehaviorMob;
use symply\behavior\entities\IComponentMob;
use symply\entity\Mob;

/**
 * Allows the mob to randomly look around.
 * @package symply\behavior\entities\component
 */
class RandomLookAroundBehavior extends BehaviorMob
{
	/**
	 * @param int $priority
	 * @param Mob $mob
	 * @param Vector2 $lookTime The range of time in seconds the mob will stay looking in a random direction before looking elsewhere
	 * @param int $maxAngleOfViewHorizontal The rightmost angle a mob can look at on the horizontal plane with respect to its initial facing direction.
	 * @param int $maxAngleOfViewVertical The leftmost angle a mob can look at on the horizontal plane with respect to its initial facing direction.
	 */
	public function __construct(
		int $priority, Mob $mob,

		protected Vector2 $lookTime = new Vector2(2, 4),
		protected int $maxAngleOfViewHorizontal = 30,
		protected int $maxAngleOfViewVertical = -30

	) {
		parent::__construct($priority, $mob);
	}

	public function getName(): string
	{
		return "minecraft:behavior.random_look_around";
	}

	public function canStart(): bool
	{
		return false;
	}

	public function toNbt(): CompoundTag
	{
		return CompoundTag::create()->setTag("minecraft:behavior.random_look_around", CompoundTag::create()
			->setInt("priority", $this->getPriority())
			->setIntArray("look_time", [$this->lookTime->x, $this->lookTime->y])
			->setInt("max_angle_of_view_horizontal", $this->maxAngleOfViewHorizontal)
			->setInt("max_angle_of_view_vertical", $this->maxAngleOfViewVertical)
		);
	}
}