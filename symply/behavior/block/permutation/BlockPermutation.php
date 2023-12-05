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

use pocketmine\nbt\tag\CompoundTag;
use symply\behavior\common\component\IComponent;
use function in_array;

final class BlockPermutation
{
	private string $condition;

	private array $components = [];
	public function __construct() {
	}

	public static function create() : self{
		return new self();
	}

	public function getCondition() : string
	{
		return $this->condition;
	}

	public function setCondition(string $condition) : self
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

	public function addComponents(IComponent $component) : static{
		if (in_array($component, $this->components, true)){
			return $this;
		}
		$this->components[] = $component;
		return $this;
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
