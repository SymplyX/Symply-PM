<?php

namespace symply\behavior\block;

use pocketmine\data\bedrock\block\convert\BlockStateReader;
use pocketmine\data\bedrock\block\convert\BlockStateWriter;
use symply\behavior\block\builder\BlockPermutationBuilder;

interface IPermutationBlock extends IBlockCustom
{

	public function deserializeState(BlockStateReader $reader) : void;
	public function serializeState(BlockStateWriter $writer) : void;

	public function getBlockBuilder() : BlockPermutationBuilder;
}