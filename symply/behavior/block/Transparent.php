<?php

namespace symply\behavior\block;

use pocketmine\block\BlockTypeInfo;
use pocketmine\block\Transparent as PMTransparent;
use symply\behavior\block\builder\BlockBuilder;

class Transparent extends PMTransparent implements IBlockCustom
{


	public function __construct(
		BlockIdentifier $idInfo,
		string          $name,
		BlockTypeInfo   $typeInfo
	)
	{
		parent::__construct($idInfo, $name, $typeInfo);
	}

	public function getIdInfo() : BlockIdentifier
	{
		$idInfo = parent::getIdInfo();
		assert($idInfo instanceof BlockIdentifier);
		return $idInfo;
	}

	public function getBlockBuilder(): BlockBuilder
	{
		return BlockBuilder::create()
			->setBlock($this)
			->setUnitCube();
	}
}