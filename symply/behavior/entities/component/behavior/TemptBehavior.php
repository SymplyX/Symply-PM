<?php

namespace symply\behavior\entities\component\behavior;

use pocketmine\math\Vector2;
use pocketmine\nbt\tag\CompoundTag;
use symply\behavior\entities\BehaviorMob;
use symply\behavior\entities\IComponentMob;
use symply\entity\Mob;

/**
 * Allows the mob to be tempted by food they like.
 * @package symply\behavior\entities\component
 */
class TemptBehavior extends BehaviorMob
{
	public function __construct(
		int $priority, Mob $mob,

		protected float $speedMultiplier = 1,
		protected array $items = [],
		protected bool $canBeScared = false,
		protected bool $canTemptVertically= false,
		protected bool $canTemptWhileRidden = false,
		protected Vector2 $soundInterval = new Vector2(0.0, 0.0),
		protected ?string $temptSound = null,
		protected float $withinRadius = 0
	) {
		parent::__construct($priority, $mob);
	}
	public function getName(): string
	{
		return "minecraft:behavior.tempt";
	}

	public function canStart() : bool{
		return false;
	}

	public function toNbt() : CompoundTag
	{
		return CompoundTag::create()->setTag("minecraft:behavior.tempt", CompoundTag::create()
			->setInt("priority", $this->getPriority())
			->setFloat("speed_multiplier", $this->speedMultiplier)
			//->setTag("items", $this->items) idk how to do this
			->setByte("can_be_scared", $this->canBeScared ? 1 : 0)
			->setByte("can_tempt_vertically", $this->canTemptVertically ? 1 : 0)
			->setByte("can_tempt_while_ridden", $this->canTemptWhileRidden ? 1 : 0)
			->setIntArray("sound_interval", [$this->soundInterval->x, $this->soundInterval->y])
			->setString("tempt_sound", $this->temptSound)
			->setFloat("within_radius", $this->withinRadius)
		);
	}
}