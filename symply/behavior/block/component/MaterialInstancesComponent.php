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

use pocketmine\nbt\tag\CompoundTag;
use symply\behavior\block\component\sub\MaterialSubComponent;
use symply\behavior\common\component\IComponent;

class MaterialInstancesComponent implements IComponent
{
	/**
	 * @param MaterialSubComponent[] $materials
	 * @return void
	 */
	public function __construct(
		protected readonly array $materials
	)
	{
	}

	public function getName() : string
	{
		return "minecraft:material_instances";
	}

	public function toNbt() : CompoundTag
	{

		$materials = CompoundTag::create();
		if (!empty($this->materials)) {
			foreach ($this->materials as $material) {
				$materials->setTag($material->getTarget()->value, $material->toNBT());
			}
		}
		return CompoundTag::create()->setTag($this->getName(),
			CompoundTag::create()
				->setTag("mappings", CompoundTag::create())
				->setTag("materials", $materials));
	}
}
