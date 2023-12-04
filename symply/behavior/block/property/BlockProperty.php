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

namespace symply\behavior\block\property;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use symply\behavior\block\permutation\BlockPermutation;
use symply\utils\Utils;
use function array_map;
use function sort;
use const SORT_NATURAL;

abstract class BlockProperty
{

	public function __construct(private readonly string $name, protected array $values = []) { }

	/**
	 * Returns the name of the block property provided in the constructor.
	 */
	public function getName() : string {
		return $this->name;
	}

	public function sortValue() : void{
		$values = $this->values;
		sort($values, SORT_NATURAL);
		$this->values = $values;
	}

	/**
	 * Returns the array of possible values of the block property provided in the constructor.
	 */
	public function getValues() : array {
		return $this->values;
	}

	public function setValues(array $values, bool $sort = false) : void{
		$this->values = $values;
		if ($sort)
			$this->sortValue();
	}

	/**
	 * Returns the block property in the correct NBT format supported by the client.
	 */
	public function toNBT() : CompoundTag {
		$values = array_map(static fn($value) => Utils::getTagType($value), $this->values);
		return CompoundTag::create()
			->setString("name", $this->name)
			->setTag("enum", new ListTag($values));
	}

	/**
	 * @return BlockPermutation[]
	 */
	abstract public function getPermutations() : array;
}
