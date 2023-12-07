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
 * Allows the mob to follow their parent around.
 * @package symply\behavior\entities\component
 */
class FollowParentBehavior extends BehaviorMob
{
	/**
	 * FollowParentBehavior constructor.
	 * @param float $speedMultiplier Movement speed multiplier of the mob when using this AI Goal
	 */
	public function __construct(
		int $priority, Mob $mob,

		protected float $speedMultiplier = 1
	) {
		parent::__construct($priority, $mob);
	}
	public function getName() : string
	{
		return "minecraft:behavior.follow_parent";
	}

	public function canStart() : bool
	{
		return false;
	}

	public function toNbt() : CompoundTag
	{
		return CompoundTag::create()->setTag("minecraft:behavior.follow_parent", CompoundTag::create()
			->setInt("priority", $this->getPriority())
			->setFloat("speed_multiplier", $this->speedMultiplier)
		);
	}
}
