<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
 */

declare(strict_types=1);

namespace pocketmine\block\tile;

use Exception;
use pocketmine\crafting\CraftingManager;
use pocketmine\crafting\FurnaceRecipeManager;
use pocketmine\crafting\FurnaceType;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\world\World;

class Campfire extends Spawnable{

	public const MAX_ITEMS = 4;

	public const TAG_COOK_TIME = "CookTimes";
	public const TAG_ITEM_COOK = "ItemCook";
	public const TAG_ITEM_TIME = "ItemTime";

	/** @var Item[] */
	private array $items = [];
	/** @var int[] */
	private array $itemTime = [];

	public function __construct(World $world, Vector3 $pos){
		parent::__construct($world, $pos);
	}

	public function close() : void{
		foreach($this->items as $item){
			$this->position->getWorld()->dropItem($this->position->add(0, 1, 0), $item);
		}
		$this->items = [];
		parent::close();
	}

	public function getFurnaceType() : FurnaceType{
		return FurnaceType::CAMPFIRE;
	}

	public function getFurnaceRecipeManager() : FurnaceRecipeManager{
		$craftingManager = new CraftingManager();
		return $craftingManager->getFurnaceRecipeManager($this->getFurnaceType());
	}

	public function setItem(Item $item, ?int $slot = null) : void{
		if($slot === null){
			$slot = count($this->items) + 1;
		}
		if($slot < 1 || $slot > self::MAX_ITEMS){
			throw new Exception("Slot must be range 0-4, got" . $slot);
		}
		if($item->isNull()){
			if(isset($this->items[$slot])) unset($this->items[$slot]);
		}else{
			$this->items[$slot] = $item;
		}
	}

	public function addItem(Item $item) : bool{
		$item->setCount(1);
		if(!$this->canCook($item)){
			return false;
		}
		$this->setItem($item);
		return true;
	}

	public function canCook(Item $item) : bool{
		$furnaceRepcipeManager = $this->getFurnaceRecipeManager();
		if($furnaceRepcipeManager->match($item) === null){
			return false;
		}
		return true;
	}

	public function canAddItem(Item $item) : bool{
		if(count($this->items) >= self::MAX_ITEMS){
			return false;
		}
		return $this->canCook($item);
	}

	public function setSlotTime(int $slot, int $time) : void{
		$this->itemTime[$slot] = $time;
	}

	public function increaseSlotTime(int $slot) : void{
		$this->setSlotTime($slot, $this->getItemTime($slot) + 1);
	}

	public function getItemTime(int $slot) : int{
		return $this->itemTime[$slot];
	}

	public function getContents() : array{
		return $this->items;
	}

	public function readSaveData(CompoundTag $nbt) : void{
		if(($tag = $nbt->getTag(Container::TAG_ITEMS)) !== null){
			$inventoryTag = $tag->getValue();
			
			/** @var CompoundTag $itemNBT */
			foreach($inventoryTag as $itemNBT){
				$this->setItem(Item::nbtDeserialize($itemNBT), $itemNBT->getByte("Slot"));
			}
		}

		if(($tag = $nbt->getTag(self::TAG_COOK_TIME)) !== null){

			/** @var IntTag $time */
			foreach($tag->getValue() as $slot => $time){
				$this->itemTime[$slot + 1] = $time->getValue();
			}
		}
	}

	protected function writeSaveData(CompoundTag $nbt) : void{
		$items = [];
		/** @var Item $item */
		foreach($this->getContents() as $slot => $item){
			$items[] = $item->nbtSerialize($slot);
		}
		$nbt->setTag(Container::TAG_ITEMS, new ListTag($items, NBT::TAG_Compound));

		$times = [];
		foreach($this->itemTime as $time){
			$times[] = new IntTag($time);
		}
		$nbt->setTag(self::TAG_COOK_TIME, new ListTag($times));
	}

	protected function addAdditionalSpawnData(CompoundTag $nbt) : void{
		foreach($this->items as $slot => $item){
			$nbt->setTag(self::TAG_ITEM_COOK . $slot, $item->nbtSerialize());
			$nbt->setInt(self::TAG_ITEM_TIME . $slot, $this->getItemTime($slot));
		}
	}
}
