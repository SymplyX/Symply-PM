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

use pocketmine\block\BlockToolType;
use pocketmine\item\enchantment\VanillaEnchantments;
use symply\behavior\common\enum\CategoryCreativeEnum;
use symply\behavior\common\enum\GroupCreativeEnum;
use symply\behavior\items\builder\ItemBuilder;
use symply\behavior\items\info\ItemCreativeInfo;
use symply\behavior\items\property\DamageProperty;

abstract class Tool extends Durable
{

	public function getMaxStackSize() : int{
		return 1;
	}

	public function getMiningEfficiency(bool $isCorrectTool) : float{
		$efficiency = 1;
		if($isCorrectTool){
			$efficiency = $this->getBaseMiningEfficiency();
			if(($enchantmentLevel = $this->getEnchantmentLevel(VanillaEnchantments::EFFICIENCY())) > 0){
				$efficiency += ($enchantmentLevel ** 2 + 1);
			}
		}

		return $efficiency;
	}

	protected function getBaseMiningEfficiency() : float{
		return 1;
	}

	public function getItemBuilder() : ItemBuilder
	{
		return parent::getItemBuilder()
			->addProperties(new DamageProperty($this->getAttackPoints()))
			->setCreativeInfo(new ItemCreativeInfo(CategoryCreativeEnum::EQUIPMENT, match ($this->getBlockToolType()){
				BlockToolType::AXE => GroupCreativeEnum::AXE,
				BlockToolType::HOE => GroupCreativeEnum::HOE,
				BlockToolType::SWORD => GroupCreativeEnum::SWORD,
				BlockToolType::PICKAXE => GroupCreativeEnum::PICKAXE,
				BlockToolType::SHOVEL => GroupCreativeEnum::SHOVEL,
				default => GroupCreativeEnum::NONE
			}));
	}
}
