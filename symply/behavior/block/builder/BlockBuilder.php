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

namespace symply\behavior\block\builder;

use Generator;
use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\convert\BlockStateDictionaryEntry;
use symply\behavior\block\component\CollisionBoxComponent;
use symply\behavior\block\component\GeometryComponent;
use symply\behavior\block\component\MaterialInstancesComponent;
use symply\behavior\block\component\SelectionBoxComponent;
use symply\behavior\block\component\sub\HitBoxSubComponent;
use symply\behavior\block\component\sub\MaterialSubComponent;
use symply\behavior\block\component\TransformationComponent;
use symply\behavior\block\component\UnitCubeComponent;
use symply\behavior\block\IBlockCustom;
use symply\behavior\block\info\BlockCreativeInfo;
use symply\behavior\common\component\IComponent;
use function array_map;

class BlockBuilder
{

	/** @var IComponent[] */
	private array $components = [];
	protected Block&IBlockCustom $blockCustom;

	public function __construct()
	{
	}

	public static function create() : static{
		return new static();
	}

	public function setBlock(Block&IBlockCustom $blockCustom) : static{
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
	private BlockCreativeInfo $creativeInfo;

	public function getCreativeInfo() : BlockCreativeInfo
	{
		return $this->creativeInfo;
	}

	public function setCreativeInfo(BlockCreativeInfo $creativeInfo) : static
	{
		$this->creativeInfo = $creativeInfo;
		return $this;
	}

	public function getComponents() : array
	{
		return $this->components;
	}

	public function addComponent(IComponent $component) : static
	{
		if ($component instanceof GeometryComponent && isset($this->components['minecraft:unit_cube'])){
			unset($this->components['minecraft:unit_cube']);
		}
		$this->components[$component->getName()] = $component;
		return $this;
	}

	public function setGeometry(string $identifier) : static{
		return $this->addComponent(new GeometryComponent($identifier));
	}

	public function setUnitCube() : static{
		return $this->addComponent(new UnitCubeComponent());
	}

	/**
	 * @param MaterialSubComponent[] $materials
	 * @return BlockBuilder
	 */
	public function setMaterialInstance(array $materials = []) : static{
		return $this->addComponent(new MaterialInstancesComponent($materials));
	}

	public function setTransformationComponent(?Vector3 $rotation = null, ?Vector3 $scale = null, ?Vector3 $translation = null) : static{
		return $this->addComponent(new TransformationComponent($rotation ?? Vector3::zero(), $scale ?? Vector3::zero(), $translation ?? Vector3::zero()));
	}

	public function setCollisionBox(Vector3 $origin, Vector3 $size, bool $enable = true) : static
	{
		return $this->addComponent(new CollisionBoxComponent(new HitBoxSubComponent($enable, $origin, $size)));
	}

	public function setSelectionBox(Vector3 $origin, Vector3 $size, bool $enable = true) : static
	{
		return $this->addComponent(new SelectionBoxComponent(new HitBoxSubComponent($enable, $origin, $size)));
	}

	public function setComponents(array $components) : static
	{
		$this->components = $components;
		return $this;
	}

	public function toPacket() : CompoundTag
	{
		return $this->getPropertiesTag()->setTag('components', $this->getComponentsTag())->setInt("molangVersion", 6);
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
		$componentsTags->setTag("blockTags", new ListTag(array_map(fn(string $tag) => new StringTag($tag), $this->blockCustom->getTypeTags())));
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
