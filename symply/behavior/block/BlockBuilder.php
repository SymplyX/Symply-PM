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

namespace symply\behavior\block;

use Generator;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\convert\BlockStateDictionaryEntry;
use symply\behavior\block\component\ModelComponent;
use symply\behavior\block\component\sub\HitBoxSubComponent;
use symply\behavior\block\component\sub\MaterialSubComponent;
use symply\behavior\block\component\TransformationComponent;
use symply\behavior\block\info\CreativeInfo;
use symply\behavior\common\component\IComponent;

class BlockBuilder
{

	/** @var IComponent[] */
	private array $components = [];
	protected BlockCustom $blockCustom;

	public function __construct()
	{
	}

	public static function create() : self{
		return new self();
	}

	public function setBlock(BlockCustom $blockCustom) : self{
		$this->blockCustom = $blockCustom;
		return $this;
	}

	public function getNamespaceId() : string
	{
		return $this->blockCustom->getIdInfo()->getNamespaceId();
	}

	public function getOldId() : int
	{
		return $this->blockCustom->getIdInfo()->getOldId();
	}
	private CreativeInfo $creativeInfo;

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

	public function addComponent(IComponent $component) : self
	{
		$this->components[] = $component;
		return $this;
	}

	/**
	 * @param MaterialSubComponent[] $materials
	 * @return $this
	 */
	public function setModalComponent(array $materials, ?string $geometry = null, ?HitBoxSubComponent $collisionBox = null, ?HitBoxSubComponent $selectionBox = null) : self
	{
		return $this->addComponent(new ModelComponent($materials, $geometry, $collisionBox, $selectionBox));
	}

	public function setTransformationComponent(?Vector3 $rotation = null, ?Vector3 $scale = null, ?Vector3 $translation = null) : self{
		return $this->addComponent(new TransformationComponent($rotation ?? Vector3::zero(), $scale ?? Vector3::zero(), $translation ?? Vector3::zero()));
	}

	public function setComponents(array $components) : self
	{
		$this->components = $components;
		return $this;
	}

	public function toPacket() : CompoundTag
	{
		return $this->getPropertiesTag()->setTag('components', $this->getComponentsTag())->setInt("molangVersion", 1);
	}

	public function getPropertiesTag() : CompoundTag
	{
		$property = CompoundTag::create();
		return $property->merge($this->getCreativeInfo()->toNbt());
	}

	public function getComponentsTag() : CompoundTag
	{
		$componentsTags = CompoundTag::create()
			->setTag("minecraft:light_emission", CompoundTag::create()
				->setByte("emission", $this->blockCustom->getLightLevel()))
			->setTag("minecraft:light_dampening", CompoundTag::create()
				->setByte("lightLevel", $this->blockCustom->getLightFilter()))
			->setTag("minecraft:destructible_by_mining", CompoundTag::create()
				->setFloat("value", $this->blockCustom->getBreakInfo()->getHardness()))
			->setTag("minecraft:friction", CompoundTag::create()
				->setFloat("value", 1 - $this->blockCustom->getFrictionFactor()));
		foreach ($this->components as $component) {
			$componentsTags = $componentsTags->merge($component->toNbt());
		}
		foreach ($this->blockCustom->getTypeTags() as $tag){
			$componentsTags->setTag("tag:$tag", CompoundTag::create());
		}
		return $componentsTags;
	}

	/**
	 * @return Generator<BlockStateDictionaryEntry>
	 */
	public function toBlockStateDictionaryEntry() : Generator
	{
		yield new BlockStateDictionaryEntry($this->getNamespaceId(), [], 0);
	}
}
