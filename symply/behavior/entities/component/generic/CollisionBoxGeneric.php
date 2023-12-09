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

namespace symply\behavior\entities\component\generic;

use pocketmine\entity\EntitySizeInfo;

/**
 * Sets the width and height of the Entity's collision box.
 * @package symply\behavior\entities\component
 */
class CollisionBoxGeneric
{
	private EntitySizeInfo $entitySizeInfo;

	public function __construct(float $height, float $width, ?float $eyeHeight = null)
	{
		$this->entitySizeInfo = new EntitySizeInfo($width, $height, $eyeHeight);
	}

	public function getName() : string
	{
		return "minecraft:collision_box";
	}

	public function getEntitySizeInfo() : EntitySizeInfo
	{
		return $this->entitySizeInfo;
	}
}
