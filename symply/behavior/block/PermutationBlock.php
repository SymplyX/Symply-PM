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

use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;
use symply\behavior\block\builder\BlockBuilderPermutation;
use symply\behavior\block\permutation\BlockPermutation;
use symply\behavior\block\property\BlockProperty;

abstract class PermutationBlock extends BlockCustom
{
	/** @var BlockProperty[] */
	private array $properties = [];

	/** @var BlockPermutation[] */
	private array $permutations = [];

	abstract public function deserializeState(BlockStateReader $reader) : void;
	abstract public function serializeState(BlockStateWriter $writer) : void;

	public function getBlockBuilder() : BlockBuilderPermutation{
		return BlockBuilderPermutation::create()
			->setBlock($this)
			->setUnitCube();
	}

}
