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
use symply\behavior\block\component\sub\HitBoxSubComponent;
use symply\behavior\block\component\sub\MaterialSubComponent;

class ModelComponent implements IComponent
{
	/**
	 * @param MaterialSubComponent[] $materials
	 * @return void
	 */
	public function __construct(
		protected readonly array              $materials,
		protected readonly ?string            $geometry = null,
		protected ?HitBoxSubComponent $collisionBox = null,
		protected ?HitBoxSubComponent $selectionBox = null,
	)
	{
		$this->collisionBox ??= new HitBoxSubComponent();
		$this->selectionBox ??= new HitBoxSubComponent();
	}

	public function toNbt() : CompoundTag
	{
		$compoundTag = CompoundTag::create();
		$materials = CompoundTag::create();
		foreach ($this->materials as $material){
			$materials->setTag($material->getTarget()->value, $material->toNBT());
		}
		$compoundTag->setTag("minecraft:material_instances",
			CompoundTag::create()
				->setTag("mappings", CompoundTag::create())
				->setTag("materials", $materials));
		if (empty($this->geometry)){
			$compoundTag->setTag("minecraft:unit_cube", CompoundTag::create());
			return $compoundTag;
		}
		$compoundTag->setTag("minecraft:geometry", CompoundTag::create()->setString("identifier", $this->geometry));
		$compoundTag->setTag("minecraft:collision_box", $this->collisionBox->toNbt());
		$compoundTag->setTag("minecraft:selection_box", $this->selectionBox->toNbt());
		return $compoundTag;
	}

}
