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

namespace symply\behavior\items\component;

use pocketmine\nbt\tag\CompoundTag;
use symply\behavior\common\component\IComponent;
use symply\behavior\items\enum\TextureTypeEnum;

class ArmorComponent implements IComponent
{

	public function __construct(private readonly int $protection, private readonly TextureTypeEnum $textureType = TextureTypeEnum::NONE)
	{
	}

	public function getProtection() : int
	{
		return $this->protection;
	}

	public function getTextureType() : TextureTypeEnum
	{
		return $this->textureType;
	}

	public function toNbt() : CompoundTag
	{
		return CompoundTag::create()->setTag("minecraft:armor", CompoundTag::create()
			->setString("texture_type", $this->getTextureType()->value)
		->setInt("protection", $this->getProtection()));
	}

	public function getName() : string
	{
		return "minecraft:armor";
	}
}
