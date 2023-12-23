<?php

namespace symply\behavior\items\property;

use pocketmine\nbt\tag\FloatTag;

class MiningSpeed extends ItemProperty
{

	public function __construct(float $mining_speed)
	{
		parent::__construct("mining_speed", new FloatTag($mining_speed));
	}

}