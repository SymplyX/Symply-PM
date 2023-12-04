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

namespace symply\behavior\block\component;

use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use function intdiv;

class TransformationComponent implements IComponent
{
	public function __construct(
		private readonly Vector3 $rotation = new Vector3(0,0,0),
		private readonly Vector3 $scale = new Vector3(0,0,0),
		private readonly Vector3 $translation = new Vector3(0,0,0)
	)
	{
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

	public static function northRotation() : self{
		return new self(new Vector3(0, 0, 0));
	}

	public static function southRotation() : self{
		return new self(new Vector3(0, 180, 0));
	}
	public static function eastRotation() : self{
		return new self(new Vector3(0, -90, 0));
	}

	public static function westRotation() : self{
		return new self(new Vector3(0, 90, 0));
	}

	public static function upRotation() : self{
		return new self(new Vector3(90,0,0));
	}

	public static function downRotation() : self{
		return new self(new Vector3(-90,0,0));
	}

	public function toNbt() : CompoundTag
	{
		return CompoundTag::create()->setTag("minecraft:transformation",
		CompoundTag::create()
			->setInt("RX", intdiv((int) $this->getRotation()->getX(), 90))
			->setInt("RY",  intdiv((int) $this->getRotation()->getY(), 90))
			->setInt("RZ", intdiv((int) $this->getRotation()->getZ(), 90))
			->setFloat("SX", $this->getScale()->getX() / 90)
			->setFloat("SY", $this->getScale()->getY() / 90)
			->setFloat("SZ", $this->getScale()->getZ() / 90)
			->setFloat("TX", $this->getTranslation()->getX() / 90)
			->setFloat("TY", $this->getTranslation()->getY() / 90)
			->setFloat("TZ", $this->getTranslation()->getZ() / 90)
		);
	}
}
