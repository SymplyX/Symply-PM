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

namespace symply\behavior\blocks\property;

use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use function array_map;
use function sort;
use const SORT_NUMERIC;

final class RotationProperty extends BlockProperty
{
	/**
	 * @param int[] $rotation
	 */
	public function __construct(array $rotation = [])
	{
		sort($rotation, SORT_NUMERIC);
		parent::__construct("symply:rotation", new ListTag(array_map(fn(int $number) => new IntTag($number), $rotation), NBT::TAG_Int));
	}
}
