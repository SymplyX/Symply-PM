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

namespace symply\behavior\block\permutation;

use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use symply\behavior\block\builder\BlockBuilder;
use symply\behavior\block\component\CollisionBoxComponent;
use symply\behavior\block\component\GeometryComponent;
use symply\behavior\block\component\MaterialInstancesComponent;
use symply\behavior\block\component\SelectionBoxComponent;
use symply\behavior\block\component\sub\HitBoxSubComponent;
use symply\behavior\block\component\sub\MaterialSubComponent;
use symply\behavior\block\component\TransformationComponent;
use symply\behavior\block\component\UnitCubeComponent;
use symply\behavior\common\component\IComponent;

final class BlockPermutation
{
	private string $condition;

	private array $components = [];
	public function __construct() {
	}

	public static function create() : BlockPermutation
	{
		return new BlockPermutation();
	}

	public function getCondition() : string
	{
		return $this->condition;
	}

	public function setCondition(string $condition) : BlockPermutation
	{
		$this->condition = $condition;
		return $this;
	}

	/**
	 * @return IComponent[]
	 */
	public function getComponents() : array
	{
		return $this->components;
	}

	public function addComponent(IComponent $component) : BlockPermutation
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

	public function setCollisionBox(Vector3 $origin, Vector3 $size, bool $enable = true): static
	{
		return $this->addComponent(new CollisionBoxComponent(new HitBoxSubComponent($enable, $origin, $size)));
	}

	public function setSelectionBox(Vector3 $origin, Vector3 $size, bool $enable = true): static
	{
		return $this->addComponent(new SelectionBoxComponent(new HitBoxSubComponent($enable, $origin, $size)));
	}

	/**
	 * Returns the permutation in the correct NBT format supported by the client.
	 */
	public function toNBT() : CompoundTag {
		$componentsTags = CompoundTag::create();

		foreach ($this->getComponents() as $component){
			$componentsTags = $componentsTags->merge($component->toNbt());
		}
		return CompoundTag::create()
			->setString("condition", $this->getCondition())
			->setTag("components", $componentsTags);
	}
}
