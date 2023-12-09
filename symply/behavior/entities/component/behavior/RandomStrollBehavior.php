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

use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use symply\behavior\entities\BehaviorMob;
use symply\entity\Mob;

/**
 * Allows a mob to randomly stroll around.
 * @package symply\behavior\entities\component
 */
class RandomStrollBehavior extends BehaviorMob
{
	protected ?Vector3 $targetPos = null;

	/**
	 * RandomStrollBehavior constructor.
	 * @param float $speedMultiplier Movement speed multiplier of the mob when using this AI Goal
	 * @param int   $interval        A random value to determine when to randomly move somewhere. This has a 1/interval chance to choose this goal
	 * @param int   $xzDist          Distance in blocks on ground that the mob will look for a new spot to move to. Must be at least 1
	 * @param int   $yDist           Distance in blocks that the mob will look up or down for a new spot to move to. Must be at least 1
	 */
	public function __construct(
		int $priority, Mob $mob,

		protected float $speedMultiplier = 1.0,
		protected int $interval = 120,
		protected int $xzDist = 10,
		protected int $yDist = 7,
	){
		parent::__construct($priority, $mob);
	}

	public function getName() : string
	{
		return "minecraft:behavior.random_stroll";
	}

	public function canStart() : bool{
		return false;
	}

	public function toNbt() : CompoundTag
	{
		return CompoundTag::create()->setTag("minecraft:behavior.random_stroll", CompoundTag::create()
			->setInt("priority", $this->getPriority())
			->setFloat("speed_multiplier", $this->speedMultiplier)
			->setInt("interval", $this->interval)
			->setInt("xz_dist", $this->xzDist)
			->setInt("y_dist", $this->yDist)
		);
	}
}
