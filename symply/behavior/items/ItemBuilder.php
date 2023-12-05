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

use pocketmine\nbt\tag\CompoundTag;
use symply\behavior\common\component\IComponent;
use symply\behavior\items\enum\AnimationEnum;
use symply\behavior\items\info\CreativeInfo;
use symply\behavior\items\property\AllowOffHandProperty;
use symply\behavior\items\property\FoilProperty;
use symply\behavior\items\property\HandEquippedProperty;
use symply\behavior\items\property\IconProperty;
use symply\behavior\items\property\ItemProperty;
use symply\behavior\items\property\UseAnimationProperty;
use symply\behavior\items\property\UseDurationProperty;
use function in_array;

class ItemBuilder
{

	private ItemCustom $item;

	private function __construct()
	{
	}

	public static function create() : self
	{
		return new self();
	}

	/** @var IComponent[] */
	private array $components = [];

	/** @var ItemProperty[] */
	private array $properties = [];

	private CreativeInfo $creativeInfo;

	public function setItem(ItemCustom $itemCustom) : self{
		$this->item = $itemCustom;
		return $this;
	}

	public function getCreativeInfo() : CreativeInfo
	{
		return $this->creativeInfo;
	}

	public function setCreativeInfo(CreativeInfo $creativeInfo) : self
	{
		$this->creativeInfo = $creativeInfo;
		return $this;
	}

	public function getComponents() : array
	{
		return $this->components;
	}

	/**
	 * @param IComponent[] $components
	 */
	public function setComponents(array $components) : self
	{
		$this->components = $components;
		return $this;
	}

	/**
	 * @return ItemProperty[]
	 */
	public function getProperties() : array
	{
		return $this->properties;
	}

	public function addProperties(ItemProperty $properties) : self
	{
		if (in_array($properties, $this->properties, true))
			return $this;
		$this->properties[] = $properties;
		return $this;
	}

	/**
	 * @param ItemProperty[] $properties
	 */
	public function setProperties(array $properties) : self
	{
		$this->properties = $properties;
		return $this;
	}

	public function setAllowOffHand(bool $value = false) : self
	{
		return $this->addProperties(new AllowOffHandProperty($value));
	}

	public function setHandEquipped(bool $value = false) : self
	{
		return $this->addProperties(new HandEquippedProperty($value));
	}

	public function setIcon(string $texture) : self
	{
		return $this->addProperties(new IconProperty($texture));
	}

	public function setAnimation(AnimationEnum $animation) : self
	{
		return $this->addProperties(new UseAnimationProperty($animation));
	}

	public function setUseDuration(int $value) : self
	{
		return $this->addProperties(new UseDurationProperty($value));
	}

	public function setEffectFoil(bool $value = true) : self{
		return $this->addProperties(new FoilProperty($value));
	}

	public function toPacket() : CompoundTag
	{
		return CompoundTag::create()
			->setTag("components", $this->getComponentsTag()
				->setTag("item_properties", $this->getPropertiesTag()))
			->setInt("id", $this->item->getIdentifier()->getOldId())
			->setString("name", $this->item->getIdentifier()->getNamespaceId());
	}

	public function getComponentsTag() : CompoundTag
	{
		$componentsTag = CompoundTag::create();
		foreach ($this->components as $property) {
			$componentsTag = $componentsTag->merge($property->toNBT());
		}
		return $componentsTag;
	}

	public function getPropertiesTag() : CompoundTag
	{
		$propertiesTag = CompoundTag::create();
		foreach ($this->properties as $property) {
			$propertiesTag = $propertiesTag->merge($property->toNBT());
		}
		return $propertiesTag->merge($this->creativeInfo->toNbt());
	}
}
