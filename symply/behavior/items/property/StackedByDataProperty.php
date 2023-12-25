<?php

namespace symply\behavior\items\property;

use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\Tag;

class StackedByDataProperty extends ItemProperty
{
	public function __construct(bool $value = false)
	{
		parent::__construct("stacked_by_data", new ByteTag($value ? 1 : 0));
	}
}