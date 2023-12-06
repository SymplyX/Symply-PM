<?php

namespace symply\behavior\block;

use pocketmine\block\BlockTypeInfo;
use pocketmine\block\Flowable as PMFlowable;
use symply\behavior\block\builder\BlockPermutationBuilder;

abstract class FlowablePermutation extends PMFlowable implements IPermutationBlock
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

	public function getBlockBuilder(): BlockPermutationBuilder
	{
		return BlockPermutationBuilder::create()
			->setBlock($this)
			->setUnitCube();
	}
}