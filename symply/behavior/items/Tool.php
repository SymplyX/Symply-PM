<?php

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

	public function getItemBuilder(): ItemBuilder
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