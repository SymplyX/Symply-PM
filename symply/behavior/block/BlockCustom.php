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

use pocketmine\block\Block;
use pocketmine\block\BlockTypeInfo;
use symply\behavior\block\builder\BlockBuilder;
use function assert;

abstract class BlockCustom extends Block
{

	public function __construct(
		BlockCustomIdentifier $idInfo,
		string                $name,
		BlockTypeInfo         $typeInfo
	)
	{
		parent::__construct($idInfo, $name, $typeInfo);
	}

	public function getIdInfo() : BlockCustomIdentifier
	{
		$idInfo = parent::getIdInfo();
		assert($idInfo instanceof BlockCustomIdentifier);
		return $idInfo;
	}
	public function getBlockBuilder() : BlockBuilder{
		return BlockBuilder::create()
			->setBlock($this)
			->setUnitCube();
	}
}
