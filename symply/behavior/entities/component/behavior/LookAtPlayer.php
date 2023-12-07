<?php

namespace symply\behavior\entities\component\behavior;

use pocketmine\math\Vector2;
use pocketmine\nbt\tag\CompoundTag;
use symply\behavior\entities\BehaviorMob;
use symply\behavior\entities\IComponentMob;
use symply\entity\Mob;

/**
 * Allows the mob to look at the player when the player is nearby.
 * @package symply\behavior\entities\component
 */
class LookAtPlayer extends BehaviorMob
{
	/**
	 * LookAtPlayer constructor.
	 * @param int $priority
	 * @param Mob $mob
	 * @param int $lookDistance The distance in blocks from which the entity will look at
	 * @param float $probability The probability of looking at the target. A value of 1.00 is 100%
	 * @param int $angleOfViewHorizontal The angle in degrees that the mob can see in the Y-axis (up-down)
	 * @param int $angleOfViewVertical The angle in degrees that the mob can see in the X-axis (left-right)
	 * @param Vector2 $lookTime Time range to look at the entity
	 */
	public function __construct(
		int $priority, Mob $mob,

		protected int $lookDistance = 8,
		protected float $probability = 0.02,
		protected int $angleOfViewHorizontal = 360,
		protected int $angleOfViewVertical = 360,
		protected Vector2 $lookTime = new Vector2(2,4)
	) {
		parent::__construct($priority, $mob);
	}

	public function getName(): string
	{
		return "minecraft:behavior.look_at_player";
	}

	public function canStart(): bool
	{
		return false;
	}

	public function toNbt(): CompoundTag
	{
		return CompoundTag::create()->setTag("minecraft:behavior.look_at_player", CompoundTag::create()
			->setInt("priority", $this->getPriority())
			->setInt("look_distance", $this->lookDistance)
			->setFloat("probability", $this->probability)
			->setInt("angle_of_view_horizontal", $this->angleOfViewHorizontal)
			->setInt("angle_of_view_vertical", $this->angleOfViewVertical)
			->setIntArray("look_time", [$this->lookTime->x, $this->lookTime->y])
		);
	}
}