<?php

namespace symply\behavior\block;

use pocketmine\block\BlockTypeInfo;
use pocketmine\block\Opaque as PMOpaque;
use symply\behavior\block\builder\BlockBuilder;

class Opaque extends PMOpaque implements IBlockCustom
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