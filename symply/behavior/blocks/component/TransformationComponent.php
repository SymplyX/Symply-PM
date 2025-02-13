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

namespace symply\behavior\blocks\component;

use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use symply\behavior\common\component\IComponent;
use function intdiv;

final class TransformationComponent implements IComponent
{
	public function __construct(
		private readonly Vector3 $rotation = new Vector3(0,0,0),
		private readonly Vector3 $scale = new Vector3(1,1,1),
		private readonly Vector3 $translation = new Vector3(0,0,0)
	)
	{
	}

	public function getName() : string
	{
		return "minecraft:transformation";
	}

	public function getRotation() : Vector3
	{
		return $this->rotation;
	}

	public function getScale() : Vector3
	{
		return $this->scale;
	}

	public function getTranslation() : Vector3
	{
		return $this->translation;
	}

	public function toNbt() : CompoundTag
	{
		return CompoundTag::create()->setTag($this->getName(),
		CompoundTag::create()
			->setInt("RX", intdiv((int) $this->getRotation()->getX(), 90))
			->setInt("RY",  intdiv((int) $this->getRotation()->getY(), 90))
			->setInt("RZ", intdiv((int) $this->getRotation()->getZ(), 90))
			->setFloat("SX", $this->getScale()->getX())
			->setFloat("SY", $this->getScale()->getY())
			->setFloat("SZ", $this->getScale()->getZ())
			->setFloat("TX", $this->getTranslation()->getX())
			->setFloat("TY", $this->getTranslation()->getY())
			->setFloat("TZ", $this->getTranslation()->getZ())
		);
	}
}
