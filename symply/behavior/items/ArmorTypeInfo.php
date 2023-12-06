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

namespace symply\behavior\items;

use pocketmine\item\ArmorMaterial;
use pocketmine\item\VanillaArmorMaterials;

class ArmorTypeInfo
{
	private ArmorMaterial $material;

	public function __construct(
		private readonly int  $defensePoints,
		private readonly int  $maxDurability,
		private readonly int  $armorSlot,
		private readonly int  $toughness = 0,
		private readonly bool $fireProof = false,
		?ArmorMaterial        $material = null
	){
		$this->material = $material ?? VanillaArmorMaterials::LEATHER();
	}

	public function getDefensePoints() : int{
		return $this->defensePoints;
	}

	public function getMaxDurability() : int{
		return $this->maxDurability;
	}

	public function getArmorSlot() : int{
		return $this->armorSlot;
	}

	public function getToughness() : int{
		return $this->toughness;
	}

	public function isFireProof() : bool{
		return $this->fireProof;
	}

	public function getMaterial() : ArmorMaterial{
		return $this->material;
	}
}
