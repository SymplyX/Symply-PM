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

use symply\behavior\block\component\TransformationComponent;
use symply\behavior\block\permutation\BlockPermutation;
use function array_key_exists;
use function array_keys;
use function sort;
use const SORT_NATURAL;

class RotationProperty extends BlockProperty
{
	/**
	 * @param array<int, TransformationComponent> $rotation
	 */
	public function __construct(protected array $rotation = [])
	{
		$values = array_keys($this->rotation);
		sort($values, SORT_NATURAL);
		parent::__construct("symply:rotation", $values);
	}

	public function addRotation(int $meta, TransformationComponent $behavior) : void
	{
		$this->rotation[$meta] = $behavior;
		if (!array_key_exists($meta, $this->values)){
			$this->values[] = $meta;
			$this->sortValue();
		}
	}

	public function setRotation(array $rotation) : void{
		$this->rotation = $rotation;
		$this->setValues(array_keys($this->rotation), true);
	}

	public function getPermutations() : array
	{
		$listBlockPermutation = [];
		foreach ($this->rotation as $meta => $behavior){
			$listBlockPermutation[] = new BlockPermutation("q.block_property('symply:rotation') == $meta", $behavior->toNbt());
		}
		return $listBlockPermutation;
	}
}
