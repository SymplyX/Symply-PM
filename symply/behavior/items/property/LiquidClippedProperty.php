<?php

namespace symply\behavior\items\property;

use pocketmine\nbt\tag\ByteTag;

class LiquidClippedProperty extends ItemProperty
{
	public function __construct(bool $value = false)
	{
		parent::__construct("liquid_clipped", new ByteTag($value ? 1 : 0));
	}
}