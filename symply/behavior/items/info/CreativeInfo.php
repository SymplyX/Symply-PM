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

namespace symply\behavior\items\info;

use pocketmine\nbt\tag\CompoundTag;
use symply\behavior\block\info\CreativeInfo as BlockCreativeInfo;
class CreativeInfo extends BlockCreativeInfo
{
	public function toNbt() : CompoundTag
	{
		return CompoundTag::create()
			->setString("creative_category", $this->getCategory()->toItemCategory() ?? "")
			->setString("creative_group", $this->getGroup()->value ?? "");
	}
}
