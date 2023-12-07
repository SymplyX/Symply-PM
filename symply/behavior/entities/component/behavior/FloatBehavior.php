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

namespace symply\behavior\entities\component\behavior;

use pocketmine\nbt\tag\CompoundTag;
use symply\behavior\entities\BehaviorMob;
use symply\entity\Mob;

/**
 * Allows the mob to stay afloat while swimming. Passengers will be kicked out the moment the mob's head goes underwater, which may not happen for tall mobs.
 * @package symply\behavior\entities\component
 */
class FloatBehavior extends BehaviorMob
{
	/**
	 * @param bool $sinkWithPassengers If true, the mob will keep sinking as long as it has passengers.
	 */
	public function __construct(
		int $priority, Mob $mob,

		protected bool $sinkWithPassengers = false
	) {
		parent::__construct($priority, $mob);
	}
	public function getName() : string
	{
		return "minecraft:behavior.float";
	}

	public function canStart() : bool{
		return false;
	}

	public function toNbt() : CompoundTag
	{
		return CompoundTag::create()->setTag("minecraft:behavior.float", CompoundTag::create()
			->setInt("priority", $this->getPriority())
			->setByte("sink_with_passengers", $this->sinkWithPassengers ? 1 : 0)
		);
	}
}
